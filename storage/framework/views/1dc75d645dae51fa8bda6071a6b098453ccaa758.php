<style>
    td .form-group {
        margin-bottom: 0 !important;
    }
</style>

<div class="row">
    <div class="<?php echo e($viewClass['label']); ?>"><h4 class="pull-right"><?php echo e($label); ?></h4></div>
    <div class="<?php echo e($viewClass['field']); ?>">
        <div id="has-many-<?php echo e($column); ?>" style="margin-top: 15px;">
            <table class="table table-has-many has-many-<?php echo e($column); ?>">
                <thead>
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($header); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <th class="hidden"></th>

                    <?php if($options['allowDelete']): ?>
                        <th></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="has-many-<?php echo e($column); ?>-forms">
                <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk => $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="has-many-<?php echo e($column); ?>-form fields-group">

                        <?php $hidden = ''; ?>

                        <?php $__currentLoopData = $form->fields(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php if(is_a($field, \Encore\Admin\Form\Field\Hidden::class)): ?>
                                <?php $hidden .= $field->render(); ?>
                                <?php continue; ?>
                            <?php endif; ?>

                            <td><?php echo $field->setLabelClass(['hidden'])->setWidth(12, 0)->render(); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <td class="hidden"><?php echo $hidden; ?></td>

                        <?php if($options['allowDelete']): ?>
                            <td class="form-group">
                                <div>
                                    <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i><?php echo e(trans('admin.remove')); ?></div>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <template class="<?php echo e($column); ?>-tpl">
                <tr class="has-many-<?php echo e($column); ?>-form fields-group">

                    <?php echo $template; ?>


                    <td class="form-group">
                        <div>
                            <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i><?php echo e(trans('admin.remove')); ?></div>
                        </div>
                    </td>
                </tr>
            </template>

            <?php if($options['allowCreate']): ?>
                <div class="form-group">
                    <div class="<?php echo e($viewClass['field']); ?>">
                        <div class="add btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;<?php echo e(trans('admin.new')); ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<hr style="margin-top: 0px;">

