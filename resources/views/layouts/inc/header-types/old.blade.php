<nav class="navbar position-relative navbar-site navbar-light header navbar-expand-md" role="navigation">
    <div class="container"
        style="background-image: linear-gradient(var(--primary-color), var(--primary-color)) !important;">

        <div class="navbar-identity p-sm-0">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="navbar-brand logo logo-title">
                <img src="{{ config('settings.app.logo_url') }}" alt="{{ strtolower(config('settings.app.name')) }}"
                    class="main-logo" data-bs-placement="bottom" data-bs-toggle="tooltip" title="{!! $logoLabel !!}" />
            </a>
            {{-- Toggle Nav (Mobile) --}}
            <button class="navbar-toggler -toggler float-end" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false"
                aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30" focusable="false">
                    <title>{{ t('Menu') }}</title>
                    <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10"
                        d="M4 7h22M4 15h22M4 23h22"></path>
                </svg>
            </button>
            {{-- Country Flag (Mobile) --}}
            @if ($multiCountriesIsEnabled)
            @if (!empty(config('country.icode')))
            @if (file_exists(public_path() . '/images/flags/24/' . config('country.icode') . '.png'))
            <button class="flag-menu country-flag d-md-none d-sm-block d-none btn btn-default float-end"
                href="#selectCountry" data-bs-toggle="modal">
                <img src="{{ url('images/flags/24/' . config('country.icode') . '.png') . getPictureVersion() }}"
                    alt="{{ config('country.name') }}" style="float: left;">
                <span class="caret d-none"></span>
            </button>
            @endif
            @endif
            @endif
        </div>

        <div class="navbar-collapse collapse" id="navbarsDefault">
            <ul class="nav navbar-nav me-md-auto navbar-left">
                @if (config('settings.list.display_browse_listings_link'))
                <li class="nav-item d-md-block d-sm-block d-block">
                    @php
                    $currDisplay = config('settings.list.display_mode');
                    $browseListingsIconClass = 'fas fa-th-large';
                    if ($currDisplay == 'make-list') {
                    $browseListingsIconClass = 'fas fa-th-list';
                    }
                    if ($currDisplay == 'make-compact') {
                    $browseListingsIconClass = 'fas fa-bars';
                    }
                    @endphp
                    <a href="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}"
                        class="nav-link bold-500 header-bt text-sm">
                        {{ t('Browse Listings') }}
                    </a>
                </li>
                <li class="nav-item d-md-block d-sm-block d-block">
                    <a href="{{ url('/memberships') }}" class="nav-link bold-500 header-bt text-sm">
                        Member
                    </a>
                </li>
                @endif

                <li class="nav-item d-md-block d-sm-block d-block">

                    <div class="flex items-center md:ml-[15px] notranslate" id="wsmu-google-translate"
                        style="height: 100%;">
                        <div id="translate-to-english" class="left-border-translate-bt header-bt"
                            onclick="doGTranslate('en|en'); checkLanguage();" title="EN">English</div>
                        <div id="translate-to-sinhala" class="no-b-radius-translate-bt header-bt"
                            onclick="doGTranslate('en|si'); checkLanguage();" title="SI">Sinhala</div>
                        <div id="translate-to-tamil" class="right-border-translate-bt header-bt"
                            onclick="doGTranslate('en|ta'); checkLanguage();" title="TA">Tamil</div>
                    </div>

                    <script>
                        function getCookie(cname) {
                            let name = cname + "=";
                            let decodedCookie = decodeURIComponent(document.cookie);
                            let ca = decodedCookie.split(';');
                            for (let i = 0; i < ca.length; i++) {
                                let c = ca[i];
                                while (c.charAt(0) == ' ') {
                                    c = c.substring(1);
                                }
                                if (c.indexOf(name) == 0) {
                                    return c.substring(name.length, c.length);
                                }
                            }
                            return "";
                        }

                        function checkCookie() {
                            let lang = getCookie("googtrans");

                            if (lang != "") {
                                return lang;
                            } else {
                                return "en";
                            }
                        }

                        function checkLanguage() {

                            // no-b-radius-translate-bt

                            let lang = checkCookie();

                            //console.log(lang);

                            if (lang == "en") {
                                document.querySelector("#translate-to-english").classList.remove("left-border-translate-bt");
                                document.querySelector("#translate-to-sinhala").classList.remove("right-border-translate-bt");
                                document.querySelector("#translate-to-tamil").classList.add("right-border-translate-bt");
                                document.querySelector("#translate-to-sinhala").classList.add("left-border-translate-bt");
                                document.querySelector("#translate-to-tamil").style.display = "block";
                                document.querySelector("#translate-to-sinhala").style.display = "block";
                                document.querySelector("#translate-to-english").style.display = "none";
                            }

                            if (lang == "/en/si") {
                                document.querySelector("#translate-to-sinhala").classList.remove("left-border-translate-bt");
                                document.querySelector("#translate-to-sinhala").classList.remove("right-border-translate-bt");
                                document.querySelector("#translate-to-tamil").classList.add("right-border-translate-bt");
                                document.querySelector("#translate-to-english").classList.add("left-border-translate-bt");
                                document.querySelector("#translate-to-english").style.display = "block";
                                document.querySelector("#translate-to-tamil").style.display = "block";
                                document.querySelector("#translate-to-sinhala").style.display = "none";
                            }

                            if (lang == "/en/ta") {
                                document.querySelector("#translate-to-tamil").classList.remove("right-border-translate-bt");
                                document.querySelector("#translate-to-english").classList.add("left-border-translate-bt");
                                document.querySelector("#translate-to-sinhala").classList.add("right-border-translate-bt");
                                document.querySelector("#translate-to-english").style.display = "block";
                                document.querySelector("#translate-to-sinhala").style.display = "block";
                                document.querySelector("#translate-to-tamil").style.display = "none";
                            }

                        }

                        checkLanguage();
                    </script>

                </li>

                {{-- Country Flag --}}
                @if (config('settings.geo_location.show_country_flag'))
                @if (!empty(config('country.icode')))
                @if (file_exists(public_path() . '/images/flags/32/' . config('country.icode') . '.png'))
                <li class="flag-menu country-flag d-md-block d-sm-none d-none nav-item" data-bs-toggle="tooltip"
                    data-bs-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}" {!!
                    $multiCountriesLabel !!}>
                    @if ($multiCountriesIsEnabled)
                    <!--a class="nav-link p-0" data-bs-toggle="modal" data-bs-target="#selectCountry">
                                            <img class="flag-icon mt-1"
                                                 src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}"
                                                 alt="{{ config('country.name') }}"
                                            >
                                            <span class="caret d-lg-block d-md-none d-sm-none d-none float-end mt-3 mx-1"></span>
                                        </a-->
                    @else
                    <!--a class="p-0" style="cursor: default;">
                                            <img class="flag-icon"
                                                 src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}"
                                                 alt="{{ config('country.name') }}"
                                            >
                                        </a-->
                    @endif
                </li>
                @endif
                @endif
                @endif
            </ul>

            <ul class="nav navbar-nav ms-auto navbar-right">
                @if (!auth()->check())
                <li class="nav-item dropdown no-arrow open-on-hover d-md-block d-sm-none d-none">
                    {{--<a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                        <!--i class="bi bi-chevron-down"></i-->
                    </a>--}}
                    <ul {{--id="authDropdownMenu" class="dropdown-menu user-menu shadow-sm" --}} style="display: flex;">
                        <li>
                            @if (config('settings.security.login_open_in_modal'))
                            <a href="#quickLogin" class="nav-link header-bt text-sm" data-bs-toggle="modal"
                                style="text-transform: capitalize;"><i class="fa fa-sign-in-alt"></i>&nbsp;{{ t('login')
                                }}</a>
                            @else
                            <a href="{{ \App\Helpers\UrlGen::login() }}" class="nav-link header-bt text-sm"
                                style="text-transform: capitalize;"><i class="fa fa-sign-in-alt"></i>&nbsp;{{ t('login')
                                }}</a>
                            @endif
                        </li>
                        <li>
                            <a href="{{ \App\Helpers\UrlGen::register() }}" class="nav-link header-bt text-sm"
                                style="text-transform: capitalize;"><i class="far fa-user"></i>&nbsp;Register{{--
                                t('signup') --}}</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item d-md-none d-sm-block d-block">
                    @if (config('settings.security.login_open_in_modal'))
                    <a href="#quickLogin" class="nav-link header-bt text-sm" data-bs-toggle="modal"
                        style="text-transform: capitalize;"><i class="fa fa-sign-in-alt"></i>&nbsp;{{ t('login') }}</a>
                    @else
                    <a href="{{ \App\Helpers\UrlGen::login() }}" class="nav-link header-bt text-sm"
                        style="text-transform: capitalize;"><i class="fa fa-sign-in-alt"></i>&nbsp;{{ t('login') }}</a>
                    @endif
                </li>
                <li class="nav-item d-md-none d-sm-block d-block">
                    <a href="{{ \App\Helpers\UrlGen::register() }}" class="nav-link header-bt text-sm"
                        style="text-transform: capitalize;"><i class="far fa-user"></i>&nbsp;Register{{-- t('signup')
                        --}}</a>
                </li>
                @else
                <li class="nav-item dropdown no-arrow {{--open-on-hover--}}">

                    @if (auth()->user()->can(App\Models\Permission::getStaffPermissions()))
                    <a href="{{ admin_url('/') }}" class="{{-- dropdown-toggle --}}nav-link text-white bold-md text-sm"
                        style="margin: 10px 0px;">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ auth()->user()->name }}</span>
                        <span
                            class="badge badge-pill badge-important count-threads-with-new-messages d-lg-inline-block d-md-none">0</span>
                        <!--i class="bi bi-chevron-down"></i-->
                    </a>
                    @else
                    <a href="{{ url('account') }}" class="{{-- dropdown-toggle --}} nav-link text-white bold-md text-sm"
                        style="margin: 10px 0px;">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ auth()->user()->name }}</span>
                        <span
                            class="badge badge-pill badge-important count-threads-with-new-messages d-lg-inline-block d-md-none">0</span>
                        <!--i class="bi bi-chevron-down"></i-->
                    </a>
                    @endif

                    {{--<ul id="userMenuDropdown" class="dropdown-menu user-menu shadow-sm">
                        @if (isset($userMenu) && !empty($userMenu))
                        @php
                        $menuGroup = '';
                        $dividerNeeded = false;
                        @endphp
                        @foreach($userMenu as $key => $value)
                        @continue(!$value['inDropdown'])
                        @php
                        if ($menuGroup != $value['group']) {
                        $menuGroup = $value['group'];
                        if (!empty($menuGroup) && !$loop->first) {
                        $dividerNeeded = true;
                        }
                        } else {
                        $dividerNeeded = false;
                        }
                        @endphp
                        @if ($dividerNeeded)
                        <li class="dropdown-divider"></li>
                        @endif
                        <li
                            class="dropdown-item{{ (isset($value['isActive']) && $value['isActive']) ? ' active' : '' }}">
                            <a href="{{ $value['url'] }}">
                                <i class="{{ $value['icon'] }}"></i> {{ $value['name'] }}
                                @if (isset($value['countVar'], $value['countCustomClass']) && !empty($value['countVar'])
                                && !empty($value['countCustomClass']))
                                <span class="badge badge-pill badge-important{{ $value['countCustomClass'] }}">0</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>--}}
                </li>
                @endif

                @if (config('plugins.currencyexchange.installed'))
                @include('currencyexchange::select-currency')
                @endif

                @if (config('settings.single.pricing_page_enabled') == '2')
                <li class="nav-item pricing">
                    <a href="{{ \App\Helpers\UrlGen::pricing() }}" class="nav-link">
                        <i class="fas fa-tags"></i> {{ t('pricing_label') }}
                    </a>
                </li>
                @endif

                <?php
                $addListingUrl = \App\Helpers\UrlGen::addPost();
                $addListingAttr = '';
                if (!auth()->check()) {
                    if (config('settings.single.guests_can_post_listings') != '1') {
                        $addListingUrl = '#quickLogin';
                        $addListingAttr = ' data-bs-toggle="modal"';
                    }
                }
                if (config('settings.single.pricing_page_enabled') == '1') {
                    $addListingUrl = \App\Helpers\UrlGen::pricing();
                    $addListingAttr = '';
                }
                ?>

                @includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.menu.select-language',
                'layouts.inc.menu.select-language'])

                <li class="nav-item postadd flex justify-center items-center">
                    <a class="btn btn-block btn-border btn-listing" href="{{ route('post-ad.index') }}">
                        <!--i class="far fa-edit"></i--> {{ t('Create Listing') }}
                    </a>
                </li>
            </ul>
        </div>


    </div>
</nav>