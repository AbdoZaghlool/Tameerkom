@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger">{{$error}}</div>
    @endforeach
@endif
<div>
    <div class="form-group mb-3">
        {!! Form::label('name', 'الاسم', ['class' => 'form-label']); !!}
        {!! Form::text('name', null, ['class'=>'form-control', 'readonly'=>$readonly]); !!}
        @error('name')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-3">
        {!! Form::label('details', 'تفاصيل المنتج', ['class' => 'form-label']); !!}
        {!! Form::text('details', null, ['class'=>'form-control', 'readonly'=>$readonly]); !!}
        @error('details')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-3">
        {!! Form::label('price', 'سعر المنتج', ['class' => 'form-label']); !!}
        {!! Form::text('price', null, ['class'=>'form-control', 'readonly'=>$readonly]); !!}
        @error('price')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-3">
        {!! Form::label('provider_id', 'المصنع'); !!}
        {!! Form::select('provider_id', providers(), null,
        ['class' => 'form-control','disabled'=>$readonly, 'placeholder' => 'اختر قيمة']) !!}
        @error('provider_id')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>


    <div class="form-group mb-3">
        {!! Form::label('category_id', 'القسم'); !!}
        {!! Form::select('category_id', categories(), null,
        ['class' => 'form-control','disabled'=>$readonly, 'placeholder' => 'اختر قيمة']) !!}
        @error('category_id')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="row" id="roo">

    </div>

    <div class="form-group mb-3">
        {!! Form::label('image', 'الصور'); !!}
        {!! Form::file('image[]',
        ['class' => 'form-control','disabled'=>$readonly,
        'placeholder' => 'اختر قيمة', 'multiple'=>true]) !!}
        @error('image')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="row">
        @isset($product)
            @if ($product->pictures)
                @foreach ($product->pictures as $item)
                <div class="col-md-3 img_{{ $item->id }}">
                    <p><img src="{{ asset('uploads/products/'.$item->image) }}" class="img-fluid" height="150" width="150" id="file_name"></p>
                    <a id="{{ $item->id }}" style="color: white;text-decoration: none;" class="delete_image hideDiv btn btn-danger">
                        <i class="glyphicon glyphicon-trash "></i> مسح</a>
                </div>
                @endforeach
            @endif
        @endisset
    </div>




    @if(!$readonly)
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"> حفظ</button>
    </div>
    @endif
</div>

@section('scripts')
<script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
<script>
    $(document).ready(function() {

        var product_id = $('input[name="product_id"]').val();
        if(product_id)
        {
            $.ajax({
                url: "/product-values/"+product_id,
                type: 'GET',
                datatype: 'json',
                success: function (data) {
                    $("#roo").append(data);
                },
                error: function(error){
                    console.log(error);
                }
            });
        
        }

        $('select[name="category_id"]').on('change', function() {
            var id = $(this).val();
            $.ajax({
                url: "/category-properties/" + id,
                type: 'GET',
                datatype: 'json',
                success: function (data) {
                    $("#roo").empty();
                    $("#roo").append(data);
                },
                error: function(error){
                    console.log(error);
                }
            });

        });

    });

</script>
<script>
    $(".delete_image").click(function() {
        var id = $(this).attr('id');
        var url = '{{ route("imageIntroRemove", ":id") }}';
        url = url.replace(':id', id);
        //alert(image_id );
        $.ajax({
            url: url
            , type: 'GET'
            , success: function(result) {
                if (!result.message) {
                    $(".img_" + id).fadeOut('1000');
                }

            }
        });
    });

</script>
@endsection