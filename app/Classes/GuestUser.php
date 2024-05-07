<?php

namespace App\Classes;

class GuestUser
{
    public $name;
    public $email;
    public $phone;
    public $created_at;
    public $updated_at;

    public array $posts = [];

    public function addPost(string $postId)
    {
        $this->posts[] = $postId;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    public function havePost(string $postId)
    {
        return in_array($postId, $this->posts);
    }

    public function doesntHavePost(string $postId)
    {
        return !in_array($postId, $this->posts);
    }

}