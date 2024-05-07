<?php

namespace App\Livewire;

use App\Models\ReportAd;
use Livewire\Component;
use Livewire\WithPagination;

class AccountReportAds extends Component
{

    use WithPagination;
    
    public $user;
    public string $search = "";

    public function mount($user){

        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.account-report-ads', [
            'reports' => ReportAd::whereRelation('post', 'user_id', $this->user->id)
                            ->orWhere('reason', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%')
                            ->orWhere('message', 'like', '%'.$this->search.'%')
                            ->orWhere('created_at', 'like', '%'.$this->search.'%')
                            ->paginate(20),
        ]);
    }

    public function deleteReport(ReportAd $report){

        if(auth()->user()->id == $report->post->user->id ){
            $report->delete();
        }
    }

    public function searchClear(){
        $this->search = "";
    }
}
