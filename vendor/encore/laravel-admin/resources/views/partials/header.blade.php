<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ admin_url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{!! config('admin.logo-mini', config('admin.name')) !!}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{!! config('admin.logo', config('admin.name')) !!}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <ul class="nav navbar-nav">
        {!! Admin::getNavbar()->render('left') !!}
        </ul>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                {!! Admin::getNavbar()->render() !!}

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        {{--<img src="{{ Admin::user()->avatar }}" class="user-image" alt="User Image">--}}
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        {{--<span class="hidden-xs">{{ Admin::user()->name }}</span>--}}
                        <span class="hidden-xs">个人中心</span>
                    </a>
                    {{--<ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">

                            <p>
                                {{ Admin::user()->name }}
                                <small>Member since admin {{ Admin::user()->created_at }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ admin_url('auth/setting') }}" class="btn btn-default btn-flat">{{ trans('admin.setting') }}</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ admin_url('auth/logout') }}" class="btn btn-default btn-flat">{{ trans('admin.logout') }}</a>
                            </div>
                        </li>
                    </ul> --}}

                    <ul class="dropdown-menu" style="width:140px">
                        <li class="">
                            <a href="{{ admin_url('auth/setting') }}" class="btn btn-default btn-flat"><i class="fa fa-user-secret"></i>修改密码</a>
                        </li>
                        <li class="">
                            <a href="{{ admin_url('auth/logout') }}" class="btn btn-default btn-flat"><i class="fa fa-sign-out"></i> 退出登录</a>
                            {{--<a href="logout"><i class="fa fa-sign-out"></i> 退出登录</a>--}}
                        </li>
                    </ul>

                </li>
                <!-- Control Sidebar Toggle Button -->
                {{--<li>--}}
                    {{--<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>--}}
                {{--</li>--}}
            </ul>
        </div>
    </nav>
</header>