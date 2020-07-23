<?php
$roleIds = DB::table('admin_role_users')->where('user_id',Admin::user()->id)->pluck('role_id')->toArray();//该用户所有的角色id


$permissionIds = DB::table('admin_role_permissions')->whereIn('role_id',$roleIds)->pluck('permission_id')->toArray();//该用户所有角色id下所有的权限id

$parentId = DB::table('admin_permissions')->whereIn('id',$permissionIds)->pluck('parent_id')->toArray(); //权限id的parent_id
$menuId = DB::table('admin_permissions')->whereIn('id',$parentId)->pluck('menu_id')->toArray(); //parent_id的menu_id

$parentId2 = DB::table('admin_permissions')->whereIn('id',$parentId)->pluck('parent_id')->toArray(); //parent_id的parent_id
$parentIdMenuId = DB::table('admin_permissions')->whereIn('id',$parentId2)->pluck('menu_id')->toArray(); //parent_id的parent_id的menu_id


?>
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                {{--<img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">--}}
            </div>
            <div class="pull-left info">
                {{--<p>{{ Admin::user()->name }}</p>--}}
                <!-- Status -->
                {{--<a href="#"><i class="fa fa-circle text-success"></i> {{ trans('admin.online') }}</a>--}}
            </div>
        </div>

        @if(config('admin.enable_menu_search'))
        <!-- search form (Optional) -->
        <form class="sidebar-form" style="overflow: initial;" onsubmit="return false;">
            <div class="input-group">
                <input type="text" autocomplete="off" class="form-control autocomplete" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                <ul class="dropdown-menu" role="menu" style="min-width: 210px;max-height: 300px;overflow: auto;">
                    @foreach(Admin::menuLinks() as $link)
                    <li>
                        <a href="{{ admin_url($link['uri']) }}"><i class="fa {{ $link['icon'] }}"></i>{{ admin_trans($link['title']) }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </form>
        <!-- /.search form -->
        @endif

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">{{ trans('admin.menu') }}</li>

{{--            @each('admin::partials.menu', Admin::menu(), 'item')--}}

            @foreach(Admin::menu() as $item)

                    @include('admin::partials.menu', [$item,$permissionIds,$menuId,$parentIdMenuId])

            @endforeach

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>