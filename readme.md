## LaraClassifier - Classified Ads Web Application

LaraClassifier is a Classified CMS, a powerfull modulable app and has a fully responsive design. Built with Laravel and Bootstrap. It is packed with lots of features.

## Installation & Update Documentation

### Clone from github

```
git clone https://github.com/siyaluma/classified.git
```

### Install composer packages and libraries

```
composer install
```

### Documentation steps.

The documentation is located in the folder: documentation/

## License

This software is furnished under a license and may be used and copied only in accordance with the terms of such license and with the inclusion of the above copyright notice. If you Purchased from CodeCanyon, Please read the full License from here : https://codecanyon.net/licenses/standard

## Cron jobs

### For Development

Typically, you would not add a scheduler cron entry to your local development machine. Instead, you may use the schedule:work Artisan command. This command will run in the foreground and invoke the scheduler every minute until you terminate the command:

```
php artisan schedule:work
```

### For Production

So, when using Laravel's scheduler, we only need to add a single cron configuration entry to our server that runs the schedule:run command every minute. If you do not know how to add cron entries to your server, consider using a service such as Laravel Forge which can manage the cron entries for you:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Run the site

```
php artisan serve
```

## Memberships and Ad-promotions expiration dayes.

For ad-promotions there is no extra days for payment to be settled. Since it's a one time payment.

If you need to chnage please change them in the `subscriptions.php` config file.

## Issues needs to be addressed

1. Profile image upload is stoped for now.

```html
<!--This code is commented in the `resources/views/account/edit.blade.php`-->

<div id="accordion" class="panel-group">
  {{-- PHOTO --}}
  <div class="card card-default">
    <div class="card-header">
      <h4 class="card-title">...</h4>
    </div>
    ....
    <div class="panel-collapse collapse {{ $photoPanelClass }}" id="photoPanel">
      <div class="card-body">
        <form
          name="photoUpdate"
          class="form-horizontal"
          role="form"
          method="POST"
          action="{{ url('account/photo') }}"
        >
          ...
        </form>
      </div>
    </div>
  </div>
  ...
</div>
```

2. Profile image shown in account is also commented

```html
<!--This code is commented in the `resources/views/account/edit.blade.php`-->

<h3 class="no-padding text-center-480 useradmin">
  <a style="display: flex; justify-content: center; align-items: center;">
    <img
      id="userImg"
      class="userImg"
      src="{{ $user->photo_url }}"
      alt="user"
    />&nbsp;
    <span style="margin-left: 10px;">{{ $user->name }}</span>
  </a>
</h3>
```

3. Commented at the shop page

```html
<!--This code is commented in the `resources/views/livewire/shop.blade.php`-->

<div
  class="banner-container"
  style='background-image: url("{{ \Illuminate\Support\Facades\Storage::url($user->shop->wallpaper) }}");'
>
  <!--img class="logo" src="{{ $this->userPhotoUrl() }}" alt="{{ $user->name }}" data-testid="" loading="lazy" /-->
</div>
```
