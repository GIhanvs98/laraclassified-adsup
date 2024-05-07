<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Validation\Rule;
use App\Models\ReportAd;
use Livewire\Component;

class ReportAds extends Component
{
    public $post_id;
    public string $reason;
    public string $email;
    public string $message;
    public string $success;
    public string $error;

    public function render()
    {
        return view('livewire.report-ads');
    }

    public function mount($postId){
        $this->post_id = $postId;
        $this->reason = "";
        $this->success = "";
    }

    public function send(){

        $validated = $this->validate([ 
            'post_id' => ['required', 'exists:posts,id'],
            'reason' => ['required', Rule::in(['sold-out-or-unavailable', 'fraud', 'duplicate', 'spam', 'wrong-category', 'offensive', 'other'])],
            'email' => ['required', 'email:rfc,dns'],
            'message' => ['required', 'min:3', 'max:500'],
        ]);

        try {
            
            $post = Post::find($this->post_id);

            $report_ad = new ReportAd;
            $report_ad->reason = $this->reason;
            $report_ad->email = $this->email;
            $report_ad->message = $this->message;
            $report_ad->post()->associate($post);
            $report_ad->save();
            
            $this->success = true;

        } catch (\Throwable $th) {

            $this->success = false;
        }
        
    }

    public function resetAds(){

        $this->reason = "";
        $this->email = "";
        $this->message = "";
        $this->success = "";
    }
}
