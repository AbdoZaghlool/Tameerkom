@extends('admin.layouts.master')

@section('title')
العمولات المستحقة
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
            <a href="/admin/commissions">العمولات المستحقة</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض العمولات المستحقة</span>
        </li>
    </ul>
</div>

<h1 class="page-title">عرض العمولات المستحقة
    <small>عرض جميع العمولات المستحقة</small>
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
                    <span class="caption-subject bold uppercase"> العمولات المستحقة</span>
                </div>

            </div>
            <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المزود</th>
                            <th>العميل </th>
                            <th>تاريخ الطلب</th>
                            <th> السعر(ريال) </th>
                            <th> العمولة(ريال) </th>
                            <th> الصورة </th>
                            <th> خيارات </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $i=0 ?>
                        @foreach($records as $order)
                        @if ($order->payment_status === 0)
                        <tr class="odd gradeX">
                            <td> {{$order->id}}</td>
                            <td>
                                <a href="{{route('users.edit',$order->provider->id)}}">{{$order->provider->name}}</a>
                            </td>
                            <td> {{$order->user->name}}</td>
                            <td> {{$order->created_at->format('Y-m-d')}}</td>
                            <td> {{ convertArabicNumbersToEnglish($order->price) }} (ريال) </td>
                            <td> {{ $order->tax}} (ريال) </td>
                            <td>
                                @if ($order->payment_image)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal{{$order->id}}">
                                    عرض الصورة
                                </button>
                                <div class="modal fade" id="exampleModal{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">صورة الدفع</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{asset('uploads/payment_images/'.$order->payment_image)}}" style="height: 300px; width: 300px;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                لا توجد صورة
                                @endif

                            </td>

                            <td>
                                <input type='hidden' name='_token' value='{{Session::token()}}'>
                                <span class="input-group-btn">
                                    <button class="btn btn-success sendConfirmation" type="button" data-name="{{$order->name}}" data-id="{{$order->id}}">
                                        تغيير الحالة لمدفوع
                                    </button>
                                </span>
                            </td>

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
        var CSRF_TOKEN = $('input[name="_token"]').val();
        $('body').on('click', '.sendConfirmation', function() {
            var id = $(this).attr('data-id');
            var swal_text = 'تأكيد حالة الدفع ل  ' + $(this).attr('data-name') + '؟';
            var swal_title = 'هل أنت متأكد من تأكيد حالة الدفع ؟';
            swal({
                title: swal_title
                , text: swal_text
                , type: "success"
                , showCancelButton: true
                , confirmButtonClass: "btn-success"
                , confirmButtonText: "تأكيد"
                , cancelButtonText: "إغلاق"
                , closeOnConfirm: false
            }, function() {
                //commissions/{id}/update-status
                //window.location.href = "{{ url('/') }}" + "/admin/commissions/" + id + "/update-status";
                $.ajax({
                    url: "{{ url('/') }}" + "/admin/commissions/" + id + "/update-status"
                    , type: 'post'
                    , data: {
                        "_token": "{{ csrf_token() }}"
                    , }
                    , datatype: 'json'
                    , success: function(data) {
                        swal({
                            title: 'تم التعديل'
                            , text: 'تم تعديل حالة العمولة بنجاح'
                            , type: "success"
                        , });
                        window.location.reload();
                    }
                    , error: function(error) {
                        console.log(error);
                        swal({
                            title: 'حدث خطأ'
                            , text: 'حدث خطأ ما برجاء المحاولة لاحقا'
                            , type: "error"
                        , });
                    }
                });
            });
        });
    });

</script>

@endsection
