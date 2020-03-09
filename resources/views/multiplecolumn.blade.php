@php
    $rand = mt_rand();
@endphp
<div class="form-group  multiColumn{{ $rand }}">
    <label class="col-sm-2 control-label">{{$label}}</label>
    @foreach($fields as $field)
        <div class="col-sm-{{ $field['width'] }}">
            {!! $field['element']->render() !!}
        </div>
    @endforeach
</div>
<script>
    for (var i=0; i<{{ count($fields) }}; i++) {
        $('.multiColumn{{ $rand }} ').prev().remove();
        $('.multiColumn{{ $rand }} .form-group').css('margin-bottom','initial');
    }
</script>