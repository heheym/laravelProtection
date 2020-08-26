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
                    <label for="exampleInputName2">场所名称</label>
                    <input type="text" class="form-control" id="placename" placeholder="" name="placename" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="roomno">
                            房号
                        </option>
{{--                        <option value="place_contacts">--}}
{{--                            联系人--}}
{{--                        </option>--}}
{{--                        <option value="place_phone">--}}
{{--                            手机号--}}
{{--                        </option>--}}
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label for="" style="margin-left:10px">地址:</label>
                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">
                        <select name="province" data-province="" style="border: none;outline: none;width:100px"></select>
                        <select name="city" data-city="" style="border: none;outline: none;"></select>
                    </div>

                    <label style="margin-left:10px">支付时间</label>
                    <input type="text" class="form-control" id="pay_time_start" placeholder="上传时间"
                           name="pay_time[start]" value="">
                    -
                    <input type="text" class="form-control" id="pay_time_end" placeholder="上传时间"
                           name="pay_time[end]" value="">

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
        $('#distpicker').distpicker('destroy');

        var province = url.searchParams.get('province');
        var city = url.searchParams.get('city');
        $('#distpicker').distpicker({
            province: province,
            city: city,
        });

        var placename = url.searchParams.get('placename');

        $('#placename').val(placename);

        var roomno = url.searchParams.get('roomno');
        if(roomno!==null){
            $("#sel option[value=roomno]").prop('selected',true);
            $("#selvalue").val(roomno);
        }

        var pay_time_start = url.searchParams.get('pay_time[start]');
        var pay_time_end = url.searchParams.get('pay_time[end]');
        if (pay_time_start !== null) {
            $('#pay_time_start').val(pay_time_start);
        }
        if (pay_time_end !== null) {
            $('#pay_time_end').val(pay_time_end);
        }
    });

   

    $('#butt').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
