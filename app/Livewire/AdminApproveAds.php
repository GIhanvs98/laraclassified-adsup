<?php

namespace App\Livewire;

use App\Mail\AdReviewResponsed;
use App\Models\PostReviewingViolation;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class AdminApproveAds extends Component
{
    use WithPagination;

    public string $search = "";

    #[Validate('required|min:3|max:5000')] 
    public string $reason = "";

    public Post $selectedAd;

    public function render()
    {
        return view('livewire.admin-approve-ads', [
            'ads' => Post::with(['currency', 'user', 'reviewingViolation' => function ($query) {
                                $query->timeleft();
                            }])
                            ->whereNull('reviewed_at')
                            ->where(function (Builder $query) {
                                $query->orWhere('title', 'like', '%'.$this->search.'%')
                                        ->orWhere('description', 'like', '%'.$this->search.'%')
                                        ->orWhere('price', 'like', '%'.$this->search.'%')
                                        ->orWhere('email', 'like', '%'.$this->search.'%')
                                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                                        ->orWhere('phone_national', 'like', '%'.$this->search.'%');
                            })->paginate(20),
                                
        ]);
    }

    public function searchClear(){
        $this->search = "";
    }

    public function approve(Post $post){

        // $post->is_approved = 1;

        $post->reviewed_at = now();

        if($post->reviewingViolation){

            $post->reviewingViolation->delete();
        }

        $post->save();
    }

    public function create(){

        $this->validate();

        try {
                
            if ($this->selectedAd) {

                PostReviewingViolation::updateOrInsert(
                    ['post_id' => $this->selectedAd->id],
                    [
                        'reason' => $this->reason, 
                        'last_datetime' => Carbon::now()->settings(['monthOverflow' => false])->addDays(config('subscriptions.ad_reviewing_duration')),
                        'rechecked_datetime' => null,
                    ]
                );

                Mail::to($this->selectedAd->email)->send(new AdReviewResponsed($this->selectedAd, $this->reason));
            }

            $this->dispatch('hide-view-revision');
        } catch (\Throwable $th) {
            $this->addError('reason', 'Unexpected error occured. Refresh the page and try again!');
        }
    }

    public function edit(){
        
        $this->validate();

        try{
    
            if ($this->selectedAd && $this->selectedAd->reviewingViolation) {

                $postReviewingViolation = PostReviewingViolation::find($this->selectedAd->reviewingViolation->id);
                $postReviewingViolation->reason = $this->reason;
                $postReviewingViolation->last_datetime = Carbon::now()->settings(['monthOverflow' => false])->addDays(config('subscriptions.ad_reviewing_duration'));
                $postReviewingViolation->rechecked_datetime = null;
                $postReviewingViolation->save();            
            
                Mail::to($this->selectedAd->email)->send(new AdReviewResponsed($this->selectedAd, $this->reason));
            }         

            $this->dispatch('hide-view-revision');

        }catch(\Throwable $th){
            $this->addError('reason', 'Unexpected error occured. Refresh the page and try again!');
        }
    }

    public function delete(Post $post){

        $this->reason = "";
        
        $this->resetValidation('reason');

        try{

            if ($this->selectedAd && $this->selectedAd->reviewingViolation) {

                PostReviewingViolation::destroy($this->selectedAd->reviewingViolation->id); 
            }

        }catch(\Throwable $th){
            $this->addError('reason', 'Unexpected error occured. Refresh the page and try again!');
        }

        $this->dispatch('hide-view-revision');
    }

    #[On('show-view-revision')] 
    public function showViewRevision(string $type, Post $post)
    {
        $this->reason = "";

        $this->selectedAd = $post;

        if ($type === "edit") {

            $this->reason = $this->selectedAd->reviewingViolation ? $this->selectedAd->reviewingViolation->reason : "";
        }
    }
}