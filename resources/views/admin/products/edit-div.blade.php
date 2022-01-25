<html>
    <head>
        <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    </head>
    <body>
        @foreach ($props as $prop)
        <div class='form-group col-md-4 mb-3' id='type'>
            {!! Form::label(categoryPropertiesName($prop->category_id,$prop->name).'', $prop->name); !!}
            {!! Form::select(categoryPropertiesName($prop->category_id,$prop->name).'[]', $prop->values->pluck('name','id'), $product->values,
            ['class' => 'form-control select2', 'multiple'=>true]) !!}
        </div>
        @endforeach
    
        <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
        <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
        <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
    </body>
</html>
