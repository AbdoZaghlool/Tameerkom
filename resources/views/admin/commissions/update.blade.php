@extends('admin.layouts.master')

@section('title')
تعديل حالة المدفوعات البنكية
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
@endsection

@section('page_header')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{url('/admin/home')}}">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="/admin/commissions">تعديل حالة المدفوعات البنكية</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض تعديل حالة المدفوعات البنكية</span>
        </li>
    </ul>
</div>

<h1 class="page-title">عرض تعديل حالة المدفوعات البنكية
    <small>عرض تعديل حالة المدفوعات البنكية</small>
</h1>
@endsection

@section('content')

@if (session('msg'))
<div class="alert alert-success">
    {{ session('msg') }}
</div>
@endif
@include('flash::message')

<div class="row">

    <div class="col-md-8">
        <!-- BEGIN TAB PORTLET-->
        <form method="post" action="{{route('commissions.post-update-status' , $bank->id)}}"
            enctype="multipart/form-data">
            <input type='hidden' name='_token' value='{{Session::token()}}'>


            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="row">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered table-responsive">
                        <div class="portlet-body form">
                            <div class="form-horizontal" role="form">

                                <div class="form-group">
                                    <label for="gender" class="col-md-3 control-label">اختر الحالة</label>
                                    <div class="col-lg-9">
                                        <div class=" input-group select2-bootstrap-append">
                                            <select name="status" class="form-control select2-allow-clear">
                                                <option value="0" {{ $bank->payment_status == 0 ? 'selected' : '' }}>لم
                                                    يتم الدفع</option>
                                                <option value="1" {{ $bank->payment_status ==1 ? 'selected' : '' }}>تم
                                                    الدفع</option>
                                            </select>

                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"
                                                    data-select2-open="single-append-text">
                                                    <span class="glyphicon glyphicon-search"></span>
                                                </button>
                                            </span>
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
<script src="{{ URL::asset('admin/js/datatable.js') }}"></script>
<script src="{{ URL::asset('admin/js/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/datatables.bootstrap.js') }}"></script>
<script src="{{ URL::asset('admin/js/table-datatables-managed.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>
<script>
    firebase.database().ref('AdminNotificationsAcceptAds').remove();
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');

            $('body').on('click', '.delete_offer', function() {
                var id = $(this).attr('data');

                var swal_text = 'حذف ' + $(this).attr('data_name') + '؟';
                var swal_title = 'هل أنت متأكد من الحذف ؟';

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "تأكيد",
                    cancelButtonText: "إغلاق",
                    closeOnConfirm: false
                }, function() {

                    window.location.href = "{{ url('/') }}" + "/admin/delete/"+id+"/checkCenter";


                });

            });

        });


</script>



@endsection
