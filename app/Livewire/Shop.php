<?php

namespace App\Livewire;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Shop extends Component
{
    use WithPagination;

    public int $id;

    public string $username;

    public $showMore = false;

    public array $order = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public string $query = '';

    public function mount($id = null, $username = null)
    {
        $this->id = $id;
        $this->username = $username;
    }

    public function userPhotoUrl()
    {
        // Default Photo
        $defaultPhotoUrl = imgUrl(config('larapen.core.avatar.default'));

        // Photo from User's account
        $userPhotoUrl = null;
        if (isset($this->photo) && !empty($this->photo)) {
            $disk = StorageDisk::getDisk();
            if ($disk->exists($this->photo)) {
                $userPhotoUrl = imgUrl($this->photo, 'user');
            }
        }

        return !empty($userPhotoUrl) ? $userPhotoUrl : $defaultPhotoUrl;
    }

    public function render()
    {
        return view('livewire.shop', [
            'posts' => Post::with(['pictures', 'currency', 'category', 'city'])
                            ->where('user_id', $this->id)
                            ->where(function (Builder $query) {
                                $query->Where('title', 'like', '%'.$this->query.'%')
                                ->orWhere('description', 'like', '%'.$this->query.'%')
                                ->orWhere('tags', 'like', '%'.$this->query.'%')
                                ->orWhere('price', 'like', '%'.$this->query.'%');
                            })
                            ->whereNotNull('email_verified_at')->whereDate('email_verified_at', '<=', now())
                            ->whereNotNull('phone_verified_at')->whereDate('phone_verified_at', '<=', now())
                            ->whereNotNull('reviewed_at')->whereDate('reviewed_at', '<=', now())
                            ->whereNull('archived_at')->orWhereDate('archived_at', '<=', now())
                            ->whereNull('archived_manually_at')->orWhereDate('archived_manually_at', '<=', now())
                            ->whereNull('deleted_at')->orWhereDate('deleted_at', '<=', now())
                            ->paginate(20),
                            
            'user' => User::with(['membership', 'shop'])->whereId($this->id)->first(),
        ]);
    }
}
