@extends('admin.layouts.master')

@section('title')
تعديل قسم
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
            <span>تعديل قسم</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> الاقسام
    <small>تعديل قسم</small>
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
                        {!! Form::model($product, ['route' => ['products.update', $product]]) !!}
                        @method('PUT')

                        @include('admin.products._form',['readonly' =>false])

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

@endsection