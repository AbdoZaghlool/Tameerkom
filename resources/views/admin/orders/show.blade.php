@extends('admin.layouts.master')

@section('title')
تفاصيل الطلب
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">

<style>
    #map {
        height: 450px;
        width: 700px;
    }

</style>
@endsection

@section('page_header')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin/home">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="/admin/orders">الطلبات</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض تفاصيل الطلب</span>
        </li>
    </ul>
</div>

<h1 class="page-title">عرض تفاصيل الطلب
    <small>عرض تفاصيل الطلب</small>
</h1>
@endsection

@section('content')
@include('flash::message')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="row">
                    <div class="caption font-dark">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject bold uppercase"> تفاصيل الطلب رقم {{$order->id}}</span>
                    </div>
                    <div class="caption font-weight-light float-left" style="float: left; color:red;">
                        <i class="icon-arrow-up font-dark"></i>
                        <span class="caption-subject bold uppercase"><a href="{{ url()->previous() }}">العودة للخلف</a></span>
                    </div>
                </div>
                {{-- here is more details  --}}
                <div class="row">
                    <h3></h3>
                </div>
            </div>
            <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">

                    <thead>
                        <tr>
                            <th>صورة المنتج</th>
                            <th>اسم المنتج</th>
                            <th>سعر المنتج</th>
                            <th>الاضافات الاساسية</th>
                            <th>الكمية</th>
                            <th>مكان التسليم</th>
                            <th>الملاحظات</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>

                            <td>
                                @if($order->product != null && $order->product->pictures()->first() != null)
                                <img src="{{asset('uploads/products/'.$order->product->pictures()->first()->image)}}" height="80px;" width="80px;">
                                @else
                                لا يوجد صور حاليا
                                @endif
                            </td>
                            <td>{{$order->product->name ?? 'غير متوفر'}}</td>
                            <td>{{$order->product->price ?? 0}} ريال</td>
                            <td>
                                @if($order->values != null)
                                @foreach ($order->values as $value)
                                    {{$value["property_name"] }} <span>: {{$value["name"]}}</span>
                                <br>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                {{$order->count}}
                            </td>
                            <td>
                                {{$order->recieve_place}}
                            </td>
                            <td>
                                {{$order->notes}}
                            </td>
                        </tr>

                    </tbody>

                </table>


            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->

        {{-- <div id="map"></div>  --}}


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




{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUMq5htfgLMNYvN4cuHvfGmhe8AwBeKU&callback=initMap" async
    defer></script>  --}}

@endsection
