<?php

namespace App\Livewire\PostAd;

use App\Classes\GuestUser;
use App\Classes\SaveImages;
use App\Models\Category;
use App\Models\City;
use App\Models\Field;
use App\Models\Post;
use App\Models\User;
use App\Models\Membership;
use App\Models\SubCategoryGroup;
use App\Traits\ContactNumbersTrait;
use App\Traits\PostAdTrait;
use App\Traits\PostAdValidationsTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class Details extends Component
{
    use WithFileUploads, ContactNumbersTrait, PostAdTrait, PostAdValidationsTrait;

    public $action = "new";
    public $mainCategory;
    public $parentCategory;
    public $post;
    public $category;
    public $city;
    public $adFields = [];
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
    public $accept_terms;
    public $user_acepted_terms = false; # This only reads if user is accepted terms from user record.
    public $error_output;
    public $adsLimit;
    public $imagesLimit;
    public $validated;

    public function render()
    {
        return view('livewire.post-ad.details');
    }

    public function mount(SubCategoryGroup $mainCategory, Category $category, City $city)
    {
        $this->contact_numbers = $this->getContactNumbers();

        $this->adsLimit = config('ads.non_member_ads_limit');
        $this->imagesLimit = config('ads.non_member_images_limit');

        $this->mainCategory = $mainCategory;
        $this->category = $category;

        if ($this->category->parent) {

            $this->parentCategory = $this->category->parent;
        } else {
            $this->parentCategory = $this->category;
        }

        $this->city = $city;

        if ($this->mainCategory->id != 5 || $this->mainCategory->id != 6) {

            $this->price['value'] = null;
        }

        # If want to change the structure or add new contact number please change `contact_numbers` property in `ContactNumbersTrait` or replace `contact_numbers` with same structure.

        $contactNumbers = [
            # 'laravel_model' => 'db_column',
            'contact_number_1' => 'phone_national',
            'contact_number_2' => 'phone_alternate_1',
            'contact_number_3' => 'phone_alternate_2',
            'whatsapp_number' => 'whatsapp_number',
        ];

        if (auth()->check()) {

            $user = auth()->user();

            $this->name['value'] = $user->name;

            $this->email['value'] = $user->email;

            if (isset($this->contact_numbers) && is_array($this->contact_numbers)) {

                foreach ($contactNumbers as $key => $property) {
                    if (isset($user->{$property}) && !empty($user->{$property})) {
                        $phoneNumber = preg_replace('/\s+/', '', $user->{$property});
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

            $this->accept_terms = $user->accept_terms;
            $this->user_acepted_terms = $user->accept_terms;

            $this->imagesLimit = $user->membership->allowed_pictures;
        } else {

            if (isset($this->contact_numbers) && is_array($this->contact_numbers)) {

                foreach ($contactNumbers as $key => $property) {

                    unset($this->contact_numbers[$key]['values']['otp']);
                }

                $this->showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);
            }
        }

        # Loading fields

        if ($this->mainCategory->id != 5 || $this->mainCategory->id != 6) {

            $this->adFields = Field::withWhereHas('categoryFields', function ($query) {

                $query->whereNull('parent_id');

                $query->where(function (Builder $query) {

                    $query->where('category_id', $this->category->id);
                    $query->orWhere('category_id', $this->parentCategory->id);
                    $query->orderBy('lft', 'asc');

                });

            })->where('belongs_to', 'posts')->where('active', 1)->with(['options', 'unit'])->get();

            # Validations and models initialization.

            $this->fields = [];

            foreach ($this->adFields as $key => $field) {

                $this->fields[$field->id] = ($field->type == "checkbox_multiple" || $field->type == "checkbox") ? [] : '';

                if (isset($field->unit) && !empty($field->unit)) {

                    $this->fields[$field->unit->id] = '';
                }
            }
        }

        $this->beforeImagesLiveValidation();
    }

    public function save()
    {
        # Start Post limit - check if the user has exceeded its limit.

        if (auth()->check()) { # Authorized user

            $user = auth()->user();

            $membership = optional($user->membership);

            $postsCount = $this->postsCount('auth', $membership);

            $this->adsLimit = $membership ? $membership->allowed_ads : $this->adsLimit;
            # $this->imagesLimit = $membership ? $membership->allowed_pictures : $this->imagesLimit;

            if (isset($this->adsLimit) && !empty($this->adsLimit) && ($this->adsLimit <= $postsCount)) {
                $message = "Dear " . $user->name . ", You have exceeded your post limit for " . $this->mainCategory->name . " category.<br>";
                $message .= $membership ? "Please upgrade your membership plan." : "Please signup to a membership plan.";
                return $this->error_output = $message;
            }

        } else { # Guest user

            if (request()->session()->has('guest-user')) { # Guest user `session` exists

                $guestUser = session('guest-user');

                $guestMembership = Membership::find(config('subscriptions.memberships.default_id'));

                $postsCount = $this->postsCount('guest', $guestMembership);

                $this->adsLimit = $guestMembership ? $guestMembership->allowed_ads : $this->adsLimit;
                # $this->imagesLimit = $guestMembership ? $guestMembership->allowed_pictures : $this->imagesLimit;

                if (isset($this->adsLimit) && !empty($this->adsLimit) && ($this->adsLimit <= $postsCount)) {
                    $message = "Dear " . $guestUser->name . ", You have exceeded your post limit for " . $this->mainCategory->name . " category.<br>";
                    return $this->error_output = $message;
                }
            }

        }

        $this->title = htmlentities($this->generateTitle());

        $this->validated = $this->validate();

        try {

            $mainCategory = $this->mainCategory;

            $category = $this->category;

            $city = $this->city;

            $post = new Post;

            if (auth()->check()) {

                $user = User::find(auth()->user()->id);

                $post->user()->associate($user);

                $post->country_code = $user->country_code;

                $post->phone_country = $user->phone_country;

                $post->email_verified_at = $user->email_verified_at;

                $post->phone_verified_at = $user->phone_verified_at;

            } else {

                $post->country_code = strtoupper(config('sms.country-code-symbol'));

                $post->phone_country = strtolower(config('sms.country-code-symbol'));

                $now = now();

                $post->email_verified_at = $now;

                $post->phone_verified_at = $now;

            }

            $post->accept_terms = 1;

            $post->accept_marketing_offers = 1;

            $post->mainCategory()->associate($mainCategory);

            $post->category()->associate($category);

            $post->transaction_type = $category->transaction_type;

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

            $post->tmp_token = md5(microtime() . mt_rand(100000, 999999));

            $post->reviewed_at = null;

            $post->bumped_at = now();

            $post->save();

            $this->post = $post;

            $this->saveFields();

            # Images upload.

            SaveImages::process($post, $this->imagesOrder, $this->images, $this->localImages);

            if (!auth()->check()) {

                if (request()->session()->has('guest-user')) {
                    # Guest user exists

                    $guestUser = session('guest-user');

                    $guestUser->addPost($post->id);

                    session(['guest-user' => $guestUser]);

                } else {
                    # Guest user doesn't exists

                    $guestUser = new GuestUser();
                    $guestUser->name = $this->name;
                    $guestUser->email = $this->email;
                    $guestUser->phone = substr_replace($this->contactNumber, config('sms.country-code'), 0, 1);
                    $guestUser->addPost($post->id);

                    session(['guest-user' => $guestUser]);
                }

            }

        } catch (Throwable $th) {

            if (config('app.debug')) {
                return $this->error_output = $th->getMessage() . ' at the line ' . $th->getLine() . ' in the file ' . $th->getFile();
            } else {
                return $this->error_output = "Unexpected error occured! Try again. For more information please contact us.";
            }
        }

        return redirect()->route('post-ad.promote', ['postId' => $post->id]);
    }

}
