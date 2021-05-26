<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo e($label); ?></label>
    <div class="col-sm-8" style="width: 390px">
        <div class="input-group input-group-sm">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control" id="<?php echo e($id['start']); ?>" placeholder="<?php echo e($label); ?>" name="<?php echo e($name['start']); ?>" value="<?php echo e(request("{$column}.start", \Illuminate\Support\Arr::get($value, 'start'))); ?>">
            <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
            <input type="text" class="form-control" id="<?php echo e($id['end']); ?>" placeholder="<?php echo e($label); ?>" name="<?php echo e($name['end']); ?>" value="<?php echo e(request("{$column}.end", \Illuminate\Support\Arr::get($value, 'end'))); ?>">
        </div>
    </div>
</div>