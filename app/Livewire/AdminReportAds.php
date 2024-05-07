<?php

namespace App\Livewire;

use Livewire\WithPagination;
use App\Models\ReportAd;
use Livewire\Component;

class AdminReportAds extends Component
{
    use WithPagination;

    public string $search = "";

    public $reportsExists;

    public function render()
    {
        return view('livewire.admin-report-ads', [
            'reports' => ReportAd::orWhere('reason', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%')
                            ->orWhere('message', 'like', '%'.$this->search.'%')
                            ->orWhere('created_at', 'like', '%'.$this->search.'%')
                            ->paginate(20),
        ]);
    }

    public function mount(){

        $this->reportsExists = ReportAd::exists();
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
