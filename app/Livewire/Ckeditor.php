<?php

namespace App\Livewire;

use Livewire\Component;

class Ckeditor extends Component
{
    public $content;
    
    public function render()
    {
        return view('livewire.ckeditor');
    }
}
