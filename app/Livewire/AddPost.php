<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class AddPost extends Component
{
    public $title;

    public $content;

    public function render()
    {
        return view('livewire.add-post');
    }

    public function save()
    {
        $post = new Post;
        $post->title = $this->title;
        $post->description = htmlentities($this->content);
        $post->save();
    }
}
