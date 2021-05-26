
<div class="row">
    <div class="<?php echo e($viewClass['label']); ?>"><h4 class="pull-right"><?php echo e($label); ?></h4></div>
    <div class="<?php echo e($viewClass['field']); ?>"></div>
</div>

<hr style="margin-top: 0px;">

<div id="has-many-<?php echo e($column); ?>" class="has-many-<?php echo e($column); ?>">

    <div class="has-many-<?php echo e($column); ?>-forms">

        <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk => $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="has-many-<?php echo e($column); ?>-form fields-group">

                <?php $__currentLoopData = $form->fields(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $field->render(); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($options['allowDelete']): ?>
                <div class="form-group">
                    <label class="<?php echo e($viewClass['label']); ?> control-label"></label>
                    <div class="<?php echo e($viewClass['field']); ?>">
                        <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i><?php echo e(trans('admin.remove')); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <hr>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    

    <template class="<?php echo e($column); ?>-tpl">
        <div class="has-many-<?php echo e($column); ?>-form fields-group">

            <?php echo $template; ?>


            <div class="form-group">
                <label class="<?php echo e($viewClass['label']); ?> control-label"></label>
                <div class="<?php echo e($viewClass['field']); ?>">
                    <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash"></i>&nbsp;<?php echo e(trans('admin.remove')); ?></div>
                </div>
            </div>
            <hr>
        </div>
    </template>

    <?php if($options['allowCreate']): ?>
    <div class="form-group">
        <label class="<?php echo e($viewClass['label']); ?> control-label"></label>
        <div class="<?php echo e($viewClass['field']); ?>">
            <div class="add btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;<?php echo e(trans('admin.new')); ?></div>
        </div>
    </div>
    <?php endif; ?>

</div>