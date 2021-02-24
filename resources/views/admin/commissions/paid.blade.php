@extends('admin.layouts.master')

@section('title')
المدفوعات
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
            <a href="/admin/home">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="/admin/commissions">المدفوعات</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض المدفوعات</span>
        </li>
    </ul>
</div>

<h1 class="page-title">عرض المدفوعات
    <small>عرض جميع المدفوعات</small>
</h1>
@endsection

@section('content')
@include('flash::message')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject bold uppercase"> المدفوعات</span>
                </div>

            </div>
            <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المزود</th>
                            <th> العميل </th>
                            <th>تاريخ الطلب</th>
                            <th> السعر </th>
                            <th> العمولة</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($records as $order)
                        @if ($order->payment_status === 1)
                        <tr class="odd gradeX">
                            <td> {{$order->id}}</td>
                            <td> {{$order->provider->name}}</td>
                            <td> {{$order->user->name}}</td>
                            <td> {{$order->created_at->format('Y-m-d')}}</td>
                            <td> {{convertArabicNumbersToEnglish($order->price)}} (ريال) </td>
                            <td> {{ convertArabicNumbersToEnglish($order->tax)}} (ريال) </td>
                        </tr>
                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
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
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');
        $('body').on('click', '.delete_attribute', function() {
            var id = $(this).attr('data');
            var swal_text = 'حذف ' + $(this).attr('data_name') + '؟';
            var swal_title = 'هل أنت متأكد من الحذف ؟';
            swal({
                title: swal_title
                , text: swal_text
                , type: "warning"
                , showCancelButton: true
                , confirmButtonClass: "btn-warning"
                , confirmButtonText: "تأكيد"
                , cancelButtonText: "إغلاق"
                , closeOnConfirm: false
            }, function() {
                window.location.href = "{{ url('/') }}" + "/admin/orders/" + id + "/delete";
            });
        });
    });

</script>

@endsection
