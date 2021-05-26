<div class="box">
    <br />


    <?php if(isset($title)): ?>
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($title); ?></h3>
        </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>

        <div class="box-header with-border">
            <form class="form-inline" id="target"  action="" method="get" style="display: inline-block;" >
                <div class="form-group">
                    <label for="exampleInputName2">key</label>
                    <input type="text" class="form-control" id="place_key" placeholder="" name="place_key" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="place_placename">
                            场所名称
                        </option>
                        <option value="place_contacts">
                            联系人
                        </option>
                        <option value="place_phone">
                            手机号
                        </option>
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label for="" style="margin-left:10px">地址:</label>
                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">
                        <select name="place_province" data-province="" style="border: none;outline: none;width:100px"></select>
                        <select name="place_city" data-city="" style="border: none;outline: none;"></select>
                    </div>

                    <label for="" style="margin-left:10px">状态:</label>
                    <select  id="place_status" class="form-control" name="place_status">
                        <option value="">
                            所有
                        </option>
                        <option value="1">
                            已启用
                        </option>
                        <option value="0">
                            未启用
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
                <div class="pull-right">
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
        $('#distpicker').distpicker('destroy');

        var province = url.searchParams.get('place_province');
        var city = url.searchParams.get('place_city');
        $('#distpicker').distpicker({
            province: province,
            city: city,
        });


        var place_key = url.searchParams.get('place_key');

        $('#place_key').val(place_key);

        var place_placename = url.searchParams.get('place_placename');
        var place_contacts = url.searchParams.get('place_contacts');
        var place_phone = url.searchParams.get('place_phone');
        if(place_contacts!==null){
            $("#sel option[value=place_contacts]").prop('selected',true);
            $("#selvalue").val(place_contacts);
        }else if(place_placename!==null){
            $("#sel option[value=place_placename]").prop('selected',true);
            $("#selvalue").val(place_placename);
        }else if(place_phone!==null){
            $("#sel option[value=place_phone]").prop('selected',true);
            $("#selvalue").val(place_phone);
        }



        var place_status = url.searchParams.get('place_status');
        if(place_status!==null && place_status.length>0){
            $("#place_status option[value="+place_status+"]").prop('selected',true);
        }
    });

    $('#butt').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
