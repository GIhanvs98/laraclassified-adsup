<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\SearchKeyword;
use Livewire\Component;
use Livewire\Attributes\On;

class AdminSettingsGeneralSearchKeywordsOld extends Component
{
    public string $search = "";

    public array $category;

    public $keywords;

    public array $modal;

    public function mount()
    {
        $this->modal = [
            'state' => 'edit', # edit/new
            'selected' => null,
        ];

        $this->category = [
            'options' => [],
            'value' => "",
        ];
    }

    public function render()
    {
        return view('livewire.admin-settings-general-search-keywords-old', [
            'searchKeywords' => SearchKeyword::withWhereHas('category')->where('keywords', 'like', '%' . $this->search . '%')->paginate(20),
        ]);
    }

    public function submit()
    {
        try {

            if ($this->modal['state'] == "edit" && $this->modal['selected']) {

                $selectedCategory = Category::find($this->category['value']);

                if ($selectedCategory) {

                    $keywordGroup = $this->modal['selected'];
                    $keywordGroup->keywords = $this->keywords;
                    // $keywordGroup->category()->associate($selectedCategory);
                    $keywordGroup->save();
                }
            } else if ($this->modal['state'] == "new") {

                $selectedCategory = Category::find($this->category['value']);

                if ($selectedCategory) {

                    $keywordGroup = new SearchKeyword;
                    $keywordGroup->keywords = $this->keywords;
                    $keywordGroup->category()->associate($selectedCategory);
                    $keywordGroup->save();
                }
            }

        } catch (\Throwable $th) {
            $this->addError('keywords', 'Unexpected error occured. Refresh the page and try again!');
        }

        $this->modal['state'] = null;
        $this->dispatch('hide-modal');

    }

    public function remove(string $keywordGroupId)
    {
        $this->category['options'] = [];

        try {
            $searchKeyword = SearchKeyword::find($keywordGroupId);
            if ($searchKeyword) {
                $searchKeyword->delete();
            }
        } catch (\Throwable $th) {
            // 
        }
    }

    #[On('show-modal')]
    public function showModal(string $type, string $keywordGroupId = null)
    {
        $this->modal['state'] = $type;
        $this->modal['selected'] = SearchKeyword::find($keywordGroupId);

        $this->category['value'] = null;

        if ($type === "new") {
            $this->category['options'] = Category::whereDoesntHave('searchKeywords')->root()->get() ?? [];
        } else if ($type === "edit") {
            if ($this->modal['selected']) {
                $this->category['options'] = Category::whereDoesntHave('searchKeywords')->orWhere('id', $this->modal['selected']->category->id)->root()->get() ?? [];
                $this->category['value'] = $this->modal['selected']->category->id;
                $this->keywords = $this->modal['selected']->keywords;
            } else {
                $this->modal['state'] = null;
                $this->dispatch('hide-modal');
            }
        } else {
            $this->modal['state'] = null;
            $this->dispatch('hide-modal');
        }
    }

    public function searchClear()
    {
        $this->search = "";
    }
}
