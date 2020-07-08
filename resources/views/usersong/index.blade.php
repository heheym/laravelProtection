<div class="box">
    <br />


    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )

        <div class="box-header with-border">
            <form class="form-inline" id="target" action="" method="get" style="display: inline-block;">
                <div class="form-group">
                    <label for="exampleInputName2">srvkey</label>
                    <input type="text" class="form-control" id="srvkey" placeholder="" name="srvkey" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="placename">
                            场所名称
                        </option>
                        <option value="KtvBoxid">
                            机器码
                        </option>
                        <option value="roomno">
                            房号
                        </option>
                        <option value="musicdbpk">
                            musicdbpk
                        </option>
                        <option value="RecordCompany">
                            唱片公司
                        </option>

                    </select>
                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

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
        var srvkey = url.searchParams.get('srvkey');

        $('#srvkey').val(srvkey);

        var placename = url.searchParams.get('placename');
        var KtvBoxid = url.searchParams.get('KtvBoxid');
        var roomno = url.searchParams.get('roomno');
        var musicdbpk = url.searchParams.get('musicdbpk');
        var RecordCompany = url.searchParams.get('RecordCompany');
        if(placename!==null){
            $("#sel option[value=placename]").prop('selected',true);
            $("#selvalue").val(placename);
        }else if(KtvBoxid!==null){
            $("#sel option[value=KtvBoxid]").prop('selected',true);
            $("#selvalue").val(KtvBoxid);
        }else if(roomno!==null){
            $("#sel option[value=roomno]").prop('selected',true);
            $("#selvalue").val(roomno);
        }else if(musicdbpk!==null){
            $("#sel option[value=musicdbpk]").prop('selected',true);
            $("#selvalue").val(musicdbpk);
        }else if(RecordCompany!==null){
            $("#sel option[value=RecordCompany]").prop('selected',true);
            $("#selvalue").val(RecordCompany);
        }

    });

    $('#target').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
