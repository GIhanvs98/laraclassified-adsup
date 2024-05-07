@php
$admin ??= null;
$city ??= null;
$cat ??= null;

$cats ??= [];

// Keywords
$keywords = request()->get('q');
$keywords = (is_string($keywords)) ? $keywords : null;
$keywords = rawurldecode($keywords);

// Category
$qCategory = request()->get('c');
$qCategory = (is_numeric($qCategory) || is_string($qCategory)) ? $qCategory : null;
$qCategory = data_get($cat, 'id', $qCategory);

// Location
$qLocationId = 0;
$qAdminName = null;
if (!empty($city)) {
$qLocationId = data_get($city, 'id') ?? 0;
$qLocation = data_get($city, 'name');
} else {
$qLocationId = request()->get('l');
$qLocation = request()->get('location');
$qAdminName = request()->get('r');

$qLocationId = (is_numeric($qLocationId)) ? $qLocationId : null;
$qLocation = (is_string($qLocation)) ? $qLocation : null;
$qAdminName = (is_string($qAdminName)) ? $qAdminName : null;

if (!empty($qAdminName)) {
$qAdminName = data_get($admin, 'name', $qAdminName);
$isAdminCode = (bool)preg_match('#^[a-z]{2}\.(.+)$#i', $qAdminName);
$qLocation = !$isAdminCode ? t('area') . rawurldecode($qAdminName) : null;
}
}

// FilterBy
$qFilterBy = request()->get('filterBy');
$qFilterBy = (is_string($qFilterBy)) ? $qFilterBy : null;

