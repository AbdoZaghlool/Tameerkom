@extends('admin.layouts.master')

@section('title')
    المشرفين
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ url('admin/home') }}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ route('admins.index') }}">المشرفين</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض المشرفين</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض المشرفين
        <small>تعديل مشرف</small>
    </h1>
@endsection

@section('content')

    @if(session()->has('msg'))

        <p class="alert alert-success" style="width: 100%">

            {{ session()->get('msg') }}

        </p>
    @endif

    <form class="form-horizontal" method="post" action="{{ url('/admin/admins/'. $data->id) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PATCH">

        <div class="row">
            <div class="col-lg-8">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> البيانات الرئيسية</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="btn-group"></div>



                        <div class="form-group">
                            <label for="username" class="col-lg-3 control-label">الاسم</label>
                            <div class="col-lg-9">
                                <input id="username" name="name" type="text" value="{{ $data->name }}" class="form-control" placeholder="الاسم">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-lg-3 control-label">الايميل</label>
                            <div class="col-lg-9">
                                <input id="email" name="email" type="email" value="{{ $data->email }}" class="form-control" placeholder="الايميل">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $roleId = $data->roles()->first() == null ? 0: $data->roles()->first()->id
                        @endphp
                        <div class="form-group">
                            <label for="role_id" class="control-label col-lg-3 ">الصلاحيات</label>
                            <div class="col-lg-9">
                                {!! 
                                    Form::select('role_id', \App\Role::pluck('name','id'), $roleId, 
                                    ['class'=> 'form-control ','id'=>'role_id', 'placeholder' => 'اختر صلاحية المدير']) 
                                !!}
                                @error('role_id')
                                    <span class="help-block">
                                        <strong style="color: red;">{{ $errors->first('role_id') }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="col-lg-3 control-label">الهاتف</label>
                            <div class="col-lg-9">
                                <input id="phone" name="phone" type="text" value="{{ $data->phone }}" class="form-control" placeholder="الهاتف">
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        
                        <div class="form-body">
                            <div class="form-group ">
                                <label class="control-label col-md-3">الصورة </label>
                                <div class="col-md-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                            data-trigger="fileinput"
                                            style="width: 200px; height: 150px; ">
                                            <img src="{{ asset('uploads/admins/'.$data->image) }}" alt="">
                                        </div>
                                        <div>
                                            <span class="btn red btn-outline btn-file">
                                                <span class="fileinput-new"> اختر الصورة </span>
                                                <span class="fileinput-exists"> تغيير </span>
                                                <input type="file" name="image"> </span>
                                            <a href="javascript:;" class="btn red fileinput-exists"
                                                data-dismiss="fileinput"> إزالة </a>
                                        </div>
                                    </div>
                                    @if ($errors->has('image'))
                                    <span class="help-block">
                                        <strong
                                            style="color: red;">{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="clear: both"></div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-lg-2 col-lg-offset-10">
                                    {{--<button type="submit" class="btn green btn-block">حفظ</button>--}}
                                    <input class="btn green btn-block" type="submit" value="حفظ" onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{--{!! Form::close() !!}--}}
@endsection

@section('scripts')
    <script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
@endsection

