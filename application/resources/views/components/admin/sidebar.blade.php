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

    body {
        background: #eee;
    }

    .jumbotron {
        background: #fff;
    }

    .noPadTop {
        padding-top: 0 !important;
    }

    .flex-card-title {
        display: flex !important;
        align-items: center;
    }

    .text-capitalize {
        text-transform: capitalize !important;
    }

    .table thead tr th {
        color: #333;
    }

    .modal-title {
        margin: 0;
        margin-top: -10px;
    }

    .row.equalPad [class*='col-'] {
        padding-right: 7px !important;
        padding-left: 7px !important;
    }

    .table-actions .btn-group form {
        margin: 0;
    }

    .table-actions .btn-group a, .table-actions .btn-group button {
        align-items: center;
        justify-content: center;
        padding: 2px 9px !important;
        font-size: 10.5px !important;
    }

    .table-actions button.btn-danger {
        background: #e72929 !important;
        display: block !important;
        padding: 4px 9px !important;
        font-size: 10.5px !important;
        border-radius: 0 2px 2px 0 !important;
    }

    .transactionTotal {
        margin: 15px 0;
    }

    .transactionTotal h4 {
        font-weight: 600;
        font-size: 15px;
        text-align: center;
        margin: 0;
        width: 100%;
        display: block;
    }


    .transactionTotal h6 {
        font-weight: 600;
        font-size: 13px;
        text-align: center;
        margin: 0;
        width: 100%;
        display: block;
    }

    .table.transactionTotal tbody tr td {
        padding: 13px;
        vertical-align: middle;
    }

    .table.transactionTotal tbody tr td {
        width: 33.3%;
    }

    .statement-overview {
        background: #ffeccf;
        padding: 25px;
    }

    .statement-details h4 {
        font-weight: 600;
        font-size: 20px;
        margin: 0;
    }

    .statement-details{
        padding: 25px;
    }

    .statement-details h5, .statement-overview h5 {
        margin: 0;
        font-weight: 600;
        font-size: 16px;
        color: #666;
    }

    .statement-overview p {
        margin: 5px 0;
    }

    .statement-details table td {
        padding: 10px !important;
    }

    .statement-details p {
        margin: 0;
    }

    .statement-details hr {
        margin: 6px !important;
    }

    .statement-overview hr {
        border-color: #333;
        margin: 5px 0;
        opacity: 0.2;
    }

    .statement-overview  h3 {
        font-weight: 600;
        font-size: 20px;
    }

</style>
<nav class="page-sidebar" data-pages="sidebar">
    <!-- BEGIN SIDEBAR MENU HEADER-->
    <div class="sidebar-header">
        <img width="25%" style="background: #fff; padding: 10px;" src="{{asset('admin/logo-nav.jpeg')}}" alt="Logo">
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
            @if (Auth::user()->role_id == 3)
            <li class="m-t-30 ">
                <a href="{{ route('admin.dashboard') }}" class="detailed">
                    <span class="title">Dashboard</span>
                </a>
                <span class="{{ request()->is('admin/dashboard') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="pg-home"></i></span>
            </li>
            <li class="category-dropdown">
                <a href="javascript:;" class="detailed">
                    <span class="titls">Bank Statements</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-bank"></i></span>

                <ul class="sub-menu" style="display: none;">
                    <li>
                        <a href="{{ route('bankStatements.list') }}" class="detailed">
                            <span class="">Statements</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('banks.index') }}" class="detailed">
                            <span class="">Banks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bank-accounts.index') }}" class="detailed">
                            <span class="">Bank Accounts</span>
                        </a>
                    </li>
                </ul>
            </li>
            @else
            <li class="m-t-30 ">
                <a href="{{ route('admin.dashboard') }}" class="detailed">
                    <span class="title">Dashboard</span>
                </a>
                <span class="{{ request()->is('admin/dashboard') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="pg-home"></i></span>
            </li>
            <li>
                <a href="/" class="detailed">
                    <span class="">Inbox</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-inbox"></i></span>
            </li>
            <li>
                <a href="{{ route('category.list').'?type=main' }}" class="detailed">
                    <span class="titls">Manage Categories</span>
                </a>
                <span class="{{ request()->is('admin/categories*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="fa fa-list-alt"></i></span>
            </li>
            <li>
                <a href="{{ route('vendors.list') }}" class="detailed">
                    <span class="titls">Vendors</span>
                </a>
                <span class="{{ request()->is('admin/vendors*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="fa fa-users"></i></span>
            </li>
            <li>
                <a href="{{ route('products.list') }}" class="detailed">
                    <span class="titls">Products</span>
                </a>
                <span class="{{ request()->is('admin/products*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="fa fa-product-hunt"></i></span>
            </li>
            <li>
                <a href="{{ route('transactions.list') }}" class="detailed">
                    <span class="title">Transactions</span>
                </a>
                <span class="{{ request()->is('admin/transactions*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="pg-charts"></i></span>
            </li>
            <li>
                <a href="{{ route('ArchicvedTransactions.list') }}" class="detailed">
                    <span class="title">Archived</span>
                </a>
                <span class="{{ request()->is('admin/archived*') ? 'bg-success' : '' }} icon-thumbnail"><i
                        class="fa fa-archive"></i></span>
            </li>
            <li class="category-dropdown">
                <a href="#" class="detailed">
                    <span class="titls">Manage Envelopes</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-envelope"></i></span>

                <ul class="sub-menu" style="display: none;">
                    <li>
                        <a href="{{ route('envelope.list') }}" class="detailed">
                            <span class="">Envelopes Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.list') }}" class="detailed">
                            <span class="">Envelope Reports</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="category-dropdown">
                <a href="#" class="detailed">
                    <span class="titls">Manage Budgets</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-balance-scale"></i></span>

                <ul class="sub-menu" style="display: none;">
                    <li>
                        <a href="{{ route('budgets.list') }}" class="detailed">
                            <span class="">Budget Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('budget-reports.list') }}" class="detailed">
                            <span class="">Budget Reports</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.users') }}" class="detailed">
                    <span class="titls">Users</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-users"></i></span>
            </li>
            <li>
                <a href="{{ route('admin.roles') }}" class="detailed">
                    <span class="titls">Roles</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-users"></i></span>
            </li>

            <li class="category-dropdown">
                <a href="javascript:;" class="detailed">
                    <span class="titls">Bank Statements</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-bank"></i></span>

                <ul class="sub-menu" style="display: none;">
                    <li>
                        <a href="{{ route('bankStatements.list') }}" class="detailed">
                            <span class="">Statements</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('banks.index') }}" class="detailed">
                            <span class="">Banks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bank-accounts.index') }}" class="detailed">
                            <span class="">Bank Accounts</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.settings.index') }}" class="detailed">
                    <span class="">Settings</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-wrench"></i></span>
            </li>
            @endif
        </ul>

        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</nav>
<script>
    const dropdown = document.querySelector('.category-dropdown');
    const dropdownList = document.querySelector('.category-dropdown-list');
    dropdown.addEventListener('mouseenter', function () {
        dropdownList.style.height = '100%';
        dropdownList.style.opacity = '1';
        dropdownList.style.visibility = 'visible';
    });
    dropdown.addEventListener('mouseleave', function () {
        dropdownList.style.height = '0';
        dropdownList.style.opacity = '0';
        dropdownList.style.visibility = 'hidden';
    });

</script>
