<!-- HEADER-SECTION-START -->
<header class="header-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-8">
                <div class="header-left">
                    <div class="mobil-burs">
                        <div class="menu-toggle">
                            <div class="menu-btn d-block d-lg-none">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <div class="logo-item">
                            <a href="{{ route('home') }}"><img src="{{ config('settings.app.logo_url') }}" alt="{{ strtolower(config('settings.app.name')) }}"></a>
                        </div>
                    </div>
                    <div class="ads-item d-none d-lg-block">
                        <ul>
                            <li><a href="{{ route('search') }}">All ads</a></li>
                            <li><a href="{{ route('memberships') }}">Member</a></li>
                        </ul>
                    </div>
                    <div class="language-item d-none d-lg-block">
                        <div class="flex items-center md:ml-[15px] notranslate" style="height: 100%;">
                            <div id="translate-to-english" class="left-border-translate-bt header-bt"
                                onclick="doGTranslate('en|en'); checkLanguage();" title="EN">English</div>
                            <div id="translate-to-sinhala" class="no-b-radius-translate-bt header-bt"
                                onclick="doGTranslate('en|si'); checkLanguage();" title="SI">Sinhala</div>
                            <div id="translate-to-tamil" class="right-border-translate-bt header-bt"
                                onclick="doGTranslate('en|ta'); checkLanguage();" title="TA">Tamil</div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-4">
                <nav class="main-nav">
                    <ul>
                        <li>
                            <livewire:header-messages-link />
                        </li>
                        <li>
                            <livewire:header-account-link />
                        </li>
                        <li class="d-none d-lg-block"><a href="{{ route('post-ad.index') }}">post your ad</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- HEADER-SECTION-END -->

<!--MOBIL-MENU-START-->
<div class="sidebar-menu d-block d-lg-none">
    <div class="menu-header d-flex align-items-center justify-content-between">
        <div class="mobil-brand">
            <a href="{{ route('home') }}" style="max-width: 150px;display: inline-block;">
                <img src="images/logo.png" alt="logo">
            </a>
        </div>
        <div class="close-btn">
            <span class="close-icon"></span>
        </div>
    </div>
    <div class="menu-wrap">
        <div class="menu-item"><a href="{{ route('search') }}">All ads</a></div>
        <div class="menu-item"><a href="{{ route('memberships') }}">Member</a></div>
    </div>
    <div class="mobil-lang" style="padding: 0 15px 0 15px;">
        <div class="flex items-center md:ml-[15px] notranslate" style="height: 100%;">
            <div id="translate-to-english-small" class="left-border-translate-bt header-bt"
                onclick="doGTranslate('en|en'); checkLanguage();" title="EN">English</div>
            <div id="translate-to-sinhala-small" class="no-b-radius-translate-bt header-bt"
                onclick="doGTranslate('en|si'); checkLanguage();" title="SI">Sinhala</div>
            <div id="translate-to-tamil-small" class="right-border-translate-bt header-bt"
                onclick="doGTranslate('en|ta'); checkLanguage();" title="TA">Tamil</div>
        </div>
    </div>
</div>
<!--MOBIL-MENU-END-->

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

            document.querySelector("#translate-to-english-small").classList.remove("left-border-translate-bt");
            document.querySelector("#translate-to-sinhala-small").classList.remove("right-border-translate-bt");
            document.querySelector("#translate-to-tamil-small").classList.add("right-border-translate-bt");
            document.querySelector("#translate-to-sinhala-small").classList.add("left-border-translate-bt");
            document.querySelector("#translate-to-tamil-small").style.display = "block";
            document.querySelector("#translate-to-sinhala-small").style.display = "block";
            document.querySelector("#translate-to-english-small").style.display = "none";
        }

        if (lang == "/en/si") {
            document.querySelector("#translate-to-sinhala").classList.remove("left-border-translate-bt");
            document.querySelector("#translate-to-sinhala").classList.remove("right-border-translate-bt");
            document.querySelector("#translate-to-tamil").classList.add("right-border-translate-bt");
            document.querySelector("#translate-to-english").classList.add("left-border-translate-bt");
            document.querySelector("#translate-to-english").style.display = "block";
            document.querySelector("#translate-to-tamil").style.display = "block";
            document.querySelector("#translate-to-sinhala").style.display = "none";

            document.querySelector("#translate-to-sinhala-small").classList.remove("left-border-translate-bt");
            document.querySelector("#translate-to-sinhala-small").classList.remove("right-border-translate-bt");
            document.querySelector("#translate-to-tamil-small").classList.add("right-border-translate-bt");
            document.querySelector("#translate-to-english-small").classList.add("left-border-translate-bt");
            document.querySelector("#translate-to-english-small").style.display = "block";
            document.querySelector("#translate-to-tamil-small").style.display = "block";
            document.querySelector("#translate-to-sinhala-small").style.display = "none";
        }

        if (lang == "/en/ta") {
            document.querySelector("#translate-to-tamil").classList.remove("right-border-translate-bt");
            document.querySelector("#translate-to-english").classList.add("left-border-translate-bt");
            document.querySelector("#translate-to-sinhala").classList.add("right-border-translate-bt");
            document.querySelector("#translate-to-english").style.display = "block";
            document.querySelector("#translate-to-sinhala").style.display = "block";
            document.querySelector("#translate-to-tamil").style.display = "none";

            document.querySelector("#translate-to-tamil-small").classList.remove("right-border-translate-bt");
            document.querySelector("#translate-to-english-small").classList.add("left-border-translate-bt");
            document.querySelector("#translate-to-sinhala-small").classList.add("right-border-translate-bt");
            document.querySelector("#translate-to-english-small").style.display = "block";
            document.querySelector("#translate-to-sinhala-small").style.display = "block";
            document.querySelector("#translate-to-tamil-small").style.display = "none";
        }

    }

    checkLanguage();
</script>