<div class="modal" tabindex="-1" role="dialog" id="{{ $modal_id }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <form>
            <div class="modal-body">
                <div class="col-md-6">
                    @foreach($fields as $k=>$field)
                        @if($k<=3)
                            {!! $field->render() !!}
                        @endif
                    @endforeach
                </div>

                <div class="col-md-6">
                    @foreach($fields as $k=>$field)
                        @if($k>3)
                            {!! $field->render() !!}
                        @endif
                    @endforeach
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('admin.close') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('admin.submit') }}</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->