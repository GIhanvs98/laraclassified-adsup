<?php

namespace App\Livewire;

use App\Models\Membership;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;

class AccountMembership extends Component
{
    public User $user;

    public $membershipDoesntExist;

    public $transactionsValidDoesntExist;

    public array $giveawayPackages;

    public function render()
    {

		$totalUsedPackageCount = Transaction::valid('ad-promotion')->where('user_id', auth()->user()->id)->giveawayTransactions()->count();
		
		$totalPackagesCount = array_sum(array_map(function($package) {
			return $package['count'];
		}, array_filter($this->giveawayPackages, function($package) {
			return !is_null($package['count']);
		})));

        return view('livewire.account-membership', [
            'totalUsedPackageCount' => $totalUsedPackageCount,
            'totalPackagesCount' => $totalPackagesCount,
        ]);
    }

    public function mount(){

		// Account panel. -> Not allowed for non members.
		 
        $this->user =  User::has('membership')->with('membership')->find(auth()->user()->id);

		// Validate membership not a free membership.
		$this->membershipDoesntExist = Membership::where('id', $this->user->membership->id)->member()->doesntExist();

		// Validate transactions. If the user has paid for the given time, then it is allowed. If he hasent payed for the current time.
		$this->transactionsValidDoesntExist = Transaction::valid('membership')->where('user_id', $this->user->id)->doesntExist();
		
		$this->giveawayPackages = json_decode($this->user->membership->giveaway_packages, true);
    }

    public function remove(Transaction $transaction){
        $transaction->delete();
    }
}
