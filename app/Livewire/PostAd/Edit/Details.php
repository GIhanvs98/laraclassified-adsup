<?php

namespace App\Livewire\PostAd\Edit;

use App\Classes\SaveImages;
use App\Helpers\UrlGen;
use App\Models\Category;
use App\Models\Field;
use App\Models\Post;
use App\Models\PostValue;
use App\Models\SubAdmin2;
use App\Models\PostReviewingViolation;
use App\Traits\ContactNumbersTrait;
use App\Traits\EditPostAdCityModelTrait;
use App\Traits\PostAdTrait;
use App\Traits\PostAdValidationsTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;
use Illuminate\Support\Facades\Log;

class Details extends Component
{
    use WithFileUploads, ContactNumbersTrait, PostAdTrait, PostAdValidationsTrait, EditPostAdCityModelTrait;

    public $action = "edit";
    public $mainCategory;
    public $post;
    public $category;
    public $city;
    public $adFields;
    public $categories;
    public $postValues;
    public $title;
    public $fields = [];
    public $images = [];
    public $localImages = [];
    public $imagesOrder = [];
    public $content = "";
    public $price = [];
    public array $name = [
        'value' => null,
        'disabled' => true,
    ];
    public array $email = [
        'value' => null,
        'disabled' => true,
    ];
    public $negotiable = false;
    public $accept_terms = true;
    public $error_output;
    public $adsLimit;
    public $imagesLimit;
    public $validated;

    # Model data
    public $districts;
    public $selectedDistrict;
    public $cities;

    public function render()
    {
        # Loading districts 

        $this->districts = SubAdmin2::where('active', 1)->withWhereHas('cities', function ($query) {
            $query->orderBy('order', 'asc');
        })->orderBy('order', 'asc')->get();

        return view('livewire.post-ad.edit.details');
    }

    public function mount(Post $post)
    {
        $this->contact_numbers = $this->getContactNumbers();

        # Loading post data.

        $this->post = $post;

        $this->adsLimit = config('ads.non_member_ads_limit');

        $this->imagesLimit = config('ads.non_member_images_limit');

        if (auth()->check()) {

            $this->imagesLimit = auth()->user()->membership->allowed_pictures;
        }

        # Loading Default configuration for a post.

        $this->category = $this->post->category;

        $this->city = $this->post->city;

        $this->mainCategory = $this->post->mainCategory;

        if (isset($this->category->parent->id) && !empty($this->category->parent->id)) {

            $this->categories = Category::where('parent_id', $this->category->parent->id)->where('active', 1)->get();
        }

        if ($this->mainCategory->id != 5 || $this->mainCategory->id != 6) {

            $this->price['value'] = null;
        }

        $this->title = $this->post->title;

        $this->localImages = [];

        foreach ($this->post->pictures as $image) {

            $this->localImages[] = [
                "absolute_url" => asset(config('pictures.thumbnail_image.image_location') . '/' . $image->thumbnailImage->filename),
                "id" => $image->id
            ];

        }

        $this->content = $this->post->description;

        $this->price['value'] = $this->post->price;

        if (isset($this->post->price_unit) && !empty($this->post->price_unit)) {

            $this->price['unit'] = $this->post->price_unit;
        }

        $this->negotiable = ($this->post->negotiable == 1) ? true : false;

        $this->name['value'] = $this->post->contact_name;

        $this->email['value'] = $this->post->email;

        # If want to change the structure or add new contact number please change `contact_numbers` property in `ContactNumbersTrait` or replace `contact_numbers` with same structure.

        if (isset($this->contact_numbers) && is_array($this->contact_numbers)) {

            $contactNumbers = [
                # 'laravel_model' => 'db_column',
                'contact_number_1' => 'phone_national',
                'contact_number_2' => 'phone_alternate_1',
                'contact_number_3' => 'phone_alternate_2',
                'whatsapp_number' => 'whatsapp_number'
            ];

            foreach ($contactNumbers as $key => $property) {
                if (isset($this->post->{$property}) && !empty($this->post->{$property})) {
                    $phoneNumber = preg_replace('/\s+/', '', $this->post->{$property});
                    $phoneNumber = preg_replace('/^\+94/', '0', $phoneNumber);
                    $this->contact_numbers[$key]['values']['contact_number'] = $phoneNumber;

                    $this->contact_numbers[$key]['states']['stage'] = 'success';
                    $this->contact_numbers[$key]['states']['visibility'] = true;
                } else {
                    unset($this->contact_numbers[$key]['values']['contact_number']);
                    $this->contact_numbers[$key]['states']['stage'] = null;
                }
                unset($this->contact_numbers[$key]['values']['otp']);
            }

            $this->showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);
        }

        # Loading fields

