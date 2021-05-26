<div class="box">
    <br />


    <?php if(isset($title)): ?>
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($title); ?></h3>
        </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>

        <div class="box-header with-border">
            <form class="form-inline" id="target" action="" method="get" style="display: inline-block;">
                <div class="form-group">&nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="KtvBoxid">
                            机器码
                        </option>
                        <option value="machineCode">
                            机顶盒MAC
                        </option>
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">
















                </div>
                <button type="submit" class="btn btn-default" id="butt">搜索</button>
            </form>

            <div class="pull-right">

                <?php echo $grid->renderColumnSelector(); ?>

                <?php echo $grid->renderExportButton(); ?>

                <?php echo $grid->renderCreateButton(); ?>

            </div>
            <?php if( $grid->showTools() ): ?>
                <div class="pull-right" style="margin-right:10px">
                    <?php echo $grid->renderHeaderTools(); ?>

                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    

    <?php echo $grid->renderHeader(); ?>


<!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="<?php echo e($grid->tableID); ?>">
            <thead>
            <tr>
                <?php $__currentLoopData = $grid->visibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php if($column->getLabel()=='操作'): ?>
                        <th style="text-overflow:ellipsis;word-break:keep-all; white-space:nowrap;text-align:center;padding-right:30px"><?php echo e($column->getLabel()); ?><?php echo $column->renderHeader(); ?></th>
                    <?php else: ?>
                        <th style="text-overflow:ellipsis;word-break:keep-all; white-space:nowrap;text-align:center;"><?php echo e($column->getLabel()); ?><?php echo $column->renderHeader(); ?></th>
                    <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
            </thead>

            <?php if($grid->hasQuickCreate()): ?>
                <?php echo $grid->renderQuickCreate(); ?>

            <?php endif; ?>

            <tbody>

            <?php if($grid->rows()->isEmpty()): ?>
                <?php echo $__env->make('admin::grid.empty-grid', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>

            <?php $__currentLoopData = $grid->rows(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr <?php echo $row->getRowAttributes(); ?>>
                    <?php $__currentLoopData = $grid->visibleColumnNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td style="text-align: center" <?php echo $row->getColumnAttributes($name); ?>>
                            <?php echo $row->column($name); ?>

                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>

            <?php echo $grid->renderTotalRow(); ?>


        </table>

    </div>

    <?php echo $grid->renderFooter(); ?>


    <div class="box-footer clearfix">
        <?php echo $grid->paginator(); ?>

    </div>
    <!-- /.box-body -->
</div>

<script>
    $(function () {
        $('#distpicker').distpicker('destroy');

        var url = new URL(location);
        var key = url.searchParams.get('key');

        $('#key').val(key);

        var placename = url.searchParams.get('placename');
        var contacts = url.searchParams.get('contacts');
        var phone = url.searchParams.get('phone');
        var KtvBoxid = url.searchParams.get('KtvBoxid');
        var machineCode = url.searchParams.get('machineCode');
        if(contacts!==null){
            $("#sel option[value=contacts]").prop('selected',true);
            $("#selvalue").val(contacts);
        }else if(placename!==null){
            $("#sel option[value=placename]").prop('selected',true);
            $("#selvalue").val(placename);
        }else if(phone!==null){
            $("#sel option[value=phone]").prop('selected',true);
            $("#selvalue").val(phone);
        }else if(KtvBoxid!==null){
            $("#sel option[value=KtvBoxid]").prop('selected',true);
            $("#selvalue").val(KtvBoxid);
        }else if(machineCode!==null){
            $("#sel option[value=machineCode]").prop('selected',true);
            $("#selvalue").val(machineCode);
        }

        var province = url.searchParams.get('province');
        var city = url.searchParams.get('city');
        $('#distpicker').distpicker({
            province: province,
            city: city,
        });

        var KtvBoxState = url.searchParams.get('KtvBoxState');
        if(KtvBoxState!==null &&KtvBoxState.length>0){
            $("#KtvBoxState option[value="+KtvBoxState+"]").prop('selected',true);
        }
    });

    $('#target').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
