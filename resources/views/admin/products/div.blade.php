@foreach ($props as $prop)

<div class='form-group col-md-4 mb-3' id='type'>
    {!! Form::label('property_value_id', $prop->name); !!}
    {!! Form::select('property_value_id', $prop->values->pluck('name','id'), null,
    ['class' => 'form-control select2', 'multiple'=>true,
    'placeholder' => 'اختر قيمة اواكثر']) !!}
</div>
@endforeach