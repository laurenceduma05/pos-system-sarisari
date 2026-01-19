<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav">
                <li class="nav-item" id="toggle-menuicon">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                {{-- <li class="nav-item d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#"S class="nav-link">Contact</a>
                </li> --}}
            </ul>

            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!--li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-comments"></i>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="dist/img/user1-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">Call me whenever you can...</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="dist/img/user8-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        John Pierce
                                        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">I got your message bro</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">

                            <div class="media">
                                <img src="dist/img/user3-128x128.jpg" alt="User Avatar"
                                    class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Nora Silvester
                                        <span class="float-right text-sm text-warning"><i
                                                class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">The subject goes here</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </li-->

                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">7</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-header">7 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <!--div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a-->
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <?php
            $base64 = "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";
        ?>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="index3.html" class="brand-link">
                <img src="{{ asset('logo/logo-1.png') }}" alt="WOL Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">POS System</span>
            </a>

            <div class="sidebar">

                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="data:image/png;base64,<?=$base64?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <router-link to="/admin/dashboard" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/admin/inventory" :class="$route.path.startsWith('/admin/inventory') ? 'active' : ''" class="nav-link">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>
                                    Inventory
                                </p>
                            </router-link>
                        </li>

                        <!-- Cashier Module - Prominent Button -->
                        <li class="nav-item">
                            <router-link to="/admin/cashier" active-class="active" class="nav-link" style="background-color: #28a745; margin: 10px 5px; border-radius: 5px;">
                                <i class="nav-icon fas fa-cash-register" style="color: white;"></i>
                                <p style="color: white; font-weight: bold;">
                                    Cashier
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/admin/users" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Users
                                </p>
                            </router-link>
                        </li-->

                        <!--li class="nav-item">
                            <router-link to="/admin/settings" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>
                                    Settings
                                </p>
                            </router-link>
                        </li-->

                        <!--li class="nav-item">
                            <router-link to="/admin/profile" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    Profile
                                </p>
                            </router-link>
                        </li-->

                        <!-- POS System Pages -->
                        <li class="nav-item">
                            <router-link to="/admin/pos/categories" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Categories
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/admin/pos/products" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-box"></i>
                                <p>
                                    Products
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/admin/pos/customers" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Customers
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/admin/pos/orders" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>
                                    Orders
                                </p>
                            </router-link>
                        </li>
                    </ul>
                </nav>

                <!-- Logout Button at Bottom -->
                <div style="position: absolute; bottom: 0; width: 100%; padding: 10px; border-top: 1px solid #dee2e6;">
                    <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                        @csrf
                        <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 10px 15px; cursor: pointer; width: 100%; text-align: left; border-radius: 4px; font-weight: 600;" class="btn btn-danger">
                            <i class="nav-icon fas fa-sign-out-alt mr-2"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

            </div>

        </aside>

        <div class="content-wrapper">
            <router-view>

            </router-view>
            {{-- <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Card title</h5>
                                    <p class="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the
                                        card's
                                        content.
                                    </p>
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <h5 class="card-title">Card title</h5>
                                    <p class="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the
                                        card's
                                        content.
                                    </p>
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div> --}}

        </div>


        <aside class="control-sidebar control-sidebar-dark">

            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>


        {{-- <footer class="main-footer">

            <div class="float-right d-none d-sm-inline">
                <ul style="list-style: none; font-size:13px;">
                    <li>Developer: <i class="text-info">Tedmar Enoria | tedtedenoria@gmail.com</i></li>
                    <!-- <li>email: <i class="text-info">tedtedenoria@gmail.com</i></li>
                    <li>Mobile: <i class="text-info">09278343508</i></li> -->
                </ul>
            </div>

            <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights
            reserved.
        </footer> --}}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleMenuIcon = document.getElementById('toggle-menuicon');
            const body = document.body;

            toggleMenuIcon.addEventListener('click', () => {
                if (body.classList.contains('sidebar-collapse')) {
                    localStorage.setItem('sidebarState', 'expanded');
                } else {
                    localStorage.setItem('sidebarState', 'collapsed');
                }
            })

            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                body.classList.add('sidebar-collapse');
            }

        });
    </script>
</body>

</html>
