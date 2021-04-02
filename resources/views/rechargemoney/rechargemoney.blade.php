<div class="box">
    <br />
    @if(isset($title))
    <div class="box-header with-border">
        <h3 class="box-title"> {{ $title }}</h3>
    </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )
    <div class="box-header with-border">
        <form class="form-inline" id="receipt" action="" method="get" style="display:inline-block;">
            <div class="form-group">
                <label style="margin-left:10px">充值时间</label>
                <input type="text" class="form-control" id="rechargeDate_start" placeholder="充值时间"
                       name="rechargeDate_start" value="">
                -
                <input type="text" class="form-control" id="rechargeDate_end" placeholder="充值时间"
                       name="rechargeDate_end" value="">
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

    {{--{!! $grid->renderFilter() !!}--}}

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
    var rechargeDate_start = url.searchParams.get('rechargeDate_start');
    var rechargeDate_end = url.searchParams.get('rechargeDate_end');
    if (rechargeDate_start !== null) {
        $('#rechargeDate_start').val(rechargeDate_start);
    }
    if (rechargeDate_end !== null) {
        $('#rechargeDate_end').val(rechargeDate_end);
    }
})
</script>
