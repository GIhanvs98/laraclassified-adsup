{{--
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@php
$addListingUrl = (isset($addListingUrl)) ? $addListingUrl : \App\Helpers\UrlGen::addPost();
$addListingAttr = '';

if (!auth()->check()) {
if (config('settings.single.guests_can_post_listings') != '1') {
$addListingUrl = '#quickLogin';
$addListingAttr = ' data-bs-toggle="modal"';
}
}
@endphp

@section('content')

@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])

<div class="main-container inner-page">
    <div class="container" style="background: white;padding-top:20px;padding-bottom:20px;border-radius: 4px;">
        <div class="inner-box category-content bg-white flex" style="margin-bottom: 0px;padding-bottom: inherit;">

            {{-------------------------------------------------------------------------------------------}}

            <div class="w-full max-w-sm">

                <form action="{{ route('search') }}" method="GET" id="searchForm">

                    <input type="text" name="p" id="price" value="{{ base64_encode(json_encode($price)) }}" readonly hidden>

                    <input type="text" name="c" id="category" value="{{ base64_encode(json_encode($category)) }}" readonly hidden>

                    <input type="text" name="l" id="location" value="{{ base64_encode(json_encode($location)) }}" readonly hidden>

                    <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                        <h2>Query</h2>

                        <div>
                            <input type="text" name="q" id="search" placeholder="Search Adsup" value="{{ $query }}">
                            <button type="submit">Search</button>
                        </div>

                    </div>

                    {{-- Locations --}}
                    <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                        <h2>Location</h2>

                        @if(isset($location->d) || isset($location->c))
                        <div id="clear-locations">All of Sri Lanka</div>
                        @else
                        <strong>
                            <div id="clear-locations">All of Sri Lanka</div>
                        </strong>
                        @endif

                        @if(isset($districts))
                        <ol style="margin-left: 20px !important;">

                            @if(isset($location->d) || isset($location->c))
                            {{-- If a city or district is selected --}}

                            <li>

                                @if(isset($location->d))

                                <strong>
                                    <label><input type="radio" value="{{ $districts->id }}" class="district" name="location" hidden> {{ $districts->name }}</label>
                                </strong>

                                @else

                                <label><input type="radio" value="{{ $districts->id }}" class="district" name="location" hidden> {{ $districts->name }}</label>

                                @endif

                                @if (isset($districts->cities))

                                <ol style="margin-left: 20px !important;">

                                    @if(isset($location->c))

                                    @foreach($districts->cities as $key => $city)

                                    @if($location->c == $city->id)

                                    <strong>
                                        <label style="display: block;"><input type="radio" value="{{ $city->id }}" class="city" name="location" hidden> {{ $city->name }}</label>
                                    </strong>

                                    @endif

                                    @endforeach

                                    @else

                                    @foreach($districts->cities as $key => $city)

                                    <label style="display: block;"><input type="radio" value="{{ $city->id }}" class="city" name="location" hidden> {{ $city->name }}</label>

                                    @endforeach

                                    @endif

                                </ol>
                                @else
                                No Cities
                                @endif

                            </li>

                            @else

                            @foreach($districts as $key => $district)
                            <li>

                                <label><input type="radio" value="{{ $district->id }}" class="district" name="location" hidden> {{ $district->name }}</label>

                            </li>
                            @endforeach

                            @endif

                        </ol>
                        @else
                        No Districts
                        @endif

                    </div>

                    <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                        <div class="input-group" style="margin-top: 20px;">
                            <div class="text-xs text-gray-500 flex">Sort results by</div>
                            <select class="border w-full mt-1 mb-1 p-2 cursor-pointer sort" style="font-size: 14px;" name="sort">

                                <option value="date_new_top" @if(!isset($sort) || $sort=="date_new_top" ) selected @endif>Date: Newest on top</option>
                                <option value="date_old_top" @If($sort=="date_old_top" ) selected @endif>Date: Oldest on top</option>
                                <option value="price_high_to_low" @If($sort=="price_high_to_low" ) selected @endif>Price: High to low</option>
                                <option value="price_low_to_high" @If($sort=="price_low_to_high" ) selected @endif>Price: Low to high</option>

                            </select>
                        </div>

                    </div>

                    <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                        <h2>Membership</h2>

                        <label style="display: block;"><input type="radio" value="0" class="membership" name="m" @if($membership==0) checked @endif hidden>
                            @if($membership === "0")
                            <strong>
                                <div>All Ads</div>
                            </strong>
                            @else
                            <div>All Ads</div>
                            @endif
                        </label>

                        <label style="display: block;"><input type="radio" value="1" class="membership" name="m" @if($membership==1) checked @endif hidden>
                            @if($membership === "1")
                            <strong>
                                <div>Members Only</div>
                            </strong>
                            @else
                            <div>Members Only</div>
                            @endif
                        </label>

                    </div>

                </form>


                <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                    <h2>Price</h2>

                    <div style="display: flex; margin-bottom: 10px;">

                        <input type="number" id="minPrice" placeholder="Min (LKR)" @if(isset($price)) value="{{ $price->min }}" @endif class="w-1/2">

                        <input type="number" id="maxPrice" placeholder="Max (LKR)" @if(isset($price)) value="{{ $price->max }}" @endif class="w-1/2">

                    </div>

                    <button type="button" class="setPrice">Apply</button>

                </div>


                <div style="padding: 5px 20px 25px; border-bottom: 1px solid #000;">

                    <h2>Category</h2>

                    <input type="text" id="category-id" hidden readonly @if(isset($category->id)) value="{{ $category->id }}" @endif>

                    <input type="text" id="sub-category-id" hidden readonly @if(isset($category->subId)) value="{{ $category->subId }}" @endif>

                    @if(isset($category->id))
                    <div id="clear-categories">All categories</div>
                    @else
                    <strong>
                        <div id="clear-categories">All categories</div>
                    </strong>
                    @endif

                    <ol style="margin-left: 20px !important;">

                        @if(isset($categories->id))

                        <li>

                            @if(isset($category->subId))
                            <label><input type="radio" value="{{ $categories->id }}" class="category" name="category" hidden> {{ $categories->name }}</label>
                            @else
                            <strong>
                                <label><input type="radio" value="{{ $categories->id }}" class="category" name="category" hidden> {{ $categories->name }}</label>
                            </strong>
                            @endif

                            <ol style="margin-left: 20px !important;">
                                @if(isset($subCategories))

                                @if(isset($category->subId))

                                @foreach($subCategories as $key => $subCategory)

                                @if($category->subId == $subCategory->id)
                                <li>
                                    <strong>
                                        <label><input type="radio" value="{{ $subCategory->id }}" class="subCategory" name="subCategory" hidden> {{ $subCategory->name }}</label>
                                    </strong>
                                </li>
                                @endif

                                @endforeach

                                @else

                                @foreach($subCategories as $key => $subCategory)
                                <li>
                                    <label><input type="radio" value="{{ $subCategory->id }}" class="subCategory" name="subCategory" hidden> {{ $subCategory->name }}</label>
                                </li>
                                @endforeach

                                @endif

                                @if(isset($category->subId))
                                <ol>
                                    @foreach($categories->fields as $key => $field)

                                    @switch($field->type)

                                    @case('select')
                                    <div class="input-group" style="margin-top: 20px;">
                                        <div class="text-xs text-gray-500 flex">{{ $field->name }}</div>
                                        <select class="border w-full mt-1 mb-1 p-2 cursor-pointer field" style="font-size: 14px;" name="{{ $field->id }}">

                                            <option value="" disabled @if(isset($category->fields))
                                                @if(!isset($category->fields[$field->id]) || empty($category->fields[$field->id]) ) selected @endif
                                                @else
                                                selected
                                                @endif >Select the {{ strtolower($field->name) }}</option>

                                            @isset($field->options)

                                            @foreach($field->options as $key => $option)
                                            <option value="{{ $option->id }}" @if(isset($category->fields)) @if($category->fields[$field->id] == $option->id) selected @endif @endif>{{ $option->value }}</option>

                                            @endforeach

                                            @endisset

                                        </select>
                                    </div>
                                    @break

                                    @case('radio')
                                    <div class="input-group block" style="margin-top: 20px;">
                                        <div class="text-xs text-gray-500 flex">{{ $field->name }}</div>

                                        @if(isset($field->options))
                                        @foreach($field->options as $key => $option)
                                        <label class="flex mt-1 w-fit">
                                            <input value="{{ $option->id }}" type="radio" name="{{ $field->id }}" @if(isset($category->fields)) @if($category->fields[$field->id] == $option->id) checked @endif @endif class="border mt-1 mb-1 p-2 field" style="font-size: 14px;cursor: pointer;">
                                            <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">{{ $option->value }}</div>
                                        </label>
                                        @endforeach
                                        @else
                                        <div class="flex text-xs text-gray-800 mt-1">No options.</div>
                                        @endif
                                    </div>
                                    @break

                                    @case('checkbox_multiple')
                                    <div class="input-group block" style="margin-top: 20px;">
                                        <div class="text-xs text-gray-500 flex">{{ $field->name }}</div>

                                        @if(isset($field->options))
                                        @foreach($field->options as $key => $option)
                                        <label class="flex mt-1 w-fit">
                                            <input value="{{ $option->id }}" type="checkbox" name="{{ $field->id }}" @if(isset($category->fields)) @if(in_array($option->id, $category->fields[$field->id])) checked @endif @endif class="border mt-1 mb-1 p-2 cmfield" style="font-size: 14px;cursor: pointer;">
                                            <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">{{ $option->value }}</div>
                                        </label>
                                        @endforeach
                                        @else
                                        <div class="flex text-xs text-gray-800 mt-1">No options.</div>
                                        @endif

                                        <input type="checkbox_multiple" hidden checkbox_multiple_name="{{ $field->id }}" class="field">

                                    </div>
                                    @break

                                    @default
                                    {{-- Ignore --}}
                                    @break

                                    @endswitch

                                    @endforeach
                                </ol>
                                @endif

                                @else
                                No sub-categories
                                @endif
                            </ol>

                        </li>

                        @else

                        @foreach($categories as $key => $category)

                        <li>
                            <label><input type="radio" value="{{ $category->id }}" class="category" name="category" hidden> {{ $category->name }}</label>
                        </li>

                        @endforeach

                        @endif
                    </ol>

                </div>


            </div>

            <div class="w-full">

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="contentAll" role="tabpanel" aria-labelledby="tabAll">
                        <div id="postsList" class="category-list-wrapper posts-wrapper row no-margin">
                            <div class="col-12" style="padding-left: 0px;">

                                @each('shops.listing-item', $results, 'post', 'shops.empty')

                            </div>
                        </div>
                        <div class="flex justify-between align-middle text-gray-500">
                            <div style="padding: 15px;padding-top: 0.85em;">Showing {{ $results->firstItem() }} to {{ $results->lastItem() }} of {{ $results->total() }} entries.</div>
                            <div>{{ $results->links() }}</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-------------------------------------------------------------------------------------------}}

        </div>
    </div>
