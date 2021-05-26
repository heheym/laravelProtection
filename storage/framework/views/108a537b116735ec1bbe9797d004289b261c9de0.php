<div class="<?php echo e($viewClass['form-group']); ?> <?php echo !$errors->hasAny($errorKey) ? '' : 'has-error'; ?>">

    <label for="<?php echo e($id); ?>" class="<?php echo e($viewClass['label']); ?> control-label"><?php echo e($label); ?></label>

    <div class="<?php echo e($viewClass['field']); ?> form-inline">

        <?php $__currentLoopData = $errorKey; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($errors->has($col)): ?>
                <?php $__currentLoopData = $errors->get($col); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> <?php echo e($message); ?></label><br/>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div id="<?php echo e($id); ?>" <?php echo $attributes; ?>>
            <select class="form-control" name="<?php echo e($name['province']); ?>" ></select>&nbsp;
            <select class="form-control" name="<?php echo e($name['city']); ?>" ></select>&nbsp;
            <select class="form-control" name="<?php echo e($name['district']); ?>" ></select>&nbsp;
        </div>
        <?php echo $__env->make('admin::form.help-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>
</div>