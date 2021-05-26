<div class="box">
    <br />
    <?php if(isset($title)): ?>
    <div class="box-header with-border">
        <h3 class="box-title"> <?php echo e($title); ?></h3>
    </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>
    <div class="box-header with-border">
        <form class="form-inline" id="receipt" action="" method="get" style="display:inline-block;">
            <div class="form-group">
                <label for="exampleInputName2">单号</label>
                <input type="text" class="form-control" id="receipt_no" placeholder="">
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
    var receipt_svrkey = url.searchParams.get('receipt_no');
    $('#receipt_no').val(receipt_svrkey);
});
    $('#receipt').click(function() {
        var url = new URL(location);
        var receipt_no = $('#receipt_no').val();
        var receipt_svrkey = url.searchParams.get('receipt_svrkey');
        if(receipt_svrkey==null){
            receipt_svrkey = '';
        }
        $('input[name=receipt_no]').remove();
        $('input[name=receipt_svrkey]').remove();
        $('#receipt').append("<input type='hidden' name='receipt_no' value='"+receipt_no+"'>");
        $('#receipt').append("<input type='hidden' name='receipt_svrkey' value='"+receipt_svrkey+"'>");
//        return false;
//        $('#target').submit();
    });
</script>