</div>
@endsection

@section('after_styles')

{{-- Flowbite css --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

<style>
    .btn-primary {
        background-color: var(--primary-color);
        background: var(--primary-color);
        border-color: #016fd7;
    }

    .posts-wrapper {
        overflow: auto;
        min-height: fit-content !important;
    }

    label {
        font-weight: inherit;
    }

</style>

@endsection

@section('after_scripts')

{{-- Jquery --}}
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

{{-- Flowbite js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

<script>
    $(function() {

        // When a district is selected.
        $(".district").on("click", function() {

            // location : l={"d":12}

            const locationArray = {
                d: $(this).val()
            };

            let locationJsonString = JSON.stringify(locationArray);

            const encodedLocation = btoa(locationJsonString); // encode a string

            $("#location").val(encodedLocation);

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When a city is selected.
        $(".city").on("click", function() {

            // location : l={"c":23}

            const locationArray = {
                c: $(this).val()
            };

            let locationJsonString = JSON.stringify(locationArray);

            const encodedLocation = btoa(locationJsonString); // encode a string

            $("#location").val(encodedLocation);

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When a category is selected.
        $(".category, .subCategory").on("click", function() {

            if ($(this).attr("name") == "category") {

                // category : c={"id":23}

                const categoryArray = {
                    id: $(this).val()
                };

                let categoryJsonString = JSON.stringify(categoryArray);

                const encodedCategory = btoa(categoryJsonString); // encode a string

                $("#category").val(encodedCategory);


            } else {

                // category : c={"id":23, "subId":45}

                const categoryArray = {
                    id: $("#category-id").val()
                    , subId: $(this).val()
                };

                let categoryJsonString = JSON.stringify(categoryArray);

                const encodedCategory = btoa(categoryJsonString); // encode a string

                $("#category").val(encodedCategory);

            }

            $("[name=subCategory]").removeAttr("name");

            $("[name=category]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When a `sort` is selected.
        $(".sort").change(function() {

            // sorting : s=date_new_top

            $("#searchForm").submit();

        });


        // When a select field is selected.
        $("select.field, [type=radio].field, [type=checkbox].cmfield ").change(function() {

            let value = $(this).val();

            let fieldsArray = [];

            $('.field').each(function(i, obj) {

                // Considering `field` class is only given for `radio`, `checkbox_multiple` and `select` only. (not checkbox)

                let attr = $(obj).attr("type");

                if (attr == "radio") {

                    let keyId = $(obj).attr("name");

                    fieldsArray[keyId] = ($(".field[name='" + keyId + "']:checked").val() == null) ? '' : $(".field[name='" + keyId + "']:checked").val();

                } else if (attr == "checkbox_multiple") {

                    let keyId = $(obj).attr("checkbox_multiple_name");

                    let cmval = [];

                    $(".cmfield[name='" + keyId + "']:checked").each(function(i) {

                        cmval[i] = $(this).val();

                    });

                    fieldsArray[keyId] = cmval;

                } else {

                    let keyId = $(obj).attr("name");

                    fieldsArray[keyId] = ($(obj).val() == null) ? '' : $(obj).val();

                }
            });

            // category : c={"id":1, "fields":{"1":2, "3":4, "5":[1, 2, 3]}}

            const categoryArray = {
                id: $("#category-id").val()
                , subId: $("#sub-category-id").val()
                , fields: fieldsArray
            };

            let categoryJsonString = JSON.stringify(categoryArray);

            const encodedCategory = btoa(categoryJsonString); // encode a string

            $("#category").val(encodedCategory);

            $("[name=category]").removeAttr("name");

            $("#searchForm").submit();

            /*
            for (let x in fieldArray) {

                console.log(x + " - " + fieldArray[x]);

            }*/


        });


        // When `all of sri lanka` is selected.
        $("#clear-locations").on("click", function() {

            $("#location").val('');

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When `all categories` is selected.
        $("#clear-categories").on("click", function() {

            $("#category").val('');

            $("[name=c]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When `all of sri lanka` is selected.
        $(".setPrice, .membership").on("click", function() {

            $("#searchForm").submit();

        });


        // When form submitted.
        $("#searchForm").on("submit", function() {


            // price : p={"min":100,"max":2300}

            if ($("#minPrice").val() == "" && $("#maxPrice").val() == "") {

                $("[name=p]").removeAttr("name");
            } else {

                const priceArray = {
                    min: $("#minPrice").val()
                    , max: $("#maxPrice").val()
                };

                let priceJsonString = JSON.stringify(priceArray);

                const encodedPrice = btoa(priceJsonString); // encode a string

                $("#price").val(encodedPrice);
            }

            if ($("#category").val() == "") {

                $("[name=c]").removeAttr("name");
            }

        });

    });

</script>
@endsection
