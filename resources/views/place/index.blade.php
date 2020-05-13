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
                    <label for="exampleInputName2">key</label>
                    <input type="text" class="form-control" id="key" placeholder="" name="key" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="placename">
                            场所名称
                        </option>
                        <option value="contacts">
                            联系人
                        </option>
                        <option value="phone">
                            手机号
                        </option>
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label for="" style="margin-left:10px">地址:</label>
                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">
                        <select name="province" data-province="" style="border: none;outline: none;width:100px"></select>
                        <select name="city" data-city="" style="border: none;outline: none;"></select>
                    </div>

                    <label for="" style="margin-left:10px">状态:</label>
                    <select  id="status" class="form-control" name="status">
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
        $('#distpicker').distpicker('destroy');

        var url = new URL(location);
        var key = url.searchParams.get('key');

        $('#key').val(key);

        var placename = url.searchParams.get('placename');
        var contacts = url.searchParams.get('contacts');
        var phone = url.searchParams.get('phone');
        if(contacts!==null){
            $("#sel option[value=contacts]").prop('selected',true);
            $("#selvalue").val(contacts);
        }else if(placename!==null){
            $("#sel option[value=placename]").prop('selected',true);
            $("#selvalue").val(placename);
        }else if(phone!==null){
            $("#sel option[value=phone]").prop('selected',true);
            $("#selvalue").val(phone);
        }

        var province = url.searchParams.get('province');
        var city = url.searchParams.get('city');
        $('#distpicker').distpicker({
            province: province,
            city: city,
        });

        var status = url.searchParams.get('status');
        if(status!==null && status.length>0){
            $("#status option[value="+status+"]").prop('selected',true);

        }
    });

    $('#target').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
