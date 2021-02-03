@extends('admin.layouts.master')

@section('title')
الاشعارات لمستخدم معين
@endsection

@section('styles')

<link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
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
            <a href="/admin/notifications/user">الاشعارات لمستخدم معين</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>ارسال الاشعارات لمستخدم معين</span>
        </li>
    </ul>
</div>

<h1 class="page-title">ارسال الاشعارات لمستخدم معين
    <small>ارسال الاشعارات لمستخدم معين</small>
</h1>
@endsection

@section('content')
@include('flash::message')


<div class="container">

    <div class="row">
        <div class="co-md-9">
            <form action="{{route('post-send-user-notifications')}}" method="POST">@csrf
                <div class="form-group">
                    <label>اختر مستخدم</label>
                    {!! Form::select('user_id[]', 
                    [
                        'العملاء'        => App\User::where('type','0')->pluck('name','id'), 
                        'الاسر المنتجة' => App\User::where('type','1')->pluck('name','id'), 
                        'السائقين'      => App\User::where('type','2')->pluck('name','id'), 
                    ], 
                    null,
                    ['class'=>'form-control select2','multiple']) !!}
                    @if ($errors->has('user_id'))
                    <span class="help-block">
                        <strong style="color: red;">{{ $errors->first('user_id') }}</strong>
                    </span>
                    @endif
                </div>
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
<script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
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