$displayStatesSearchTip = config('settings.list.display_states_search_tip');
@endphp
@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.spacer', 'home.inc.spacer'])
<div class="container mb-2 py-3 serp-search-bar bg-white" style="border-radius: 4px 4px 0px 0px;">
    <form id="search" name="search" action="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" method="GET">
        @if (!empty($qFilterBy))
        <input type="hidden" name="filterBy" value="{{ $qFilterBy }}">
        @endif
        <div class="row m-0">
            <div class="col-xl-3 col-md-3 col-sm-12 col-12 pr-0 md:pr-[12px] mb-2" style="padding-left: 0px;">
                <div id="btnlocan" class="home-location-btn search-model-btn green-btn mr-0 md:mr-[5px]" data-bs-toggle="modal" data-bs-target="#exampleModal" style="width: 100% !important;">
                    <div style="width: 32px; height: 32px; fill: white; margin-right: 4px; display: inline-block;pointer-events: none; vertical-align: middle;">
                        <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                            <path d="M30 10c-8.4 0-15.3 6.7-15.3 15 0 4.7 2.3 10.2 6.8 16.5 3.3 4.5 6.5 7.7 6.6 7.8.5.5 1.1.7 1.8.7s1.3-.2 1.8-.7c.1-.1 3.4-3.3 6.6-7.8 4.5-6.2 6.8-11.8 6.8-16.5.2-8.3-6.7-15-15.1-15zm0 8.8c3.5 0 6.4 2.8 6.4 6.2s-2.9 6.2-6.4 6.2c-3.5 0-6.4-2.8-6.4-6.2s2.9-6.2 6.4-6.2"></path>
                        </svg>
                    </div>
                    Location
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-12 col-12 pr-0 md:pr-[12px] mb-2" style="padding-left: 0px;">
                <div id="btncalt" class="home-location-btn search-model-btn green-btn mr-0 md:mr-[5px]" data-bs-toggle="modal" data-bs-target="#categories" style="width: 100% !important;">
                    <div style="width: 32px; height: 32px; fill: white; margin-right: 4px; display: inline-block;pointer-events: none; vertical-align: middle;">
                        <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                            <path d="M47.834 26.901l-2.56-9.803c-.448-1.874-1.41-2.85-3.256-3.307l-9.655-2.599c-1.846-.456-3.134-.124-4.478 1.24L12.007 28.555c-1.343 1.364-1.343 3.596 0 4.96L25.85 47.57a3.427 3.427 0 0 0 4.885 0l15.878-16.122c1.344-1.364 1.67-2.672 1.22-4.547zm-12.62-2.894a3.546 3.546 0 0 1 0-4.96 3.418 3.418 0 0 1 4.885 0 3.545 3.545 0 0 1 0 4.96 3.417 3.417 0 0 1-4.886 0z"></path>
                        </svg>
                    </div>
                    Categories
                </div>
            </div>
            <div class="col-xl-6 col-md-6 col-sm-12 col-12 px-1 py-sm-1 bg-primary rounded search-field-btn search-input-field mb-2">
                <div class="row gx-1 gy-1">

                    <!-- <div class="col-xl-3 col-md-3 col-sm-12 col-12"> -->
                    <!-- <select name="c" id="catSearch" class="form-control selecter">
							<option value="" {{ ($qCategory=='') ? 'selected="selected"' : '' }}>
								{{ t('all_categories') }}
							</option>
							@if (!empty($cats))
								@foreach ($cats as $itemCat)
									<option value="{{ data_get($itemCat, 'id') }}" @selected($qCategory == data_get($itemCat, 'id'))>
										{{ data_get($itemCat, 'name') }}
									</option>
								@endforeach
							@endif
						</select> -->
                    <input class="form-control locinput input-rel searchtag-input" type="text" id="catSearch" name="c" hidden value="{{ $qCategory }}">

                    <!-- </div> -->

                    <div class="col-10">
                        <input name="q" class="form-control keyword" type="text" placeholder="{{ t('what') }}" value="{{ $keywords }}" style="border: 0px;margin-left: 6px;outline: none;box-shadow: none;">
                    </div>

                    <input type="hidden" id="rSearch" name="r" value="{{ $qAdminName }}">
                    <input type="hidden" id="lSearch" name="l" value="{{ $qLocationId }}">

                    <!-- <div class="col-xl-3 col-md-3 col-sm-12 col-12 search-col locationicon"> -->
                    @if ($displayStatesSearchTip)
                    <input class="form-control locinput input-rel searchtag-input" type="text" id="locSearch" name="location" placeholder="{{ t('where') }}" value="{{ $qLocation }}" data-bs-placement="top" data-bs-toggle="tooltipHover" title="{{ t('Enter a city name OR a state name with the prefix', ['prefix' => t('area')]) . t('State Name') }}" hidden>
                    @else
                    <input class="form-control locinput input-rel searchtag-input" type="text" id="locSearch" name="location" placeholder="{{ t('where') }}" value="{{ $qLocation }}" hidden>
                    @endif

                    <!-- </div> -->

                    <div id="all-search-field" class="col-2 green-btn-2">
                        <button class="btn btn-block btn-primary search-field-btn search-input-field-btn">
                            Search
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="categories" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px;">
                <h5 class="modal-title" id="categoriesLabel" style="display: none;">Select Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 10px;padding: 10px;"></button>
            </div>
            <div class="modal-body" style="padding:10px 40px 40px 40px;">
                <div class="row">
                    <div class="col">
                        <input type="text" id="tempCate" hidden value="">
                        <ul class="nav flex-column" style="border-bottom: 1px solid #d4ded9;">
                            <li style="padding: 0px;">
                                <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0rem !important;color: #2f3432;margin-bottom: 8px;font-size: 1rem !important;line-height: 1.33333333 !important;">Categories</h5>
                            </li>
                            @if (!empty($cats))
                            @foreach ($cats as $itemCat)
                            <li class="nav-item serp-locations parent-categories cat" cat-value="{{data_get($itemCat, 'id')}}" aria-current="page" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
                                <button class="nav-link button-list active" href="#" value="{{data_get($itemCat, 'id')}}">
                                    <img src="{{ data_get($itemCat, 'picture_url') }}" class="col lazyload img-fluid" style="height: 20px;margin-right: 8px;" alt="{{ data_get($itemCat, 'name') }}">
                                    {{data_get($itemCat, 'name')}}
                                </button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="col" id="subCategoryContainer-model">
                        <li style="padding: 0px;">
                            <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0rem !important;color: #2f3432;margin-bottom: 8px;font-size: 1rem !important;line-height: 1.33333333 !important;">Sub Categories</h5>
                        </li>
                        <ul class="nav flex-column" id="bbbbbbbbb" style="border-bottom: 1px solid #d4ded9;">
                            <ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="display: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px;">
                <h5 class="modal-title" id="exampleModalLabel" style="display: none;">Select City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 10px;padding: 10px;"></button>
            </div>
            <div class="modal-body" style="padding:10px 40px 40px 40px;">
                <div class="row">
                    <div class="col">
                        <input type="text" id="tempname" hidden value="">
                        <ul class="nav flex-column" style="border-bottom: 1px solid #d4ded9;">
                            <li>
                                <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px;">Districts</h5>
                            </li>
							<li class="nav-item serp-locations parent-locations disc" location-value="" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
								<button class="nav-link button-list active" aria-current="page" href="#" style="padding-left: 0px;">All of Sri Lanka</button>
							</li>
                            @if(isset($alldis))
                            @foreach ($alldis as $alldi)
                            <li class="nav-item serp-locations parent-locations disc" location-value="{{$alldi['originalID']}}" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
                                <button class="nav-link button-list active " aria-current="page" href="#" style="padding-left: 0px;">{{ str_replace(" District","", $alldi['name']) }}</button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="col" id="subLocationContainer-model">
                        <li>
                            <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px;">Cities</h5>
                        </li>
                        <ul class="nav flex-column" id="city" style="border-bottom: 1px solid #d4ded9;">

                            <ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="display: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

