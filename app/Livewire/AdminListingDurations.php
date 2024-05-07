<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class AdminListingDurations extends Component
{
    use WithPagination;

    public string $search = "";

    public array $datetime = [];

    protected $adsList;

    public function render()
    {
        return view('livewire.admin-listing-durations', [
            'ads' => Post::with(['currency', 'user'])
                ->withWhereHas('transactions', function ($query) {
                    $query->successfull('ad-promotion');
                })
                ->verified()
                ->unarchived()
                ->where(function (Builder $query) {
                    $query->orWhere('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('price', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_national', 'like', '%' . $this->search . '%');
                })->paginate(20),
            'adsExists' => Post::withWhereHas('transactions', function ($query) {
                $query->successfull('ad-promotion');
            })
                ->verified()
                ->unarchived()
                ->exists(),
        ]);
    }

    public function mount()
    {
        $this->adsList = Post::with(['currency', 'user'])
            ->withWhereHas('transactions', function ($query) {
                $query->successfull('ad-promotion');
            })
            ->verified()
            ->unarchived()
            ->get();

        foreach ($this->adsList as $key => $ad) {
            $this->datetime[$ad->id]['value'] = $ad->transactions()->successfull('ad-promotion')->latest('created_at')->first()->payment_valid_untill_datetime ?? null;
            $this->datetime[$ad->id]['disabled'] = true;
        }
    }

    public function searchClear()
    {
        $this->search = "";
    }

    public function edit(string $id = null)
    {
        if (isset($this->datetime[$id])) {

            $this->datetime[$id]['disabled'] = false;
        }
    }

    public function save(string $id = null)
    {
        if (isset($this->datetime[$id])) {

            $newDatetime = $this->datetime[$id]['value'] ?? null;

            $transaction = Post::find($id)->transactions()->successfull('ad-promotion')->latest('created_at')->first();

            if ($transaction && isset($newDatetime)) {

                $endDatetime = Carbon::parse($newDatetime)->settings(['monthOverflow' => false])->addDays(config('subscriptions.ad-promotions.payment_pending_time'));

                $transaction->payment_valid_untill_datetime = $newDatetime;

                $transaction->payment_due_datetime = $endDatetime;

                $transaction->save();
            }

            $this->datetime[$id]['disabled'] = true;
        }

    }
}
