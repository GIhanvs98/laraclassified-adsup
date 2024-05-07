<?php

namespace App\Livewire\Forms;

use App\Models\Membership;
use App\Models\Package;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AdminMembership extends Form
{
    public $membership_id = '';

    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required|min:10|max:500')]
    public $description = '';

    #[Validate('nullable')]
    public $icon = '';

    #[Validate('nullable|integer|min:0')]
    public $allowed_ads;

    #[Validate('nullable|integer|min:0')]
    public $allowed_pictures;

    #[Validate('required|boolean')]
    public $doorstep_delivery = false;

    #[Validate('required|min:0|decimal:0,2')]
    public $amount = "0.00";

    #[Validate('required|exists:currencies,code')]
    public $currency_code;

    #[Validate('required|boolean')]
    public $active = true;

    #[Validate('required|in:per_main_category,ignore')]
    public $allowed_ads_category_rate;

    #[Validate('required')]
    public $giveaway_packages = [];

    #[Validate('required|boolean')]
    public $post_untill_membership_duration = false;

    public function setMembership(Membership $membership)
    {
        $this->membership_id = $membership->id;
        $this->name = $membership->name;
        $this->description = $membership->description;
        $this->icon = htmlentities($membership->icon);
        $this->allowed_ads = $membership->allowed_ads;
        $this->allowed_pictures = $membership->allowed_pictures;
        $this->doorstep_delivery = $membership->doorstep_delivery ? true : false;
        $this->amount = $membership->amount;
        $this->currency_code = $membership->currency_code;
        $this->active = $membership->active ? true : false;
        $this->allowed_ads_category_rate = $membership->allowed_ads_category_rate;

        $giveaway_packages = json_decode($membership->giveaway_packages, true);

        $new_giveaway_packages = [];

        foreach ($giveaway_packages as $key => $giveaway_package) {

            $new_giveaway_packages[$giveaway_package['id']]['id'] = $giveaway_package['id'];
            $new_giveaway_packages[$giveaway_package['id']]['count'] = $giveaway_package['count'];
            $new_giveaway_packages[$giveaway_package['id']]['package'] = Package::find($giveaway_package['id']) ?? null;
            $new_giveaway_packages[$giveaway_package['id']]['states']['is_edit'] = false;
            $new_giveaway_packages[$giveaway_package['id']]['models'] = ['package' => $giveaway_package['id'], 'count' => $giveaway_package['count']];
        }

        $this->giveaway_packages = $new_giveaway_packages;

        $this->post_untill_membership_duration = $membership->post_untill_membership_duration ? true : false;
    }

    public function storeMembership()
    {
        $this->validate();

        $membership = new Membership;
        $membership->name = $this->name;
        $membership->description = $this->description;
        $membership->icon = htmlspecialchars_decode($this->icon);
        $membership->allowed_ads = $this->allowed_ads;
        $membership->allowed_pictures = $this->allowed_pictures;
        $membership->doorstep_delivery = $this->doorstep_delivery ? true : false;
        $membership->amount = $this->amount;
        $membership->currency_code = $this->currency_code;
        $membership->active = $this->active ? true : false;
        $membership->allowed_ads_category_rate = $this->allowed_ads_category_rate;

        $filteredGiveawayPackages = array_map(function ($giveaway_package) {
            if (isset ($giveaway_package['id']) && isset ($giveaway_package['count'])) {
                return ['id' => $giveaway_package['id'], 'count' => $giveaway_package['count']];
            }
            return null;
        }, $this->giveaway_packages);

        $giveaway_packages = array_values(array_filter($filteredGiveawayPackages));

        $membership->giveaway_packages = json_encode($giveaway_packages);
        $membership->save();
    }

    public function updateMembership()
    {
        $this->validate();

        $this->validate();

        $membership = Membership::find($this->membership_id);
        $membership->name = $this->name;
        $membership->description = $this->description;
        $membership->icon = htmlspecialchars_decode($this->icon);
        $membership->allowed_ads = $this->allowed_ads;
        $membership->allowed_pictures = $this->allowed_pictures;
        $membership->doorstep_delivery = $this->doorstep_delivery ? true : false;
        $membership->amount = $this->amount;
        $membership->currency_code = $this->currency_code;
        $membership->active = $this->active ? true : false;
        $membership->allowed_ads_category_rate = $this->allowed_ads_category_rate;

        $filteredGiveawayPackages = array_map(function ($giveaway_package) {
            if (isset ($giveaway_package['id']) && isset ($giveaway_package['count'])) {
                return ['id' => $giveaway_package['id'], 'count' => $giveaway_package['count']];
            }
            return null;
        }, $this->giveaway_packages);

        $giveaway_packages = array_values(array_filter($filteredGiveawayPackages));

        $membership->giveaway_packages = json_encode($giveaway_packages);
        $membership->save();
    }
}
