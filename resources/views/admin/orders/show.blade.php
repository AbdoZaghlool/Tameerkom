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

                <!-- 
                    <table class="table table-striped table-bordered table-hover table-checkable order-column"
                        id="sample_1">
                        <thead>
                            <tr>
                                <th> رقم الطلب</th>
                                <th> المزودينة</th>
                                <th> العميل </th>
                                <th> السائق </th>
                                <th> حالة الطلب </th>
                                <th> وقت التسليم </th>
                                <th> السعر(ريال) </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i=0 ?>
                            <tr class="odd gradeX">
                                <td>{{$order->id}}</td>
                                <td>{{$order->provider->name}}</td>
                                <td>{{$order->user->name}}</td>
                                <td>{{$order->driver_id == null ? ($order->delivery_type ?? 'لم يحدد بعد' ) : $order->driver->name }}</td>
                                <td>
                                    @if($order->status == '0')
                                    <button type="button" class="btn btn-circle green btn-sm">جديد</button>
                                    @elseif($order->status == '1')
                                    <button type="button" class="btn btn-circle purple btn-sm">مقبول</button>
                                    @elseif($order->status == '2')
                                    <button type="button" class="btn btn-circle purple btn-sm">نشط</button>
                                    @elseif($order->status == '3')
                                    <button type="button" class="btn btn-circle purple btn-sm">مكتمل</button>
                                    @endif
                                </td>
                                <td> {{$order->recieve_at ?? '' }} </td>
                                <td> {{ convertArabicNumbersToEnglish($order->price) }} </td>
                                
                            </tr>
                        </tbody>

                    </table>
                -->


                @php
                $orderItems = unserialize($order->cart_items);
                $userAddress = App\UserAdresses::find($order->user_adresses_id) ;
                $family = $order->provider;
                @endphp
                <!-- make table for products in order -->

                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>صورة المنتج</th>
                            <th>اسم المنتج</th>
                            <th>سعر المنتج</th>
                            <th>الاضافات الاساسية</th>
                            <th>الاضافات الجانبية</th>
                            <th>الكمية</th>
                            <th>الملاحظات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orderItems as $item)
                        @php
                        $product = App\Product::find($item->product_id);
                        @endphp
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                @if($product != null && $product->pictures()->first() != null)
                                <img src="{{asset('uploads/products/'.$product->pictures()->first()->image)}}" height="100px;" width="100px;">
                                @else
                                المنتج غير متوفر حاليا
                                @endif
                            </td>
                            <td>{{$product->name ?? 'غير متوفر'}}</td>
                            <td>{{$product->price ?? 0}} ريال</td>
                            <td>
                                @if($item->additions != null)
                                @foreach ($item->getAdditions($item->additions) as $addition)
                                {{$addition['name'] }} ( {{($addition['price'])}} ريال) <br>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                @if($item->more_additions != null)
                                @foreach ($item->getAdditions($item->more_additions) as $addition)
                                {{$addition['name'] }} ( {{($addition['price'])}} ريال) <br>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                {{$item->quantity}}
                            </td>
                            <td>
                                {{$item->notes}}
                            </td>
                        </tr>
                        @endforeach
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
