<?php

use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use App\Livewire\Forms\AdminMembership;
use function Livewire\Volt\{mount, state, form};

state(['currencyCodes', 'packages']);

form(AdminMembership::class);

mount(function ($membership, $currencyCodes) {

    $this->form->setMembership($membership);

    $this->currencyCodes = $currencyCodes;

    $this->packages = Package::where('active', true)->get() ?? [];

});

$save = function () {

    $this->form->updateMembership();

    return $this->redirect(route('memberships.index'));
};

$addGiveawayPackage = function () {

    array_push($this->form->giveaway_packages, [
        'id' => null,
        'count' => null,
        'states' => [
            'is_edit' => true,
        ],
        'package' => null,
        'models' => [
            'package' => null,
            'count' => null,
        ]
    ]);
};

$editPackage = function (string $package_key) {

    $this->form->giveaway_packages[$package_key]['states']['is_edit'] = true;
};

$savePackage = function (string $package_key) {

    $validated = $this->validate([
        'form.giveaway_packages.*.models.package' => 'required|exists:packages,id',
        'form.giveaway_packages.*.models.count' => 'required|integer|min:0',
    ], attributes: [
        'form.giveaway_packages.*.models.package' => 'package',
        'form.giveaway_packages.*.models.count' => 'package',
    ]);

    $this->form->giveaway_packages[$package_key]['id'] = $this->form->giveaway_packages[$package_key]['models']['package'] ?? $this->form->giveaway_packages[$package_key]['id'];
    $this->form->giveaway_packages[$package_key]['count'] = $this->form->giveaway_packages[$package_key]['models']['count'] ?? $this->form->giveaway_packages[$package_key]['count'];
    $this->form->giveaway_packages[$package_key]['package'] = Package::find($this->form->giveaway_packages[$package_key]['models']['package']) ?? null;

    $this->form->giveaway_packages[$package_key]['states']['is_edit'] = false;
};

$deletePackage = function (string $package_key) {

    unset($this->form->giveaway_packages[$package_key]);
};

?>

