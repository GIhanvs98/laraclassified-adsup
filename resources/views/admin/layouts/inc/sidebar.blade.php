@if (auth()->check())
@php
// Get plugins admin menu
$pluginsMenu = '';
$plugins = plugin_installed_list();
if (!empty($plugins)) {
foreach($plugins as $plugin) {
if (method_exists($plugin->class, 'getAdminMenu')) {
$pluginsMenu .= call_user_func($plugin->class . '::getAdminMenu');
}
}
}
@endphp
<style>
    #adminSidebar ul li span {
        text-transform: capitalize;
    }

</style>
<aside class="left-sidebar" id="adminSidebar">
    {{-- Sidebar scroll --}}
    <div class="scroll-sidebar">
        {{-- Sidebar navigation --}}
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item user-profile">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <img src="{{ auth()->user()->photo_url }}" alt="user">
                        <span class="hide-menu">{{ auth()->user()->name }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ admin_url('account') }}" class="sidebar-link p-0">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">My Account Settings</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ url('account') }}" class="sidebar-link p-0">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">My User Account</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ admin_url('logout') }}" class="sidebar-link p-0">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.logout') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="{{ admin_url('dashboard') }}" class="sidebar-link waves-effect waves-dark">
                        <i data-feather="home" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.dashboard') }}</span>
                    </a>
                </li>
                @if (
                auth()->user()->can('post-list')
                || auth()->user()->can('category-list')
                || auth()->user()->can('picture-list')
                || auth()->user()->can('post-type-list')
                || auth()->user()->can('field-list')
                || userHasSuperAdminPermissions()
                )
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link has-arrow waves-effect waves-dark">
                        <i data-feather="list"></i> <span class="hide-menu">{{ trans('admin.listings') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        @if (auth()->user()->can('post-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('posts') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.list') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('approve-ads') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('approve-ads') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Approve Ads</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('listing-durations') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('listing-durations') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Listing Duration</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('category-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('categories') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.categories') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('picture-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('pictures') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.pictures') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('post-type-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('p_types') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.listing types') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('ads-reports') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('ads-reports') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Ads Reports</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('field-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('custom_fields') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.custom fields') }}</span>
                            </a>
                        </li>
                        @endif

                        @if (auth()->user()->can('provinces') || auth()->user()->can('districts') || auth()->user()->can('cities') || userHasSuperAdminPermissions())
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link has-arrow waves-effect waves-dark">
                                    <i class="bi bi-geo-alt-fill"></i> <span class="hide-menu">Locations</span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level">

                                    @if (auth()->user()->can('provinces') || userHasSuperAdminPermissions())
                                    <li class="sidebar-item">
                                        <a href="{{ admin_url('provinces') }}" class="sidebar-link">
                                            <i class="mdi mdi-adjust"></i>
                                            <span class="hide-menu">Provinces</span>
                                        </a>
                                    </li>
                                    @endif

                                    @if (auth()->user()->can('districts') || userHasSuperAdminPermissions())
                                    <li class="sidebar-item">
                                        <a href="{{ admin_url('districts') }}" class="sidebar-link">
                                            <i class="mdi mdi-adjust"></i>
                                            <span class="hide-menu">Districts</span>
                                        </a>
                                    </li>
                                    @endif

                                    @if (auth()->user()->can('cities') || userHasSuperAdminPermissions())
                                    <li class="sidebar-item">
                                        <a href="{{ admin_url('cities') }}" class="sidebar-link">
                                            <i class="mdi mdi-adjust"></i>
                                            <span class="hide-menu">Cities</span>
                                        </a>
                                    </li>
                                    @endif

                                </ul>
                            </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if (
                auth()->user()->can('user-list')
                || auth()->user()->can('role-list')
                || auth()->user()->can('permission-list')
                || auth()->user()->can('gender-list')
                || userHasSuperAdminPermissions()
                )
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link has-arrow waves-effect waves-dark">
                        <i data-feather="users" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.users') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        @if (auth()->user()->can('user-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('users') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.list') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('role-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('roles') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.roles') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('users-memberships') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('user-memberships') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">User Memberships</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('membership-durations') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('membership-durations') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Membership Duration</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('permission-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('permissions') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.permissions') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->can('gender-list') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('genders') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">{{ trans('admin.titles') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                {{--@if (auth()->user()->can('payment-list') || userHasSuperAdminPermissions())
						<li class="sidebar-item">
							<a href="{{ admin_url('payments') }}" class="sidebar-link">
                <i data-feather="dollar-sign" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.payments') }}</span>
                </a>
                </li>
                @endif--}}

                @if (auth()->user()->can('transactions') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('transactions') }}" class="sidebar-link">
                        <i data-feather="dollar-sign" class="feather-icon"></i> <span class="hide-menu">Transactions</span>
                    </a>
                </li>
                @endif

                @if (auth()->user()->can('shops') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('shops') }}" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="margin: 0 8px 0 5px;height: 18px; width: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        <span class="hide-menu">Shops</span>
                    </a>
                </li>
                @endif

                @if (auth()->user()->can('page-list') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('pages') }}" class="sidebar-link">
                        <i data-feather="book-open" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.pages') }}</span>
                    </a>
                </li>
                @endif
                {!! $pluginsMenu !!}

                {{-- ======================================= --}}
                @if (
                auth()->user()->can('setting-list')
                || auth()->user()->can('language-list')
                || auth()->user()->can('home-section-list')
                || auth()->user()->can('meta-tag-list')
                || auth()->user()->can('package-list')
                || auth()->user()->can('payment-method-list')
                || auth()->user()->can('advertising-list')
                || auth()->user()->can('country-list')
                || auth()->user()->can('currency-list')
                || auth()->user()->can('blacklist-list')
                || auth()->user()->can('report-type-list')
                || userHasSuperAdminPermissions()
                )
                <li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">{{ trans('admin.configuration') }}</span>
                </li>

                @if (
                auth()->user()->can('setting-list')
                || auth()->user()->can('language-list')
                || auth()->user()->can('home-section-list')
                || auth()->user()->can('meta-tag-list')
                || auth()->user()->can('package-list')
                || auth()->user()->can('payment-method-list')
                || auth()->user()->can('advertising-list')
                || auth()->user()->can('country-list')
                || auth()->user()->can('currency-list')
                || auth()->user()->can('blacklist-list')
                || auth()->user()->can('report-type-list')
                || userHasSuperAdminPermissions()
                )
                <li class="sidebar-item">
                    <a href="#" class="has-arrow sidebar-link">
                        <i data-feather="settings" class="feather-icon"></i>
                        <span class="hide-menu">{{ trans('admin.settings') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        @include('admin.layouts.inc.sidebar.general-settings')
                        @include('admin.layouts.inc.sidebar.tableData-settings')
                    </ul>
                </li>
                @endif
                @endif

                @if (auth()->user()->can('plugin-list') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('plugins') }}" class="sidebar-link">
                        <i data-feather="package" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.plugins') }}</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->can('clear-cache') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('actions/clear_cache') }}" class="sidebar-link">
                        <i data-feather="refresh-cw" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.clear cache') }}</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->can('backup-list') || userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ admin_url('backups') }}" class="sidebar-link">
                        <i data-feather="hard-drive" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.backups') }}</span>
                    </a>
                </li>
                @endif

                @if (
                auth()->user()->can('system-info')
                || auth()->user()->can('maintenance')
                || userHasSuperAdminPermissions()
                )
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link has-arrow waves-effect waves-dark">
                        <i data-feather="alert-circle"></i> <span class="hide-menu">{{ trans('admin.system') }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        @if (
                        auth()->user()->can('maintenance') ||
                        userHasSuperAdminPermissions()
                        )
                        @if (app()->isDownForMaintenance())
                        @if (auth()->user()->can('maintenance') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('actions/maintenance/up') }}" data-bs-toggle="tooltip" title="{{ trans('admin.Leave Maintenance Mode') }}" class="sidebar-link confirm-simple-action">
                                <span class="hide-menu">{{ trans('admin.Live Mode') }}</span>
                            </a>
                        </li>
                        @endif
                        @else
                        @if (auth()->user()->can('maintenance') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('actions/maintenance/down') }}" data-bs-toggle="tooltip" title="{{ trans('admin.Put in Maintenance Mode') }}" class="sidebar-link confirm-simple-action">
                                <span class="hide-menu">{{ trans('admin.Maintenance') }}</span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @endif
                        @if (auth()->user()->can('system-info') || userHasSuperAdminPermissions())
                        <li class="sidebar-item">
                            <a href="{{ admin_url('system') }}" class="sidebar-link">
                                <span class="hide-menu">{{ trans('admin.system_info') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (userHasSuperAdminPermissions())
                <li class="sidebar-item">
                    <a href="{{ url('docs/api') }}" target="_blank" class="sidebar-link">
                        <i data-feather="book" class="feather-icon"></i> <span class="hide-menu">{{ trans('admin.api_docs') }}</span>
                    </a>
                </li>
                @endif

            </ul>
        </nav>

    </div>

</aside>
@endif
