<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <div class="d-flex align-items-center mt-3">
                    <img src="{{ asset(config('app.companyInfo.logo_transparent')) }}" class="rounded" alt=""
                        height="30">
                </div>
            </span>
            <span class="logo-lg">
                <div class="d-flex align-items-center mt-3">
                    <img src="{{ asset(config('app.companyInfo.logo_transparent')) }}" class="rounded" alt=""
                        height="70">
                </div>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <div class="d-flex align-items-center mt-3">
                    <img src="{{ asset(config('app.companyInfo.logo_transparent')) }}" class="rounded" alt=""
                        height="40">
                </div>
            </span>
            <span class="logo-lg">
                <div class="d-flex align-items-center mt-3">
                    <img src="{{ asset(config('app.companyInfo.logo_transparent')) }}" class="rounded" alt=""
                        height="70" style="transform: scale(1);">
                </div>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                {{-- <li class="menu-title"><span data-key="t-menu">Menu</span></li> --}}
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="dashboard-analytics.html" class="nav-link" data-key="t-analytics"> Analytics </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-crm.html" class="nav-link" data-key="t-crm"> CRM </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.html" class="nav-link" data-key="t-ecommerce"> Ecommerce </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-crypto.html" class="nav-link" data-key="t-crypto"> Crypto </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-projects.html" class="nav-link" data-key="t-projects"> Projects </a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu --> --}}

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ request()->url() == route('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class=" ri-dashboard-line"></i> <span data-key="t-landing">Dashboard </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('customer') }}"
                        class="nav-link menu-link  {{ Request::routeIs('customer*') ? 'active' : '' }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Customers</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('warehouses*') ? 'active' : '' }}"
                        href="{{ route('warehouses.index') }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Warehouses</span>
                    </a>
                </li>

                    <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('roles*') ? 'active' : '' }}"
                        href="{{ route('roles.index') }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Roles</span>
                    </a>
                </li>

                 <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('user-warehouse-permissions*') ? 'active' : '' }}"
                        href="{{ route('user-warehouse-permissions.index') }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Permissions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('post*') ? 'active' : '' }}"
                        href="{{ route('post') }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Posts</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('users*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="ri-user-3-line"></i><span data-key="t-landing">Users</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('brand*') ? 'active' : '' }}"
                        href="{{ route('brand') }}">
                        <i class=" ri-codepen-line"></i> <span data-key="t-landing">Brands</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link menu-link {{ Request::routeIs('category*') ? 'active' : '' }}"
                        href="{{ route('category') }}">
                        <i class="ri-stack-line"></i> <span data-key="t-landing">Categories</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link menu-link {{ Request::routeIs('subcategory*') ? 'active' : '' }}"
                        href="{{ route('subcategory') }}">
                        <i class="ri-stack-line"></i> <span data-key="t-landing">SubCategories</span>
                    </a>
                </li>
                {{-- <li class="menu-title"><span data-key="t-menu">Manage Products</span></li> --}}
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link  {{ request()->url() == route('product.color') ? 'active' : '' }}"
                        href="{{ route('product.color') }}">
                        <i class="ri-paint-line"></i> <span data-key="t-landing">Colors</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link  {{ request()->url() == route('product.size') ? 'active' : '' }}"
                        href="{{ route('product.size') }}">
                        <i class="ri-shape-2-line"></i> <span data-key="t-landing">Sizes</span>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('variation*') ? 'active' : '' }}"
                        href="{{ route('variation') }}">
                        <i class="ri-box-2-line"></i> <span data-key="t-landing">Variations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('product*') ? 'active' : '' }}"
                        href="{{ route('product') }}">
                        <i class="ri-product-hunt-line"></i> <span data-key="t-landing">Products</span>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('wholesale*') ? 'active' : '' }}"
                        href="{{ route('wholesale') }}">
                        <i class="ri-discount-percent-line"></i><span data-key="t-landing">Wholesale</span>
                    </a>
                </li> --}}


                 <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('banner*') ? 'active' : '' }}"
                        href="{{ route('banner') }}">
                        <i class="ri-slideshow-4-line"></i> <span data-key="t-landing">Banners</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('payment*') ? 'active' : '' }}"
                        href="{{ route('payment') }}">
                        <i class="ri-bank-card-2-line"></i> <span data-key="t-landing">Payments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('region*') ? 'active' : '' }}"
                        href="{{ route('region') }}">
                        <i class="mdi mdi-truck-check-outline"></i> <span data-key="t-landing">Regions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('deliveryfee*') ? 'active' : '' }}"
                        href="{{ route('deliveryfee') }}">
                        <i class="mdi mdi-truck-delivery-outline"></i> <span data-key="t-landing">Delivery Fee</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('contactDetail*') ? 'active' : '' }}"
                        href="{{ route('contactDetail') }}">
                        <i class="ri-phone-find-line"></i> <span data-key="t-landing">Contact Detail</span>
                    </a>
                </li>

                 <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('note*') ? 'active' : '' }}"
                        href="{{ route('note') }}">
                        <i class="ri-file-text-line"></i> <span data-key="t-landing">Note</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('versionSetting*') ? 'active' : '' }}"
                        href="{{ route('versionSetting') }}">
                        <i class="ri-settings-3-line"></i> <span data-key="t-landing">Version Setting</span>
                    </a>
                </li>

                <li class="menu-title"><span data-key="t-menu">Manage Orders</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link   {{ Request::routeIs('orderSuccessMessage*') ? 'active' : '' }}"
                        href="{{ route('orderSuccessMessage') }}">
                        <i class="ri-message-2-line"></i> <span data-key="t-landing">Order Message</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link  {{ Request::routeIs('order.refund.list*') ? 'active' : '' }}"
                        href="{{ route('order.refund.list') }}">
                        <i class="ri-refund-2-line"></i> <span data-key="t-landing">Refund Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::routeIs('order*') && !Request::routeIs('order.refund*') && !Request::routeIs('orderSuccessMessage*') ? 'active' : '' }}"
                        href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidebarDashboards">
                        <i class="mdi mdi-shopping-outline"></i><span data-key="t-dashboards">Orders</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is('orders*') && !Request::is('orders/refund/all') ? 'show' : '' }}"
                        id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'pending') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'pending') ? 'active' : '' }}"
                                    data-key="t-analytics"> Pending Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'confirm') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'confirm') ? 'active' : '' }}"
                                    data-key="t-analytics"> Confirmed Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'processing') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'processing') ? 'active' : '' }}"
                                    data-key="t-analytics"> Processing Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'delivered') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'delivered') ? 'active' : '' }}"
                                    data-key="t-analytics"> Delivered Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'complete') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'complete') ? 'active' : '' }}"
                                    data-key="t-analytics"> Complete Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orderByStatus', 'cancel') }}"
                                    class="nav-link {{ request()->url() == route('orderByStatus', 'cancel') ? 'active' : '' }}"
                                    data-key="t-analytics"> Cancel Orders </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('order') }}"
                                    class="nav-link {{ request()->url() == route('order') ? 'active' : '' }}"
                                    data-key="t-crm"> Order Histories </a>
                            </li>

                        </ul>
                    </div>
                </li>


            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
