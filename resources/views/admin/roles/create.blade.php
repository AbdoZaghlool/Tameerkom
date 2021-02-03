@extends('admin.layouts.master')


@section('title')
اضافة رتبة
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
            <a href="/admin/roles">الرتب</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>اضافة رتبة</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> الرتب
    <small>اضافة رتبة</small>
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

                        {!! Form::open( ['action'=>'AdminController\RoleController@store']) !!}

                        <div class="form-group">
                            <label for="title">الاسم</label>
                            {!! Form::text('name', null, ['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for="content">التفاصيل</label>
                            {!! Form::text('display_name', null, ['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <div class="row">

                                @forelse ($permissions as $key => $value)
                                    <div class="col-sm-3">
                                        <div class="form-check">

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permission_list[]" value="{{$value}}" class="custom-control-input" id="customCheck{{$value}}">
                                            <label class="custom-control-label" for="customCheck{{$value}}">{{$key}}</label>
                                        </div>
                                        </div>
                                    </div>

                                @empty
                                    echo ('no permissions yet !');
                                @endforelse
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>

                        {!! Form::close() !!}

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