@section('after_scripts')
@parent
<script>
    $(document).ready(function() {

        $("#subCategoryContainer-model").hide();
        $("#subLocationContainer-model").hide();

        $(".parent-categories").on("click", function() {
            $(".parent-categories").removeClass("categories-parent-active");
            $(this).addClass("categories-parent-active");

            if ($("#bbbbbbbbb").text() == "" || $("#bbbbbbbbb").text() == null) {
                $("#subCategoryContainer-model").hide();
            } else {
                $("#subCategoryContainer-model").show();
            }

        });

        $(".parent-locations").on("click", function() {
            $(".parent-locations").removeClass("locations-parent-active");
            $(this).addClass("locations-parent-active");

            if ($("#city").text() == "" || $("#city").text() == null) {
                $("#subLocationContainer-model").hide();
            } else {
                $("#subLocationContainer-model").show();
            }

        });

        $('#locSearch').on('change', function() {
            if ($(this).val() == '') {
                $('#lSearch').val('');
                $('#rSearch').val('');
            }
        });


        $(".cat").click(function() {
            var APP_URL = {!!json_encode(url('/')) !!}
            //console.log(APP_URL);
            var code = $(this).attr("cat-value");
            $.ajax({
                type: 'GET'
                , url: APP_URL + '/ajax/getSubcategoryBycatID/' + code
                , dataType: 'json'
                , success: function(data) {
                    $("#bbbbbbbbb").html("");
                    var catName = JSON.parse(data.cat.name);
                    var Newccc = catName.en.split(" ").join('_');
                    var newobject = {
                        catID: data.cat.id
                        , catName: Newccc
                    , };
                    $("#bbbbbbbbb").append("<li class='nav-item serp-locations subcat ' style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;' subcat-value= " + JSON.stringify(newobject) + "><button class='nav-link button-list active'> All " + catName.en + "</button></li>");
                    data.data.forEach(function(element) {
                        var Name = JSON.parse(element.name);
                        var Newdd = Name.en.split(" ").join('_');
                        let newobjectOne = {
                            catID: element.id
                            , catName: Newdd
                        , };
                        $("#bbbbbbbbb").append("<li class='nav-item serp-locations subcat ' style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;' subcat-value= " + JSON.stringify(newobjectOne) + "><button class='nav-link button-list active'>" + Name.en + "</button></li>");

                        clickcataFirstButton();
                    });
                    document.getElementById("catSearch").value = code;
                    console.log(document.getElementById("tempCate").value, 'value');
                }
            });
        });

        function clickcataFirstButton(value) {
            $(".subcat").click(function() {
                var valueNew = JSON.parse($(this).attr("subcat-value"));
                console.log(valueNew, 'value');
                console.log(document.getElementById("tempCate").value, 'valueaaaaaaaaaaa');
                document.getElementById("catSearch").value = valueNew.catID;
                $('#categories').modal('hide');
                var nameaa = valueNew.catName.split("_").join(' ');
                $("#btncalt").html(nameaa);
            });
        }
        
        $(".disc").click(function() {
            document.getElementById("tempname").value = "";
            var code = $(this).attr("location-value");
            document.getElementById("lSearch").value = "";
            $.ajax({
                type: 'GET'
                , url: 'ajax/getCitesByDisID/' + code
                , dataType: 'json'
                , success: function(data) {
                    $("#city").html("");
                    var NameNew = JSON.parse(data.dis);
                    var newaaa = NameNew.en.split(" ").join('_');
                    console.log(NameNew);
                    var tempname = NameNew.en
                    let newobjecttwo = {
                        locID: data.disID
                        , lcaName: newaaa
                    , };
                    $("#city").append("<li class='nav-item serp-locations testCity' location-value=" + JSON.stringify(newobjecttwo) + " style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;'> <input type='text' class='templocalcalid serp-locations' hidden><button class='nav-link button-list active' value= " + JSON.stringify(newobjecttwo) + ">" + tempname + "</button></li>");
                    // $( "#city" ).append( "<li class='nav-item '><button class='nav-link button-list active testCity' value= "+'area:'+NameNew.en+">" + tempname + "</button></li>" );		
                    data.data.forEach(function(element) {
                        var Name = JSON.parse(element.name);
                        var newbbb = Name.en.split(" ").join('_');
                        let newobjecttree = {
                            locID: element.id
                            , lcaName: newbbb
                        , };
                        $("#city").append("<li class='nav-item serp-locations testCity' location-value= " + JSON.stringify(newobjecttree) + " style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;'><input type='text' class='templocalcalid' hidden value=" + JSON.stringify(newobjecttree) + "><button class='nav-link button-list active'>" + Name.en + "</button></li>");
                        // elemm.onclick = function() { alert('blah'); };
                    });

                    // var NameNew = JSON.parse(data.dis);
                    document.getElementById("tempname").value = data.disID;
                    // console.log(document.getElementById("tempname").value,'value');
                    // document.getElementById("locSearch").value = document.getElementById("tempname").value;
                    // document.getElementById("lSearch").value = data.disID;
                    clickFirstButton();
                    // clickFirstButton(NameNew, true);
                }
            });
        });

        function clickFirstButton() {
            $(".testCity").click(function() {
                var valuehw = JSON.parse($(this).attr("location-value"));
                console.log(valuehw, 'value');
                var nawename = valuehw.lcaName.split("_").join(' ');
                document.getElementById("lSearch").value = valuehw.locID;
                document.getElementById("locSearch").value = 'area:' + nawename;
                // document.getElementById("tempname").value = event.target.value;
                // var value = event.target.value;
                // console.log(document.getElementById("tempname").value,'valueaaaaaaaaaaa');
                // document.getElementById("locSearch").value = document.getElementById("tempname").value;
                $('#exampleModal').modal('hide');
                $("#btnlocan").html(nawename);
            });
            // }


        }
    });

</script>
@endsection
