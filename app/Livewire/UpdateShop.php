<?php

namespace App\Livewire;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use App\Rules\PhoneLenght;
use App\Rules\PhoneCountry;
use Closure;

class UpdateShop extends Component
{
    use WithFileUploads;

    // #[Rule('required|min:3|max:150')]
    // public string $title = '';

    #[Rule('required|min:3|max:500')]
    public string $shopDescription = '';

    #[Rule('required|min:3|max:500')]
    public string $shopAddress = '';

    #[Rule('nullable|mimes:jpeg,jpg,png,jpe|max:5120')]
    public $wallpaperImage;
    public string $wallpaperUrl;

    #[Rule('nullable|date_format:H:i')]
    public string $mondayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $mondayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $tuesdayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $tuesdayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $wednesdayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $wednesdayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $thursdayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $thursdayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $fridayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $fridayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $saturdayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $saturdayTo = '';

    #[Rule('nullable|date_format:H:i')]
    public string $sundayFrom = '';

    #[Rule('nullable|date_format:H:i|after:time_start')]
    public string $sundayTo = '';

    public function mount()
    {
        $shop = Shop::where('user_id', auth()->user()->id);

        if ($shop->exists()) {

            $shop = $shop->first();

            // $this->title = $shop->title;
            $this->shopDescription = $shop->description;
            $this->shopAddress = $shop->address;

            $this->wallpaperUrl = Storage::url($shop->wallpaper);

            $this->mondayFrom = json_decode($shop->open_hours)->monday->from;
            $this->mondayTo = json_decode($shop->open_hours)->monday->to;

            $this->tuesdayFrom = json_decode($shop->open_hours)->tuesday->from;
            $this->tuesdayTo = json_decode($shop->open_hours)->tuesday->to;

            $this->wednesdayFrom = json_decode($shop->open_hours)->wednesday->from;
            $this->wednesdayTo = json_decode($shop->open_hours)->wednesday->to;

            $this->thursdayFrom = json_decode($shop->open_hours)->thursday->from;
            $this->thursdayTo = json_decode($shop->open_hours)->thursday->to;

            $this->fridayFrom = json_decode($shop->open_hours)->friday->from;
            $this->fridayTo = json_decode($shop->open_hours)->friday->to;

            $this->saturdayFrom = json_decode($shop->open_hours)->saturday->from;
            $this->saturdayTo = json_decode($shop->open_hours)->saturday->to;

            $this->sundayFrom = json_decode($shop->open_hours)->sunday->from;
            $this->sundayTo = json_decode($shop->open_hours)->sunday->to;
        }

    }

    public function render()
    {
        return view('livewire.update-shop');
    }

    public function updatedWallpaperImage()
    {
        $this->validate([
            'wallpaperImage' => 'nullable|image|max:5120',
        ]);
    }

    public function save()
    {
        $validated = $this->validate();

        $shop = Shop::where('user_id', auth()->user()->id);

        $user = User::find(auth()->user()->id);

        if ($shop->exists()) {

            $shop = $shop->first();

            // $shop->title = $this->title;
            $shop->description = $this->shopDescription;
            $shop->address = $this->shopAddress;

            if (isset($this->wallpaperImage) || $this->wallpaperImage != "") {

                $path = $this->wallpaperImage->store(config('pictures.local.shops'), 'public');

                $shop->wallpaper = $path;

            }

            $shop->open_hours = json_encode([
                'monday' => [
                    'from' => $this->mondayFrom,
                    'to' => $this->mondayTo,
                ],
                'tuesday' => [
                    'from' => $this->tuesdayFrom,
                    'to' => $this->tuesdayTo,
                ],
                'wednesday' => [
                    'from' => $this->wednesdayFrom,
                    'to' => $this->wednesdayTo,
                ],
                'thursday' => [
                    'from' => $this->thursdayFrom,
                    'to' => $this->thursdayTo,
                ],
                'friday' => [
                    'from' => $this->fridayFrom,
                    'to' => $this->fridayTo,
                ],
                'saturday' => [
                    'from' => $this->saturdayFrom,
                    'to' => $this->saturdayTo,
                ],
                'sunday' => [
                    'from' => $this->sundayFrom,
                    'to' => $this->sundayTo,
                ],
            ]);

            $shop->save();

        } else {

            $shop = new Shop;

            $shop->user()->associate($user);
            // $shop->title = $this->title;
            $shop->title = str()->slug(auth()->user()->name, '-');
            $shop->description = $this->shopDescription;
            $shop->address = $this->shopAddress;

            if (!isset($this->wallpaperImage) || $this->wallpaperImage == "") {

                $shop->wallpaper = config('larapen.core.picture.default');

            } else {

                $path = $this->wallpaperImage->store(config('pictures.local.shops'), 'public');

                $shop->wallpaper = $path;

            }
            $shop->open_hours = json_encode([
                'monday' => [
                    'from' => $this->mondayFrom,
                    'to' => $this->mondayTo,
                ],
                'tuesday' => [
                    'from' => $this->tuesdayFrom,
                    'to' => $this->tuesdayTo,
                ],
                'wednesday' => [
                    'from' => $this->wednesdayFrom,
                    'to' => $this->wednesdayTo,
                ],
                'thursday' => [
                    'from' => $this->thursdayFrom,
                    'to' => $this->thursdayTo,
                ],
                'friday' => [
                    'from' => $this->fridayFrom,
                    'to' => $this->fridayTo,
                ],
                'saturday' => [
                    'from' => $this->saturdayFrom,
                    'to' => $this->saturdayTo,
                ],
                'sunday' => [
                    'from' => $this->sundayFrom,
                    'to' => $this->sundayTo,
                ],
            ]);

            $shop->save();

            return redirect()->route('shops.index', ['id' => auth()->user()->shop->id, 'slug' => \Illuminate\Support\Str::slug(auth()->user()->shop->title, '-')]);

        }
    }
}