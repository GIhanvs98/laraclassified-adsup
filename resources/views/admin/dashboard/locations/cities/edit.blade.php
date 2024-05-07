@extends('admin.layouts.master')

@section('header')
<div class="row page-titles">
    <div class="col-md-6 col-12 align-self-center">
        <h2 class="mb-0">
            <span class="text-capitalize">Cities <i class="bi bi-arrow-right"></i> {{ $city->name }} </span>
            <small id="tableInfo">edit</small>
        </h2>
    </div>
    <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
        <ol class="breadcrumb mb-0 p-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.locations.cities') }}" class="text-capitalize">Cities</a></li>
            <li class="breadcrumb-item active d-flex align-items-center">Edit City</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="controlls" style="display: flex; margin-bottom: 20px;">

            <a href="{{ route('admin.locations.cities') }}" class="btn btn-primary shadow" style="margin-right: 5px;">
                <i class="fa fa-angle-double-left"></i> Back to all cities
            </a>

        </div>

        <div class="card rounded">

            <livewire:locations.cities.edit-city :cityId="$city->id" />

        </div>

    </div>
</div>
@endsection

@section('after_styles')
<style>
    .error {
        --tw-text-opacity: 1;
        color: rgb(240 82 82/var(--tw-text-opacity));
        font-size: .75rem;
        line-height: 1rem;
    }

    .mr-3 {
        margin-right: 0.75rem
    }

    .inline {
        display: inline
    }

    .h-4 {
        height: 1rem
    }

    .w-4 {
        width: 1rem
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite
    }

    .text-white {
        --tw-text-opacity: 1;
        color: rgb(255 255 255 / var(--tw-text-opacity))
    }


    .flex {
        display: flex;
    }

    .justify-between {
        justify-content: space-between;
    }

    .align-middle {
        vertical-align: middle;
    }

    /*
! tailwindcss v3.3.5 | MIT License | https://tailwindcss.com
*/

    /*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/

    *,
    ::before,
    ::after {
        box-sizing: border-box;
        /* 1 */
        border-width: 0;
        /* 2 */
        border-style: solid;
        /* 2 */
        border-color: #e5e7eb;
        /* 2 */
    }

    /*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

    hr {
        height: 0;
        /* 1 */
        color: inherit;
        /* 2 */
        border-top-width: 1px;
        /* 3 */
    }

    /*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

    abbr:where([title]) {
        -webkit-text-decoration: underline dotted;
        text-decoration: underline dotted;
    }


    /*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

    button,
    input,
    optgroup,
    select,
    textarea {
        font-family: inherit;
        /* 1 */
        font-feature-settings: inherit;
        /* 1 */
        font-variation-settings: inherit;
        /* 1 */
        font-size: 100%;
        /* 1 */
        font-weight: inherit;
        /* 1 */
        line-height: inherit;
        /* 1 */
        color: inherit;
        /* 1 */
        margin: 0;
        /* 2 */
        padding: 0;
        /* 3 */
    }

    /*
Remove the inheritance of text transform in Edge and Firefox.
*/

    button,
    select {
        text-transform: none;
    }

    /*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

    button,
    [type='button'],
    [type='reset'],
    [type='submit'] {
        -webkit-appearance: button;
        /* 1 */
        background-color: transparent;
        /* 2 */
        background-image: none;
        /* 2 */
    }

    /*
Use the modern Firefox focus style for all focusable elements.
*/

    :-moz-focusring {
        outline: auto;
    }

    /*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

    :-moz-ui-invalid {
        box-shadow: none;
    }

    /*
Add the correct vertical alignment in Chrome and Firefox.
*/

    progress {
        vertical-align: baseline;
    }

    /*
Correct the cursor style of increment and decrement buttons in Safari.
*/

    ::-webkit-inner-spin-button,
    ::-webkit-outer-spin-button {
        height: auto;
    }

    /*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

    [type='search'] {
        -webkit-appearance: textfield;
        /* 1 */
        outline-offset: -2px;
        /* 2 */
    }

    /*
Remove the inner padding in Chrome and Safari on macOS.
*/

    ::-webkit-search-decoration {
        -webkit-appearance: none;
    }

    /*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

    ::-webkit-file-upload-button {
        -webkit-appearance: button;
        /* 1 */
        font: inherit;
        /* 2 */
    }

    /*
Add the correct display in Chrome and Safari.
*/

    summary {
        display: list-item;
    }

    /*
Removes the default spacing and border for appropriate elements.
*/

    blockquote,
    dl,
    dd,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    hr,
    figure,
    p,
    pre {
        margin: 0;
    }

    fieldset {
        margin: 0;
        padding: 0;
    }

    legend {
        padding: 0;
    }

    ol,
    ul,
    menu {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /*
Reset default styling for dialogs.
*/

    dialog {
        padding: 0;
    }

    /*
Prevent resizing textareas horizontally by default.
*/

    textarea {
        resize: vertical;
    }

    /*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

    input::placeholder,
    textarea::placeholder {
        opacity: 1;
        /* 1 */
        color: #9ca3af;
        /* 2 */
    }

    /*
Set the default cursor for buttons.
*/

    button,
    [role="button"] {
        cursor: pointer;
    }

    /*
Make sure disabled buttons don't get the pointer cursor.
*/

    :disabled {
        cursor: default;
    }

    /*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
This can trigger a poorly considered lint error in some tools but is included by design.
*/

    img,
    svg,
    video,
    canvas,
    audio,
    iframe,
    embed,
    object {
        display: block;
        /* 1 */
        vertical-align: middle;
        /* 2 */
    }

    /*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

    img,
    video {
        max-width: 100%;
        height: auto;
    }

    /* Make elements with the HTML hidden attribute stay hidden by default */

    [hidden] {
        display: none;
    }

    *,
    ::before,
    ::after {
        --tw-border-spacing-x: 0;
        --tw-border-spacing-y: 0;
        --tw-translate-x: 0;
        --tw-translate-y: 0;
        --tw-rotate: 0;
        --tw-skew-x: 0;
        --tw-skew-y: 0;
        --tw-scale-x: 1;
        --tw-scale-y: 1;
        --tw-pan-x: ;
        --tw-pan-y: ;
        --tw-pinch-zoom: ;
        --tw-scroll-snap-strictness: proximity;
        --tw-gradient-from-position: ;
        --tw-gradient-via-position: ;
        --tw-gradient-to-position: ;
        --tw-ordinal: ;
        --tw-slashed-zero: ;
        --tw-numeric-figure: ;
        --tw-numeric-spacing: ;
        --tw-numeric-fraction: ;
        --tw-ring-inset: ;
        --tw-ring-offset-width: 0px;
        --tw-ring-offset-color: #fff;
        --tw-ring-color: rgb(59 130 246 / 0.5);
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
        --tw-shadow: 0 0 #0000;
        --tw-shadow-colored: 0 0 #0000;
        --tw-blur: ;
        --tw-brightness: ;
        --tw-contrast: ;
        --tw-grayscale: ;
        --tw-hue-rotate: ;
        --tw-invert: ;
        --tw-saturate: ;
        --tw-sepia: ;
        --tw-drop-shadow: ;
        --tw-backdrop-blur: ;
        --tw-backdrop-brightness: ;
        --tw-backdrop-contrast: ;
        --tw-backdrop-grayscale: ;
        --tw-backdrop-hue-rotate: ;
        --tw-backdrop-invert: ;
        --tw-backdrop-opacity: ;
        --tw-backdrop-saturate: ;
        --tw-backdrop-sepia:
    }

    ::backdrop {
        --tw-border-spacing-x: 0;
        --tw-border-spacing-y: 0;
        --tw-translate-x: 0;
        --tw-translate-y: 0;
        --tw-rotate: 0;
        --tw-skew-x: 0;
        --tw-skew-y: 0;
        --tw-scale-x: 1;
        --tw-scale-y: 1;
        --tw-pan-x: ;
        --tw-pan-y: ;
        --tw-pinch-zoom: ;
        --tw-scroll-snap-strictness: proximity;
        --tw-gradient-from-position: ;
        --tw-gradient-via-position: ;
        --tw-gradient-to-position: ;
        --tw-ordinal: ;
        --tw-slashed-zero: ;
        --tw-numeric-figure: ;
        --tw-numeric-spacing: ;
        --tw-numeric-fraction: ;
        --tw-ring-inset: ;
        --tw-ring-offset-width: 0px;
        --tw-ring-offset-color: #fff;
        --tw-ring-color: rgb(59 130 246 / 0.5);
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
        --tw-shadow: 0 0 #0000;
        --tw-shadow-colored: 0 0 #0000;
        --tw-blur: ;
        --tw-brightness: ;
        --tw-contrast: ;
        --tw-grayscale: ;
        --tw-hue-rotate: ;
        --tw-invert: ;
        --tw-saturate: ;
        --tw-sepia: ;
        --tw-drop-shadow: ;
        --tw-backdrop-blur: ;
        --tw-backdrop-brightness: ;
        --tw-backdrop-contrast: ;
        --tw-backdrop-grayscale: ;
        --tw-backdrop-hue-rotate: ;
        --tw-backdrop-invert: ;
        --tw-backdrop-opacity: ;
        --tw-backdrop-saturate: ;
        --tw-backdrop-sepia:
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0
    }

    .relative {
        position: relative
    }

    .mb-4 {
        margin-bottom: 1rem
    }

    .inline-flex {
        display: inline-flex
    }

    .h-6 {
        height: 1.5rem
    }

    .w-11 {
        width: 2.75rem
    }

    .cursor-pointer {
        cursor: pointer
    }

    .items-center {
        align-items: center
    }

    .rounded-full {
        border-radius: 9999px
    }

    .bg-gray-200 {
        --tw-bg-opacity: 1;
        background-color: rgb(229 231 235 / var(--tw-bg-opacity))
    }

    .after\:absolute::after {
        content: var(--tw-content);
        position: absolute
    }

    .after\:left-\[2px\]::after {
        content: var(--tw-content);
        left: 2px
    }

    .after\:top-0::after {
        content: var(--tw-content);
        top: 0px
    }

    .after\:top-0\.5::after {
        content: var(--tw-content);
        top: 0.125rem
    }

    .after\:h-5::after {
        content: var(--tw-content);
        height: 1.25rem
    }

    .after\:w-5::after {
        content: var(--tw-content);
        width: 1.25rem
    }

    .after\:rounded-full::after {
        content: var(--tw-content);
        border-radius: 9999px
    }

    .after\:border::after {
        content: var(--tw-content);
        border-width: 1px
    }

    .after\:border-gray-300::after {
        content: var(--tw-content);
        --tw-border-opacity: 1;
        border-color: rgb(209 213 219 / var(--tw-border-opacity))
    }

    .after\:bg-white::after {
        content: var(--tw-content);
        --tw-bg-opacity: 1;
        background-color: rgb(255 255 255 / var(--tw-bg-opacity))
    }

    .after\:transition-all::after {
        content: var(--tw-content);
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms
    }

    .after\:content-\[\'\'\]::after {
        --tw-content: '';
        content: var(--tw-content)
    }

    .peer:checked~.peer-checked\:bg-blue-600 {
        --tw-bg-opacity: 1;
        background-color: rgb(37 99 235 / var(--tw-bg-opacity))
    }

    .peer:checked~.peer-checked\:after\:translate-x-full::after {
        content: var(--tw-content);
        --tw-translate-x: 100%;
        transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
    }

    .peer:checked~.peer-checked\:after\:border-white::after {
        content: var(--tw-content);
        --tw-border-opacity: 1;
        border-color: rgb(255 255 255 / var(--tw-border-opacity))
    }

    .peer:focus~.peer-focus\:ring-4 {
        --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
        --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
        box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
    }

    .peer:focus~.peer-focus\:ring-blue-300 {
        --tw-ring-opacity: 1;
        --tw-ring-color: rgb(147 197 253 / var(--tw-ring-opacity))
    }

    .btn-primary {
        color: #fff;
        background-color: #7460ee;
        border-color: #7460ee;
    }

    .btn-secondary {
        color: #fff;
        background-color: #545b62;
        border-color: #545b62;
    }

</style>
@endsection

@section('after_scripts')

@endsection

