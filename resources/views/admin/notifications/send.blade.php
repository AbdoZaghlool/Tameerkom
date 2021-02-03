@extends('admin.layouts.master')

@section('title')
الاشعارات
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
            <a href="/admin/notifications">الاشعارات</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>ارسال الاشعارات</span>
        </li>
    </ul>
</div>

<h1 class="page-title">ارسال الاشعارات
    <small>ارسال الاشعارات</small>
</h1>
@endsection

@section('content')
@include('flash::message')


<div class="container">

    <div class="row">
        <div class="co-md-9">
            <form action="{{route('post-send-notifications')}}" method="POST">@csrf
                <div class="form-group">
                    <label> عنوان الاشعار</label>
                    <input class="form-control" type="text" name="title" value="{{old('title')}}" required>
                    @if ($errors->has('title'))
                    <span class="help-block">
                        <strong style="color: red;">{{ $errors->first('title') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label> محتوى الاشعار</label>
                    <input class="form-control" type="text" name="body" value="{{old('body')}}" required>
                    @if ($errors->has('body'))
                    <span class="help-block">
                        <strong style="color: red;">{{ $errors->first('body') }}</strong>
                    </span>
                    @endif
                </div>
                <button class="btn btn-success" type="submit">ارسال</button>
            </form>
        </div>
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
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "تأكيد",
                    cancelButtonText: "إغلاق",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = "{{ url('/') }}" + "/admin/orders/"+id+"/delete";
                });
            });
        });
</script>

@endsection
