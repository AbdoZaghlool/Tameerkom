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
        {!! Form::file('image',
        ['class' => 'form-control','disabled'=>$readonly,
        'placeholder' => 'اختر قيمة', 'multiple'=>true]) !!}
        @error('image')
        <span class="help-block">
            <strong style="color: red;">{{ $message }}</strong>
        </span>
        @enderror
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

        $('select[name="category_id"]').on('change', function() {
            var id = $(this).val();
            console.log(id);
            
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
@endsection