<?php

namespace App\Livewire;

use App\Models\Shop;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class AdminShops extends Component
{
    use WithPagination;

    public string $search = "";

    public array $title = [];

    protected $shopsList;

    public function render()
    {
        return view('livewire.admin-shops', [
                'shops' => Shop::whereHas('user', function (Builder $query) {
                    $query->verified();
                })
                ->where(function (Builder $query) {
                    $query->orWhere('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                })->paginate(20),
            'shopsExists' => Shop::whereHas('user', function (Builder $query) {
                    $query->verified();
                })->exists(),
        ]);
    }

    public function mount()
    {
        $this->shopsList = Shop::whereHas('user', function (Builder $query) {
                                $query->verified();
                            })->get();

        foreach ($this->shopsList as $key => $shop) {
            $this->title[$shop->id]['value'] = $shop->title ?? null;
            $this->title[$shop->id]['disabled'] = true;
        }
    }

    public function searchClear()
    {
        $this->search = "";
    }

    public function edit(string $id = null)
    {
        if (isset($this->title[$id])) {

            $this->title[$id]['disabled'] = false;
        }
    }

    public function save(string $id = null)
    {
        $validated = $this->validate([ 
            'title.'.$id.'.value' => 'required|min:3|max:150',
        ]);
 
        if (isset($this->title[$id])) {

            $shop = Shop::find($id);

            if ($shop) {

                $shop->title = $validated['title'][$id]['value'] ?? $shop->title;

                $shop->save();
            }

            $this->title[$id]['disabled'] = true;
        }
    }
}
