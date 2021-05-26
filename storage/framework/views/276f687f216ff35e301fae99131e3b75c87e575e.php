<div class="box">
    <br />


    <?php if(isset($title)): ?>
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($title); ?></h3>
        </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>

        <div class="box-header with-border">
            <form class="form-inline" id="orderTarget"  action="" method="get" style="display: inline-block;" >
                <div class="form-group">


                    &nbsp;&nbsp;&nbsp;
                    <select  id="orderSel" class="form-control" name="">
                        <option value="paymentno">
                            账号
                        </option>









                    </select>

                    <input type="text" class="form-control" id="orderSelValue" placeholder="" style="width:120px">













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
        $('#distpicker').distpicker('destroy');

        var url = new URL(location);
        // var ordersn_leshua_order_id = url.searchParams.get('ordersn_leshua_order_id');

        // $('#ordersn_leshua_order_id').val(ordersn_leshua_order_id);

        var paymentno = url.searchParams.get('paymentno');
        // var ordersn_roomno = url.searchParams.get('ordersn_roomno');
        // var phone = url.searchParams.get('phone');
        // var KtvBoxid = url.searchParams.get('KtvBoxid');
        // var key = url.searchParams.get('key');
        if(paymentno!==null){
            $("#orderSel option[value=paymentno]").prop('selected',true);
            $("#orderSelValue").val(paymentno);
        }
        // else if(ordersn_roomno!==null){
        //     $("#orderSel option[value=ordersn_roomno]").prop('selected',true);
        //     $("#orderSelValue").val(ordersn_roomno);
        // }
        // if(contacts!==null){
        //     $("#sel option[value=contacts]").prop('selected',true);
        //     $("#selvalue").val(contacts);
        // }else if(placename!==null){
        //     $("#sel option[value=placename]").prop('selected',true);
        //     $("#selvalue").val(placename);
        // }else if(phone!==null){
        //     $("#sel option[value=phone]").prop('selected',true);
        //     $("#selvalue").val(phone);
        // }

        // var province = url.searchParams.get('province');
        // var city = url.searchParams.get('city');
        // $('#distpicker').distpicker({
        //     province: province,
        //     city: city,
        // });

        // var ordersn_order_status = url.searchParams.get('ordersn_order_status');
        // // alert(ordersn_order_status);
        // if(ordersn_order_status!==null && ordersn_order_status.length>0){
        //     $("#ordersn_order_status option[value="+ordersn_order_status+"]").prop('selected',true);
        //     $("#sel option[value=placename]").prop('selected',true);
        //
        // }
    });

    $('#orderTarget').click(function() {
        // alert(12);
        var sel =$("#orderSel option:selected").val();
        var selvalue = $("#orderSelValue").val();
        $('#orderSelhidden').remove();
        $('#orderTarget').append("<input type='hidden' id='orderSelhidden' name='"+sel+"' value='"+selvalue+"'>");
        // alert(123);
        // return false;
        // var url = new URL(location);
        // var order_key = url.searchParams.get('order_key');

        // $('#order_key').remove();
        // $('#orderTarget').append("<input type='hidden' id='order_key' name='"+order_key+"' value='"+order_key+"'>");
    });
</script>
