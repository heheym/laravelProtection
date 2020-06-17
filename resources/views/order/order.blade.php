<div class="box">
    <br />


    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )

        <div class="box-header with-border">
            <form class="form-inline" id="target"  action="" method="get" style="display: inline-block;" >
                <div class="form-group">
                    <label for="exampleInputName2">乐刷订单号</label>
                    <input type="text" class="form-control" id="leshua_order_id" placeholder="" name="leshua_order_id" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="KtvBoxid">
                            机器码
                        </option>
{{--                        <option value="key">--}}
{{--                            key--}}
{{--                        </option>--}}
{{--                        <option value="contacts">--}}
{{--                            联系人--}}
{{--                        </option>--}}
{{--                        <option value="phone">--}}
{{--                            手机号--}}
{{--                        </option>--}}
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label for="" style="margin-left:10px">状态:</label>
                    <select  id="order_status" class="form-control" name="order_status">
                        <option value="">
                            所有
                        </option>
                        <option value="0">
                            未支付
                        </option>
                        <option value="1">
                            已支付
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
        var leshua_order_id = url.searchParams.get('leshua_order_id');

        $('#leshua_order_id').val(leshua_order_id);

        // var placename = url.searchParams.get('placename');
        // var contacts = url.searchParams.get('contacts');
        // var phone = url.searchParams.get('phone');
        var KtvBoxid = url.searchParams.get('KtvBoxid');
        var key = url.searchParams.get('key');
        if(KtvBoxid!==null){
            $("#sel option[value=KtvBoxid]").prop('selected',true);
            $("#selvalue").val(KtvBoxid);
        }else if(key!==null){
            $("#sel option[value=key]").prop('selected',true);
            $("#selvalue").val(key);
        }
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

        var order_status = url.searchParams.get('order_status');
        if(order_status!==null && order_status.length>0){
            $("#order_status option[value="+order_status+"]").prop('selected',true);

        }
    });

    $('#butt').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
        var url = new URL(location);
        var order_key = url.searchParams.get('order_key');

        $('#order_key').remove();
        $('#target').append("<input type='hidden' id='order_key' name='"+order_key+"' value='"+order_key+"'>");
    });
</script>
