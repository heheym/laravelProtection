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

{{--                    <label for="" style="margin-left:10px">地址:</label>--}}
{{--                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">--}}
{{--                        <select name="province" data-province="" style="border: none;outline: none;width:100px"></select>--}}
{{--                        <select name="city" data-city="" style="border: none;outline: none;"></select>--}}
{{--                    </div>--}}

{{--                    <label for="" style="margin-left:10px">状态:</label>--}}
{{--                    <select  id="KtvBoxState" class="form-control" name="KtvBoxState">--}}
{{--                        <option value="">--}}
{{--                            所有--}}
{{--                        </option>--}}
{{--                        <option value="0">--}}
{{--                            待审核--}}
{{--                        </option>--}}
{{--                    </select>--}}
                </div>
                <button type="submit" class="btn btn-default" id="butt">搜索</button>
            </form>

            <div class="pull-right">

                {!! $grid->renderColumnSelector() !!}
                {!! $grid->renderExportButton() !!}
                {!! $grid->renderCreateButton() !!}
            </div>
            @if ( $grid->showTools() )
                <div class="pull-right" style="margin-right:10px">
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
