<div class="box">
    <br />


    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )

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

{{--                    <label for="" style="margin-left:10px">地址:</label>--}}
{{--                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">--}}
{{--                        <select name="settopbox_province" data-province="" style="border: none;outline: none;width:100px"></select>--}}
{{--                        <select name="settopbox_city" data-city="" style="border: none;outline: none;"></select>--}}
{{--                    </div>--}}

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
