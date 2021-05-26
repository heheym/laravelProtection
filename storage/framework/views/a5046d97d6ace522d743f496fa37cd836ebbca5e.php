<div class="box">
    <br/>


    <?php if(isset($title)): ?>
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($title); ?></h3>
        </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>

        <div class="box-header with-border;" >
            <form class="form-inline" id="target" action="" method="get" style="" >
                <div class="form-group" style="">
                    <label for="exampleInputName2">svrkey</label>
                    <input type="text" class="form-control" id="svrkey" placeholder="" name="svrkey"
                           style="width:120px">
                    &nbsp;
                    <select id="sel" class="form-control">
                        <option value="placename">
                            场所名称
                        </option>
                        <option value="songname">
                            歌名
                        </option>
                        <option value="singer">
                            歌星
                        </option>
                    </select>
                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label style="margin-left:10px">创建时间</label>
                    <input type="text" class="form-control" id="createdate_start" placeholder="上传时间"
                           name="createdate[start]" value="">
                    -
                    <input type="text" class="form-control" id="createdate_end" placeholder="上传时间"
                           name="createdate[end]" value="">
                </div>
                <button type="submit" class="btn btn-default form-control" id="butt">搜索</button>
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
        var svrkey = url.searchParams.get('svrkey');

        $('#svrkey').val(svrkey);

        var placename = url.searchParams.get('placename');
        var songname = url.searchParams.get('songname');
        var singer = url.searchParams.get('singer');
        if (placename !== null) {
            $("#sel option[value=placename]").prop('selected', true);
            $("#selvalue").val(placename);
        } else if (songname !== null) {
            $("#sel option[value=songname]").prop('selected', true);
            $("#selvalue").val(songname);
        } else if (singer !== null) {
            $("#sel option[value=singer]").prop('selected', true);
            $("#selvalue").val(singer);
        } 

        var createdate_start = url.searchParams.get('createdate[start]');
        var createdate_end = url.searchParams.get('createdate[end]');
        if (createdate_start !== null) {
            $('#createdate_start').val(createdate_start);
        }
        if (createdate_end !== null) {
            $('#createdate_end').val(createdate_end);
        }

    });

    $('#target').click(function () {
        var sel = $("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='" + sel + "' value='" + selvalue + "'>");
    });
</script>
