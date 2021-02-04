@extends('admin.layouts.master')

@section('title')
اضافة تصنيف
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
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
            <a href="/admin/main-categories">الاقسام</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>اضافة تصنيف</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> الاقسام
    <small>اضافة تصنيف</small>
</h1>
@endsection

@section('content')
@include('flash::message')


<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
<div class="row">
    <div class="col-md-12">

        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <form role="form" action="{{route('main-categories.store')}}" method="post" enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>


                            <div class="portlet-body">

                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="tab_1_1">

                                        <div class="form-group">
                                            <label class="control-label"> اسم القسم </label>
                                            <input type="text" name="name" class="form-control" placeholder="أسم القسم" />
                                            @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        {{-- <!-- image -->  --}}
                                        <div class="form-body">
                                            <div class="form-group {{$errors->has('image')?'has-error':''}} ">
                                                <label class="control-label col-md-3">الصورة الشخصية</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                        </div>
                                                        <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="image"> </span>
                                                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>
                                                        </div>
                                                    </div>
                                                    @error('image')
                                                    <span class="status-error">{{ $errors->first('image') }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="margiv-top-10">
                                <div class="form-actions">
                                    <button type="submit" class="btn green" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

@endsection
