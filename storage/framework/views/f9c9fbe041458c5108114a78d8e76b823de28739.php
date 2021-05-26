<div class="box-footer">

    <?php echo e(csrf_field()); ?>


    <div class="col-md-<?php echo e($width['label']); ?>">
    </div>

    <div class="col-md-<?php echo e($width['field']); ?>">

        <?php if(in_array('submit', $buttons)): ?>
        <div class="btn-group pull-right">
            <button type="submit" class="btn btn-primary"><?php echo e(trans('admin.submit')); ?></button>
        </div>

        <?php $__currentLoopData = $submit_redirects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $redirect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(in_array($redirect, $checkboxes)): ?>
            <label class="pull-right" style="margin: 5px 10px 0 0;">
                <input type="checkbox" class="after-submit" name="after-save" value="<?php echo e($value); ?>" <?php echo e(($default_check == $redirect) ? 'checked' : ''); ?>> <?php echo e(trans("admin.{$redirect}")); ?>

            </label>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php endif; ?>

        <?php if(in_array('reset', $buttons)): ?>
        <div class="btn-group pull-left">
            <button type="reset" class="btn btn-warning"><?php echo e(trans('admin.reset')); ?></button>
        </div>
        <?php endif; ?>
    </div>
</div>