<div class="box">
    <br />


    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )

        <div class="box-header with-border">
            <form class="form-inline" id="orderTarget"  action="" method="get" style="display: inline-block;" >
                <div class="form-group">
{{--                    <label for="exampleInputName2">乐刷订单号</label>--}}
{{--                    <input type="text" class="form-control" id="ordersn_leshua_order_id" placeholder="" name="ordersn_leshua_order_id" style="width:120px">--}}
                    &nbsp;&nbsp;&nbsp;
                    <select  id="orderSel" class="form-control" name="">
                        <option value="paymentno">
                            账号
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

                    <input type="text" class="form-control" id="orderSelValue" placeholder="" style="width:120px">

{{--                    <label for="" style="margin-left:10px">状态:</label>--}}
{{--                    <select  id="ordersn_order_status" class="form-control" name="ordersn_order_status">--}}
{{--                        <option value="">--}}
{{--                            所有--}}
{{--                        </option>--}}
{{--                        <option value="0">--}}
{{--                            未支付--}}
{{--                        </option>--}}
{{--                        <option value="1">--}}
{{--                            已支付--}}
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
