<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}">{{ setting('site_name') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard.index') }}">
                <?php 
                    if(setting('site_name')) {
                        $sitenames = explode(' ', setting('site_name'));
                        if(count($sitenames) > 1) {
                            foreach ($sitenames as $sitename) {
                                echo $sitename[0];
                            }
                        } else {
                            echo substr(setting('site_name'), 0, 2);
                        }
                    }
                ?>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.dashboard.index') }}"><i class="fas fa-laptop"></i> <span>{{ __('sidebar.dashboard')}}</span></a></li>
            <li class="{{ request()->is('admin/location*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.location.index') }}"><i class="fas fa-life-ring "></i> <span>{{ __('sidebar.locations') }}</span></a></li>
            <li class="{{ request()->is('admin/area*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.area.index') }}"><i class="fas fa-map"></i> <span>{{ __('Areas') }}</span></a></li>
            <li class="{{ request()->is('admin/category*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.category.index') }}"><i class="fas fa-list-ul"></i> <span>{{ __('sidebar.categories') }}</span></a></li>
            <li class="{{ request()->is('admin/products*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.products.index') }}"><i class="fas fa-gift"></i> <span>{{ __('sidebar.products') }}</span></a></li>
            <li class="{{ request()->is('admin/shop*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.shop.index') }}"><i class="fas fa-university"></i> <span>{{ __('sidebar.shops') }}</span></a></li>
            <li class="{{ request()->is('admin/orders*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.orders.index') }}"><i class="fas fa-cart-plus"></i> <span>{{ __('sidebar.orders') }}</span></a></li>
            <li class="{{ request()->is('admin/transaction*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.transaction.index') }}"><i class="fas fa-calculator"></i> <span>{{ __('sidebar.transactions') }}</span></a></li>
            <li class="{{ request()->is('admin/customers*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="fas fa-user-secret"></i> <span>{{ __('sidebar.customers') }}</span></a></li>
            <li class="{{ request()->is('admin/adminusers*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.adminusers.index') }}"><i class="fas fa-users"></i> <span>{{ __('sidebar.administrators') }}</span></a></li>
            <li class="{{ request()->is('admin/deliveryboys*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.deliveryboys.index') }}"><i class="fas fa-user"></i> <span>{{ __('sidebar.delivery_boys') }}</span></a></li>
            <li class="{{ request()->is('admin/updates*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.updates.index') }}"><i class="fas fa-cloud-download-alt"></i> <span>{{ __('sidebar.updates') }}</span></a></li>
            <li class="{{ request()->is('admin/shop-owner-sales-report') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.shop-owner-sales-report.index') }}"><i class="fas fa-list-alt"></i> <span>{{ __('sidebar.shop-owner-sales-report') }}</span></a></li>
            <li class="{{ request()->is('admin/admin-commission-report') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.admin-commission-report.index') }}"><i class="fas fa-fax"></i> <span>{{ __('sidebar.admin-commission-report') }}</span></a></li>
            <li class="{{ request()->is('admin/setting') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin.setting.all') }}"><i class="fas fa-cogs"></i> <span>{{ __('sidebar.settings') }}</span></a></li>
        </ul>
    </aside>
</div>
