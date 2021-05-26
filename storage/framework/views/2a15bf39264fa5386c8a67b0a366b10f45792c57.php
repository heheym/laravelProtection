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
                
            </div>
            <div class="pull-left info">
                
                <!-- Status -->
                
            </div>
        </div>

        <?php if(config('admin.enable_menu_search')): ?>
        <!-- search form (Optional) -->
        <form class="sidebar-form" style="overflow: initial;" onsubmit="return false;">
            <div class="input-group">
                <input type="text" autocomplete="off" class="form-control autocomplete" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                <ul class="dropdown-menu" role="menu" style="min-width: 210px;max-height: 300px;overflow: auto;">
                    <?php $__currentLoopData = Admin::menuLinks(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(admin_url($link['uri'])); ?>"><i class="fa <?php echo e($link['icon']); ?>"></i><?php echo e(admin_trans($link['title'])); ?></a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </form>
        <!-- /.search form -->
        <?php endif; ?>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header"><?php echo e(trans('admin.menu')); ?></li>



            <?php $__currentLoopData = Admin::menu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php echo $__env->make('admin::partials.menu', [$item,$permissionIds,$menuId,$parentIdMenuId], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>

<style>
    li span {
        color:#000;
    }
</style>