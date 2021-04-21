<html>
    <head>
        <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    </head>
    <body>
        @foreach ($props as $prop)
        <div class='form-group col-md-4 mb-3' id='type'>
            {!! Form::label('property_value_id', $prop->name); !!}
            {!! Form::select('property_value_id[]', $prop->values->pluck('name','id'), null,
            ['class' => 'form-control select2', 'multiple'=>true]) !!}
        </div>
        @endforeach
    
        <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
        <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
        <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    </body>
</html>
