<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class AdminMembershipDurations extends Component
{
    use WithPagination;

    public string $search = "";

    public array $datetime = [];

    protected $usersList;

    public function render()
    {
        return view('livewire.admin-membership-durations', [
                'users' => User::withWhereHas('transactions', function ($query) {
                    $query->successfull('membership');
                })
                ->withWhereHas('membership', function ($query) {
                    $query->whereNot(function (Builder $query) {
                        $query->whereId(1)->orWhere('name', 'Non Member');
                    });
                })
                ->where(function (Builder $query) {
                    $query->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_alternate_1', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_alternate_2', 'like', '%' . $this->search . '%')
                        ->orWhere('whatsapp_number', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_national', 'like', '%' . $this->search . '%');
                })->paginate(20),
            'usersExists' => User::withWhereHas('transactions', function ($query) {
                    $query->successfull('membership');
                })
                ->withWhereHas('membership', function ($query) {
                    $query->whereNot(function (Builder $query) {
                        $query->whereId(1)->orWhere('name', 'Non Member');
                    });
                })->exists(),
        ]);
    }

    public function mount()
    {
        $this->usersList = User::withWhereHas('transactions', function ($query) {
                    $query->successfull('membership');
                })
                ->withWhereHas('membership', function ($query) {
                    $query->whereNot(function (Builder $query) {
                        $query->whereId(1)->orWhere('name', 'Non Member');
                    });
                })
                ->get();

        foreach ($this->usersList as $key => $user) {
            $this->datetime[$user->id]['value'] = $user->transactions()->successfull('membership')->latest('created_at')->first()->payment_valid_untill_datetime ?? null;
            $this->datetime[$user->id]['disabled'] = true;
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

            $transaction = User::find($id)->transactions()->successfull('membership')->latest('created_at')->first();

            if ($transaction && isset($newDatetime)) {

                $endDatetime = Carbon::parse($newDatetime)->settings(['monthOverflow' => false])->addDays(config('subscriptions.memberships.payment_pending_time'));

                $transaction->payment_valid_untill_datetime = $newDatetime;

                $transaction->payment_due_datetime = $endDatetime;

                $transaction->save();
            }

            $this->datetime[$id]['disabled'] = true;
        }
    }
}
