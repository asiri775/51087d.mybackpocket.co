<!-- BEGIN SIDEBPANEL-->
<style>
    .category-dropdown-list {
        list-style: none;
        height: 0;
        opacity: 0;
        visibility: hidden;
        transition: all .3s ease-in;
        margin-left: 5px;
    }

    .category-dropdown-list li {
        font-family: Arial, sans-serif;
        font-size: 14px;
        padding: 5px 0;
    }

    .new-title {
        font-family: Montserrat;
        font-size: 11px;
        font-weight: 500;
        color: #abdbe3 ;
    }

</style>
<nav class="page-sidebar" data-pages="sidebar">
    <!-- BEGIN SIDEBAR MENU HEADER-->
    <div class="sidebar-header">
        <img width="25%" style="background: #fff; padding: 10px;" src="/admin/logo-nav.jpeg" alt="Logo">
        <div class="sidebar-header-controls">
            <button type="button"
                class="btn btn-link d-lg-inline-block d-xlg-inline-block d-md-inline-block d-sm-none d-none"
                data-toggle-pin="sidebar">
            </button>
        </div>
    </div>
    <!-- END SIDEBAR MENU HEADER-->
    <!-- START SIDEBAR MENU -->
    <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
            <li class="m-t-30 ">
                <a href="{{ route('user.dashboard') }}" class="detailed">
                    <span class="title new-title">Dashboard</span>
                </a>
                <span class="{{ request()->is('user/dashboard') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="pg-home"></i></span>
            </li>
            <li>
                <a href="{{ route('user.transactions.index') }}" class="detailed">
                    <span class="title new-title">Transactions</span>
                </a>
                <span class="{{ request()->is('user/transactions*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="pg-charts"></i></span>
            </li>
            <li>
                <a href="{{ route('user.stores.index') }}" class="detailed">
                    <span class=" new-title">My Stores</span>
                </a>
                <span class="{{ request()->is('user/stores*') ? 'bg-success' : '' }} icon-thumbnail"><i class="pg-shopping_cart"></i></span>
            </li>
            <li>
                <a href="{{ route('user.envelopes.index') }}" class="detailed">
                    <span class=" new-title">My Envelopes</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-envelope"></i></span>
            </li>
            <li>
                <a href="{{ route('user.budget.manager.index') }}" class="detailed">
                    <span class=" new-title">Budget Manager</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-envelope"></i></span>
            </li>
            <li>
                <a href="{{ route('user.reports.index') }}" class="detailed">
                    <span class=" new-title">Reports</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-envelope"></i></span>
            </li>
            <li>
                <a href="{{ route('user.settings.index') }}" class="detailed">
                    <span class="">Settings</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-wrench"></i></span>
            </li>
        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</nav>
<script>
    const dropdown = document.querySelector('.category-dropdown');
    const dropdownList = document.querySelector('.category-dropdown-list');
    dropdown.addEventListener('mouseenter', function() {
        dropdownList.style.height = '100%';
        dropdownList.style.opacity = '1';
        dropdownList.style.visibility = 'visible';
    });
    dropdown.addEventListener('mouseleave', function() {
        dropdownList.style.height = '0';
        dropdownList.style.opacity = '0';
        dropdownList.style.visibility = 'hidden';
    });

</script>