<div class="flex-row d-flex justify-content-center">
    
    @php
        $colMd = config('settings.style.admin_boxed_layout') == '1' ? ' col-md-12' : ' col-md-9';
    @endphp

    <div class="col-sm-12{{ $colMd }}">

        <a href="{{ route('memberships.index') }}" class="btn btn-primary shadow">
            <i class="fa fa-angle-double-left"></i>
            <span class="text-lowercase">Back to all</span>
        </a>

        <form class="card border-top border-primary mt-4" wire:submit="save">

            <div class="card-header">
                <h3 class="mb-0">Edit membership</h3>
            </div>

            <div class="card-body">
                <div class="card mb-0">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bolder">Name</label>
                            <input type="text" wire:model="form.name" placeholder="Name" class="form-control">
                            @error('form.name')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <span class="form-label fw-bolder">Enable / Disable</span>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" wire:model="form.active" class="form-check-input" id="membership-status" style="cursor: pointer;">
                                <label class="form-check-label" for="membership-status" style="cursor: pointer;">Status</label>
                            </div>
                            @error('form.active')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bolder">Ads per category (Empty for ∞)</label>
                            <input type="number" wire:model="form.allowed_ads" placeholder="Total ads" class="form-control" min="0">
                            @error('form.allowed_ads')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bolder">Ads per category rate/unit</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="form.allowed_ads_category_rate" id="allowed_ads_category_rate-per_main_category" value="per_main_category" style="cursor: pointer;">
                                <label class="form-check-label" for="allowed_ads_category_rate-per_main_category" style="cursor: pointer;">Per main category (`Ads per category` will be assign per main category.)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="form.allowed_ads_category_rate" id="allowed_ads_category_rate-ignore" value="ignore" style="cursor: pointer;">
                                <label class="form-check-label" for="allowed_ads_category_rate-ignore" style="cursor: pointer;">Ignore (`Ads per category` will be assign for all categories collectively.)</label>
                            </div>
                            @error('form.allowed_ads_category_rate')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <span class="form-label fw-bolder">Post until membership duration</span>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" wire:model="form.post_untill_membership_duration" class="form-check-input" id="membership-post_untill_membership_duration" style="cursor: pointer;">
                                <label class="form-check-label" for="membership-post_untill_membership_duration" style="cursor: pointer;">This allows posts with memberships to have unlimited duration (without auto deletion in `x` number of days. managed by `<a href="{{ url('/admin/packages') }}" target="_blank">Packages</a> -> Duration + Promotion Duration` fileds) until the a user have a valid membership.</label>
                            </div>
                            @error('form.post_untill_membership_duration')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bolder">Images per category (Empty for ∞)</label>
                            <input type="number" wire:model="form.allowed_pictures" placeholder="Total images" class="form-control" min="0" pattern="[0-9]*">
                            @error('form.allowed_pictures')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-bolder">Amount</label>
                            <div style="display: flex;">
                                <select wire:model="form.currency_code" class="form-select" style="width: 34%;">
                                    <option value="">Select the currecy</option>
                                    @foreach($currencyCodes as $key => $currencyCode)
                                        <option value="{{ $currencyCode->code }}">{{ $currencyCode->code }}</option>
                                    @endforeach
                                </select>
                                <input type="number" wire:model="form.amount" placeholder="0.00" class="form-control" min="0" style="width: 66%;">
                            </div>
                            @error('form.currency_code')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                            @error('form.amount')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-bolder">SVG Icon</label>
                            <textarea wire:model="form.icon" placeholder="svg icon code" rows="6" class="form-control"></textarea>
                            @error('form.icon')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-bolder">Description</label>
                            <textarea wire:model="form.description" placeholder="Description" rows="6" class="form-control"></textarea>
                            @error('form.description')
                                <div style="margin-bottom: 0px; color: red;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-bolder">Giveaway packages</label>
                            <div class="giveaway-packages-container">
                                @if($form->giveaway_packages)
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="border-bottom: 2px solid #dee2e6;">#</th>
                                                <th scope="col" style="border-bottom: 2px solid #dee2e6;">Package</th>
                                                <th scope="col" style="border-bottom: 2px solid #dee2e6;">Count</th>
                                                <th scope="col" style="border-bottom: 2px solid #dee2e6;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $giveaway_package_key = 0;
                                            @endphp
                                            @foreach($form->giveaway_packages as $key => $giveaway_package)
                                                @php
                                                    $giveaway_package_key += 1;
                                                @endphp
                                                <tr wire:key="package_{{ $key }}">
                                                    <th scope="row">{{ $giveaway_package_key }}</th>
                                                    <td>
                                                        @if($giveaway_package['states']['is_edit'])
                                                            <select wire:model="form.giveaway_packages.{{ $key }}.models.package" class="form-control form-control-sm">
                                                                <option value="">Select a package</option>
                                                                @foreach($packages as $selectKey => $package)
                                                                    <option wire:key="package_item_{{ $selectKey }}" value="{{ $package->id }}">{{ ucfirst($package->name) }} - {{ ucfirst($package->short_name) }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('form.giveaway_packages.'.$key.'.models.package')
                                                                <div style="margin-bottom: 0px; color: red; font-size: 12px;">{{ $message }}</div>
                                                            @enderror
                                                        @else
                                                            {{ $giveaway_package['package']->name }} - {{ $giveaway_package['package']->short_name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($giveaway_package['states']['is_edit'])
                                                            <input wire:model="form.giveaway_packages.{{ $key }}.models.count" type="number" class="form-control form-control-sm" placeholder="Enter the number of packages">
                                                            @error('form.giveaway_packages.'.$key.'.models.count')
                                                                <div style="margin-bottom: 0px; color: red; font-size: 12px;">{{ $message }}</div>
                                                            @enderror
                                                        @else
                                                            {{ $giveaway_package['count'] }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($giveaway_package['states']['is_edit'])
                                                            <button type="button" wire:click="savePackage('{{ $key }}')" class="btn btn-success btn-sm">Save</button>
                                                        @else
                                                            <button type="button" wire:click="editPackage('{{ $key }}')" class="btn btn-primary btn-sm">Edit</button>
                                                        @endif
                                                        <button type="button" wire:click="deletePackage('{{ $key }}')" class="btn btn-danger btn-sm">Delete</button>
                                                    </td>
                                                </tr>

                                                @php
                                                    foreach ($this->packages as $index => $package) {
                                                        if ($package['id'] == $key) {
                                                            unset($this->packages[$index]);
                                                        }
                                                    }
                                                @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="d-inline-block w-100 text-center mb-3">
                                        No giveaway packages.
                                    </div>
                                @endif
                            </div>
                            <button class="btn btn-primary btn-sm w-100" type="button" wire:click="addGiveawayPackage">Add new giveaway package</button>
                        </div>

                    </div>
                </div>

            </div>

            <div class="card-footer" style="padding-left: 0px;">
                <div>
                    <button type="submit" class="btn btn-primary shadow">
                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp; <span>Save and back</span>
                    </button>

                    <button type="reset" class="btn btn-secondary shadow"><span class="fa fa-ban"></span>&nbsp;Cancel</button>
                </div>
            </div>

        </form>

    </div>
</div>