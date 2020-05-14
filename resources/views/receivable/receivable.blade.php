<div class="box">
    @if(isset($title))
    <div class="box-header with-border">
        <h3 class="box-title"> {{ $title }}</h3>
    </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )
    <div class="box-header with-border">
        <form class="form-inline" id="receivable" action="" method="get" style="display: inline-block;">
            <div class="form-group">
                <label for="exampleInputName2">单号</label>
                <input type="text" class="form-control" id="receivable_item_no" placeholder="">
            </div>
            <button type="submit" class="btn btn-default" id="butt">搜索</button>
        </form>
        <div class="pull-right">
            {!! $grid->renderColumnSelector() !!}
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>
        @if ( $grid->showTools() )
        <div class="pull-right">
            {!! $grid->renderHeaderTools() !!}
        </div>
        @endif
    </div>
    @endif

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="{{ $grid->tableID }}">
            <thead>
                <tr>
                    @foreach($grid->visibleColumns() as $column)

                        @if($column->getLabel()=='操作')
                            <th style="text-overflow:ellipsis;word-break:keep-all; white-space:nowrap;text-align:center;padding-right:30px">{{$column->getLabel()}}{!! $column->renderHeader() !!}</th>
                        @else
                            <th style="text-overflow:ellipsis;word-break:keep-all; white-space:nowrap;text-align:center;">{{$column->getLabel()}}{!! $column->renderHeader() !!}</th>
                        @endif

                    @endforeach
                </tr>
            </thead>

            @if ($grid->hasQuickCreate())
                {!! $grid->renderQuickCreate() !!}
            @endif

            <tbody>

                @if($grid->rows()->isEmpty())
                    @include('admin::grid.empty-grid')
                @endif

                @foreach($grid->rows() as $row)
                <tr {!! $row->getRowAttributes() !!}>
                    @foreach($grid->visibleColumnNames() as $name)
                    <td style="text-align: center" {!! $row->getColumnAttributes($name) !!}>
                        {!! $row->column($name) !!}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>

            {!! $grid->renderTotalRow() !!}

        </table>

    </div>

    {!! $grid->renderFooter() !!}

    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>

<script>
    $(function () {
        var url = new URL(location);
        var receivable_item_no = url.searchParams.get('receivable_item_no');
        $('#receivable_item_no').val(receivable_item_no);
    });
    $('#receivable').click(function() {
        var url = new URL(location);
        var receivable_item_no = $('#receivable_item_no').val();
        var receivable_svrkey = url.searchParams.get('receivable_svrkey');
        if(receivable_svrkey==null){
            receivable_svrkey = '';
        }
        $('input[name=receivable_item_no]').remove();
        $('input[name=receivable_svrkey]').remove();
    $('#receivable').append("<input type='hidden' name='receivable_item_no' value='"+receivable_item_no+"'>");
        $('#receivable').append("<input type='hidden' name='receivable_svrkey' value='"+receivable_svrkey+"'>");
    });
</script>
