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
                    <label for="exampleInputName2">key</label>
                    <input type="text" class="form-control" id="place_key" placeholder="" name="place_key" style="width:120px">
                    &nbsp;&nbsp;&nbsp;
                    <select  id="sel" class="form-control">
                        <option value="place_placename">
                            场所名称
                        </option>
                        <option value="place_contacts">
                            联系人
                        </option>
                        <option value="place_phone">
                            手机号
                        </option>
                    </select>

                    <input type="text" class="form-control" id="selvalue" placeholder="" style="width:120px">

                    <label for="" style="margin-left:10px">地址:</label>
                    <div data-toggle="distpicker" data-value-type="code" class="form-control" id="distpicker">
                        <select name="place_province" data-province="" style="border: none;outline: none;width:100px"></select>
                        <select name="place_city" data-city="" style="border: none;outline: none;"></select>
                    </div>

                    <label for="" style="margin-left:10px">状态:</label>
                    <select  id="place_status" class="form-control" name="place_status">
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
        var url = new URL(location);
        $('#distpicker').distpicker('destroy');

        var province = url.searchParams.get('place_province');
        var city = url.searchParams.get('place_city');
        $('#distpicker').distpicker({
            province: province,
            city: city,
        });


        var place_key = url.searchParams.get('place_key');

        $('#place_key').val(place_key);

        var place_placename = url.searchParams.get('place_placename');
        var place_contacts = url.searchParams.get('place_contacts');
        var place_phone = url.searchParams.get('place_phone');
        if(place_contacts!==null){
            $("#sel option[value=place_contacts]").prop('selected',true);
            $("#selvalue").val(place_contacts);
        }else if(place_placename!==null){
            $("#sel option[value=place_placename]").prop('selected',true);
            $("#selvalue").val(place_placename);
        }else if(place_phone!==null){
            $("#sel option[value=place_phone]").prop('selected',true);
            $("#selvalue").val(place_phone);
        }



        var place_status = url.searchParams.get('place_status');
        if(place_status!==null && place_status.length>0){
            $("#place_status option[value="+place_status+"]").prop('selected',true);
        }
    });

    $('#butt').click(function() {
        var sel =$("#sel option:selected").val();
        var selvalue = $("#selvalue").val();
        $('#selhidden').remove();
        $('#target').append("<input type='hidden' id='selhidden' name='"+sel+"' value='"+selvalue+"'>");
    });
</script>
