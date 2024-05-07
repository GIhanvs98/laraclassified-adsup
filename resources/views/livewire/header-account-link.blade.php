<?php

use function Livewire\Volt\{mount, state};
use App\Helpers\UrlGen;
use App\Models\Permission;

state(['url', 'text']);
 
mount(function () {

    $this->url = UrlGen::login();
    $this->text = 'Login';

    if (auth()->check()){

        $this->text = 'My Account';

        if(auth()->user()->can(Permission::getStaffPermissions())){

            $this->url = admin_url('/');
             
        }else{
            
            $this->url = url('account');
        }
    }
 
});

?>

<a href="{{ $url }}" style="color: white;">
<span>
    <svg viewBox="0 0 60 60"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path
            d="M36.723 34.65c1.637 1.243 6.495 2.38 10.397 4.484 1.286.694 1.396 1.724 1.492 2.345.097.622.3 7.404.3 7.404H11s.204-6.782.3-7.404c.097-.621.105-1.565 1.493-2.345 3.866-2.169 8.738-3.169 10.375-4.412.65-.494.482-1.292.627-2.058.144-.766.626-.24.626-1.052 0-.819.153-.676.063-1.763-.078-.948-1.386-1.049-1.46-2.821-.015-.374-.674-.623-1.06-1.197-.385-.574-1.01-1.579-1.01-2.727s.24-.862.24-2.297-.053-2.267.771-5.598c.308-1.243 1.354-2.7 2.402-3.359 1.413-.888.845.296 5.593-.756 3.077-.682 7.898 2.488 7.946 5.024.065 3.43.276 3.172.48 4.593.145 1.005.434.766.434 1.914 0 1.15-.626 2.584-1.011 3.158-.385.575-.742 1.496-.792 1.866-.252 1.917-1.54 1.474-1.593 2.501-.05.938-.111.818.03 1.549.118.61.476-.04.62.726.146.765.045 1.89.649 2.225">
        </path>
    </svg>
</span>
<span class="d-none d-lg-block">{{ $text }}</span>
</a>