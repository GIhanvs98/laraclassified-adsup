<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;

class AdminTransactions extends Component
{
    use WithPagination;

    public string $search = "";

    public $transactionsExists;

    public function render()
    {   
        return view('livewire.admin-transactions', [
            'transactions' => Transaction::
                                orWhere('payment_type', 'like', '%'.$this->search.'%')
                                ->orWhere('payment_status', 'like', '%'.$this->search.'%')
                                ->orWhere('net_amount', 'like', '%'.$this->search.'%')
                                ->orWhere('payment_started_datetime', 'like', '%'.$this->search.'%')
                                ->orWhere('payment_valid_untill_datetime', 'like', '%'.$this->search.'%')
                                ->orWhere('payment_due_datetime', 'like', '%'.$this->search.'%')
                                ->onepayTransactions()
                                ->paginate(20),
        ]);
    }

    public function mount(){

        $this->transactionsExists = Transaction::onepayTransactions()->exists(); 
    }

    public function searchClear(){
        $this->search = "";
    }
}