        if ($this->mainCategory->id != 5 || $this->mainCategory->id != 6) {

            $this->adFields = Field::withWhereHas('categoryFields', function ($query) {

                $query->whereNull('parent_id');

                $query->where(function (Builder $query) {

                    $query->where('category_id', $this->category->id);

                    if (isset ($this->category->parent->id) && !empty ($this->category->parent->id)) {

                        $query->orWhere('category_id', $this->category->parent->id);
                    }

                    $query->orderBy('lft', 'asc');

                });

            })->where('belongs_to', 'posts')->where('active', 1)->with(['options', 'unit'])->get();

            # Validations and models initialization.

            $this->fields = [];

            $this->postValues = PostValue::where('post_id', $this->post->id)->get();

            foreach ($this->adFields as $key => $field) {

                if ($field->type == "checkbox_multiple") {

                    $this->fields[$field->id] = [];

                    $checkboxValues = PostValue::where('post_id', $this->post->id)->where('field_id', $field->id)->get();

                    foreach ($checkboxValues as $key => $checkboxValue) {

                        $this->fields[$field->id][] = $checkboxValue->value;
                    }

                } else {

                    $this->fields[$field->id] = $this->postValues->where('field_id', $field->id)->first()->value ?? null;

                    if (!isset($this->postValues->where('field_id', $field->id)->first()->value)) {
                        Log::error(json_encode($field));
                    }
                }

                if (isset($field->unit) && !empty($field->unit)) {

                    $this->fields[$field->unit->id] = $this->postValues->where('field_id', $field->unit->id)->first()->value ?? null;
                }
            }
        }

        $this->beforeImagesLiveValidation();
    }

    public function save()
    {

        $this->title = htmlentities($this->generateTitle());

        $this->validated = $this->validate();

        try {

            if (isset(auth()->user()->is_admin) && !empty(auth()->user()->is_admin) && auth()->user()->is_admin == "1") {

                $userId = Post::find($this->post->id)->user_id;

            } else if (auth()->check()) {

                $userId = auth()->user()->id;

                if (Post::find($this->post->id)->user()->whereId($userId)->doesntExist()) {
                    # Unauthrozed.
                    return $this->error_output = "This post do not belongs to you! You can't apply your changes to this post.";
                }

            } else if (request()->session()->has('guest-user')) {

                /* ----------------------------------------------------------------------------
                /
                / Guest users are allowed to edit their posts by using their contact numbers.
                /
                / In order to do that users must verify their contact numbers wiht the OTP send to their mobile phones.
                /
                / Then the `postId` will be stored in session to edit their ad. 
                /  ----------------------------------------------------------------------------
                */

                $guestUser = session('guest-user');

                if ($guestUser->doesntHavePost($this->post->id)) {
                    # Unauthrozed.
                    return $this->error_output = "This post do not belongs to you! You can't apply your changes to this post.";
                }

            } else {
                # Not Login.
                return $this->error_output = "You have lost your account credentials! Please login again.";
            }

            # Post does't exists.
            if (Post::whereId($this->post->id)->doesntExist()) {

                return $this->error_output = "The post you are trying to edit do not exists. Please contact us for more information.";
            }

            $city = $this->city;

            $post = $this->post;

            if (auth()->check()) {

                $post->country_code = $post->user->country_code;

                $post->phone_country = $post->user->phone_country;

            } else {

                $post->country_code = strtoupper(config('sms.country-code-symbol'));

                $post->phone_country = strtolower(config('sms.country-code-symbol'));

            }

            $post->title = $this->title;

            $post->description = $this->content;

            $post->tags = $this->title . ", " . $this->price['value'] ?? null;

            $post->price = $this->price['value'] ?? null;

            $post->price_unit = $this->price['unit'] ?? null;

            $post->currency_code = "LKR";

            $post->negotiable = $this->negotiable;

            $post->auth_field = "email";

            $post->contact_name = $this->name['value'];

            $post->email = $this->email['value'];

            $post->phone_national = $this->contact_numbers['contact_number_1']['states']['visibility'] ? $this->contact_numbers['contact_number_1']['values']['contact_number'] ?? null : null;

            $post->phone = $this->contact_numbers['contact_number_1']['states']['visibility'] ? $this->formatPhoneNumber($this->contact_numbers['contact_number_1']['values']['contact_number'] ?? null, config('sms.country-code')) : null;

            $post->phone_alternate_1 = $this->contact_numbers['contact_number_2']['states']['visibility'] ? $this->formatPhoneNumber($this->contact_numbers['contact_number_2']['values']['contact_number'] ?? null, config('sms.country-code')) : null;

            $post->phone_alternate_2 = $this->contact_numbers['contact_number_3']['states']['visibility'] ? $this->formatPhoneNumber($this->contact_numbers['contact_number_3']['values']['contact_number'] ?? null, config('sms.country-code')) : null;

            $post->whatsapp_number = $this->contact_numbers['whatsapp_number']['states']['visibility'] ? $this->formatPhoneNumber($this->contact_numbers['whatsapp_number']['values']['contact_number'] ?? null, config('sms.country-code')) : null;

            $post->city()->associate($city);

            $post->ip_addr = request()->ip();

            $post->lat = $city->latitude;

            $post->lon = $city->longitude;

            $post->reviewed_at = (!isset(auth()->user()->is_admin) || empty(auth()->user()->is_admin)) ? null : $post->reviewed_at;

            # If an review posted by admin.

            $reviewingViolation = PostReviewingViolation::whereBelongsTo($post)->notRecheckedTimeleft()->first();

            if ($reviewingViolation) {

                $reviewingViolation->rechecked_datetime = now();

                $reviewingViolation->save();
            }

            $post->save();

            $this->saveFields();

            # Images upload.

            SaveImages::process($post, $this->imagesOrder, $this->images, $this->localImages);

        } catch (Throwable $th) {

            if (config('app.debug')) {
                return $this->error_output = $th->getMessage() . ' at the line ' . $th->getLine() . ' in the file ' . $th->getFile();
            } else {
                return $this->error_output = "Unexpected error occured! Try again. For more information please contact us.";
            }
        }

        return redirect(UrlGen::post($post));
    }

}
