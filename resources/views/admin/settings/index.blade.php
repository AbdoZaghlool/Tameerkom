@extends('admin.layouts.master')

@section('title')
اعدادات التطبيق
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
            <a href="/admin/setting">اعدادات التطبيق</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>تعديل اعدادات التطبيق</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> اعدادات التطبيق
    <small>تعديل اعدادات التطبيق</small>
</h1>
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<div class="row ">

    <div class="col-md-10 text-center">
        <!-- BEGIN TAB PORTLET-->
        @if(count($errors))
        <ul class="alert alert-danger">
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
        @endif
        <form action="{{url('admin/add/settings')}}" method="post" enctype="multipart/form-data">
            <input type='hidden' name='_token' value='{{Session::token()}}'>
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
                                        <label class="col-md-3 control-label">ايميل التطبيق</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="الايميل" name="email" value="{{$settings->email ?? ''}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">رقم الهاتف</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="رقم الهاتف" name="phone" value="{{$settings->phone ?? ''}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label"> اسم البنك </label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="البنك" name="bank_name" value="{{$settings->bank_name ?? ''}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label"> رقم الحساب </label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="رقم الحساب" name="bank_number" value="{{$settings->bank_number ?? 546546654}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">الحد الاقصي للطلبات النشطة للمزود</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" placeholder="{{$settings->active_orders_count}}" name="active_orders_count" value="{{$settings->active_orders_count ?? 4}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">الحد الاقصي للعمولات الغير مدفوعة</label>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" placeholder="4" name="unpaid_commissions" value="{{$settings->unpaid_commissions ?? 4}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label"> عمولة المزودين </label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" placeholder="العمولة" name="commission" value="{{$settings->commission ?? 0.1}}">
                                        </div>
                                    </div>

                                    {{--
                            <div class="form-group">
                                <label class="col-md-3 control-label">رابط الفيس بوك</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="رابط الفيس بوك" name="face_url"
                                        value="{{$settings->face_url ?? ''}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">رابط تويتر</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="رابط تويتر" name="twiter_url" value="{{$settings->twiter_url ?? ''}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">رابط اليوتيوب</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="رابط اليوتيوب" name="youtube_url" value="{{$settings->youtube_url ?? ''}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">رابط سناب شات</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="رابط سناب شات" name="snapchat_url" value="{{$settings->snapchat_url ?? ''}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label"> الأصدار </label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="الأصدار" name="version" value="{{$settings->version ?? ''}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">رابط انستجرام</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="رابط انستجرام" name="insta_url" value="{{$settings->insta_url ?? ''}}">
                                </div>
                            </div> --}}

                            <div class="form-body">
                                <div class="form-group ">
                                    <label class="control-label col-md-3">لوجو الدخول</label>
                                    <div class="col-md-9">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                @if($settings && $settings->logo !==null)
                                                <img src="{{ asset('images/'.$settings->logo) }}">
                                                @endif
                                            </div>
                                            <div>
                                                <span class="btn red btn-outline btn-file">
                                                    <span class="fileinput-new"> اختر الصورة </span>
                                                    <span class="fileinput-exists"> تغيير </span>
                                                    <input type="file" name="logo"> </span>
                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> إزالة </a>
                                            </div>
                                        </div>
                                        @if ($errors->has('logo'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('logo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <button type="submit" class="btn green">حفظ</button>
        </div>
    </div>
</div>
</form>
<!-- END TAB PORTLET-->

</div>
</div>

@endsection

@section('scripts')
<script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>
@endsection
