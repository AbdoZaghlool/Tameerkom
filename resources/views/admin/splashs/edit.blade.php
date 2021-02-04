@extends('admin.layouts.master')

@section('title')
تعديل بانر
@endsection
@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
@endsection

@section('page_header')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin/home">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>

        <li>
            <a href="{{ route('splashs.index') }}">البنرات</a>
            <i class="fa fa-circle"></i>
        </li>


        <li>
            <span> تعديل بانر </span>
        </li>
    </ul>
</div>

<h1 class="page-title"> تعديل بانر
    <small> تعديل بانر </small>
</h1>
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<div class="row">

    <div class="col-md-8">
        <!-- BEGIN TAB PORTLET-->
        @if(count($errors))
        <ul class="alert alert-danger">
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
        @endif
        @include('flash::message')
        {!! Form::model($splash,['action'=>['AdminController\SplashController@update'
        ,$splash->id],'method'=>'PUT', 'enctype' => 'multipart/form-data', 'id'=>'main_form' ]) !!}

        {{-- @method('put') --}}
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->

            <div class="row">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered table-responsive">
                    <div class="portlet-body form">
                        <div class="form-horizontal" role="form">
                            <div class="form-body">

                                <div class="form-group">
                                    <label for="link" class="control-label">اللينك</label>
                                    <input type="text" name="link" id="link" class="form-control" value="{{$splash->link}}">
                                    @error('link')
                                    <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('link') }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="control-label">المنتجات </label>
                                    {!! Form::select('product_id', App\Product::pluck('name','id'),
                                    $splash->product_id, ['class' => 'form-control','placeholder'=> 'اختر']) !!}
                                    @if ($errors->has('product_id'))
                                    <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('product_id') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-body">
                                    <div class="form-group ">
                                        <label class="control-label col-md-3">الصورة </label>
                                        <div class="col-md-9">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px; ">
                                                    <img src="{{asset('uploads/sliders/'.$splash->image)}}" alt="">
                                                </div>
                                                <div>
                                                    <span class="btn red btn-outline btn-file">
                                                        <span class="fileinput-new"> اختر الصورة </span>
                                                        <span class="fileinput-exists"> تغيير </span>
                                                        <input type="file" name="image"> </span>
                                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>
                                                </div>
                                            </div>
                                            @if ($errors->has('image'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('image') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- END SAMPLE FORM PORTLET-->
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->

        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button onclick="subForm();" type="submit" class="btn green">حفظ</button>
                </div>
            </div>
        </div>
        </form>
        <!-- END TAB PORTLET-->





    </div>
</div>

@endsection

@section('scripts')

<script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

<script src="{{ URL::asset('admin/ckeditor/ckeditor.js') }}"></script>
<script>
    function subForm() {
        var form = document.getElementById('main_form');
        form.submit();
    }

</script>

<script>
    function sendOptions() {
        var fd = $("form").serialize();
        $.ajax({
            url: '/get-filterd-users'
            , data: fd
            , processData: false
            , contentType: false
            , type: 'get'
            , success: function(data) {

                $('select[name="provider_id"]').empty();
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        {
                            {
                                --console.log(value);
                                --
                            }
                        }
                        $('select[name="provider_id"]')
                            .append('<option value="' + value['id'] + '">' + value['name'] + '</option>');
                    });
                } else {
                    $('select[name="provider_id"]')
                        .append('<option value="0"> لا يوجد نتائج تطابق هذة الاختيارات </option>');
                }
            }
        });
    }

</script>

@endsection
