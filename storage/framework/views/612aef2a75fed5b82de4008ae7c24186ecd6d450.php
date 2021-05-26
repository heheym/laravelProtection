<div class="box">
    <br />


    <?php if(isset($title)): ?>
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($title); ?></h3>
        </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>

        <div class="box-header with-border">
            <form class="form-inline" id="settopboxTarget" action="" method="get" style="display: inline-block;">
                <div class="form-group">
                    <label for="exampleInputName2">key</label>
                    <input type="text" class="form-control" id="settopbox_key" placeholder="" name="settopbox_key" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="settopboxSel" class="form-control">
                        <option value="settopbox_placename">
                            场所名称
                        </option>
                        <option value="settopbox_KtvBoxid">
                            机器码
                        </option>
                        <option value="settopbox_machineCode">
                            机顶盒MAC
                        </option>
                        <option value="settopbox_contacts">
                            联系人
                        </option>
                    </select>

                    <input type="text" class="form-control" id="settopboxSelvalue" placeholder="" style="width:120px">







                    <label for="" style="margin-left:10px">状态:</label>
                    <select  id="settopbox_KtvBoxState" class="form-control" name="settopbox_KtvBoxState">
                        <option value="">
                            所有
                        </option>
                        <option value="0">
                            未注册
                        </option>
                        <option value="1">
                            正常
                        </option>
                        <option value="2">
                            返修
                        </option>
                        <option value="3">
                            过期
                        </option>
                        <option value="4">
                            作废
                        </option>
                    </select>
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

        var url = new URL(location);
        var settopbox_key = url.searchParams.get('settopbox_key');

        $('#settopbox_key').val(settopbox_key);

        var settopbox_placename = url.searchParams.get('settopbox_placename');
        var settopbox_contacts = url.searchParams.get('settopbox_contacts');
        var settopbox_phone = url.searchParams.get('settopbox_phone');
        var settopbox_KtvBoxid = url.searchParams.get('settopbox_KtvBoxid');
        var settopbox_machineCode = url.searchParams.get('settopbox_machineCode');
        if(settopbox_contacts!==null){
            $("#settopboxSel option[value=settopbox_contacts]").prop('selected',true);
            $("#settopboxSelvalue").val(settopbox_contacts);
        }else if(settopbox_placename!==null){
            $("#settopboxSel option[value=settopbox_placename]").prop('selected',true);
            $("#settopboxSelvalue").val(settopbox_placename);
        }else if(settopbox_phone!==null){
            $("#settopboxSel option[value=settopbox_phone]").prop('selected',true);
            $("#settopboxSelvalue").val(settopbox_phone);
        }else if(settopbox_KtvBoxid!==null){
            $("#settopboxSel option[value=settopbox_KtvBoxid]").prop('selected',true);
            $("#settopboxSelvalue").val(settopbox_KtvBoxid);
        }else if(settopbox_machineCode!==null){
            $("#settopboxSel option[value=settopbox_machineCode]").prop('selected',true);
            $("#settopboxSelvalue").val(settopbox_machineCode);
        }

        var settopbox_province = url.searchParams.get('settopbox_province');
        var settopbox_city = url.searchParams.get('settopbox_city');
        $('#distpicker').distpicker({
            province: settopbox_province,
            city: settopbox_city,
        });

        var settopbox_KtvBoxState = url.searchParams.get('settopbox_KtvBoxState');
        if(settopbox_KtvBoxState!==null && settopbox_KtvBoxState.length>0){
            $("#settopbox_KtvBoxState option[value="+settopbox_KtvBoxState+"]").prop('selected',true);
        }
    });

    $('#settopboxTarget').click(function() {
        var settopboxSel =$("#settopboxSel option:selected").val();
        var settopboxSelvalue = $("#settopboxSelvalue").val();
        $('#settopboxSelhidden').remove();
        $('#settopboxTarget').append("<input type='hidden' id='settopboxSelhidden' name='"+settopboxSel+"' value='"+settopboxSelvalue+"'>");
    });
</script>
