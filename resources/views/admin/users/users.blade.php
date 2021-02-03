@extends('admin.layouts.master')

@section('title')
المستخدمين
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('page_header')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin/home">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="/admin/users">المستخدمين</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض المستخدمين</span>
        </li>
    </ul>
</div>

<h1 class="page-title">عرض المستخدمين
    <small>عرض جميع المستخدمين</small>
</h1>
@endsection

@section('content')

@include('flash::message')

<div class="row">
    <div class="col-lg-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered table-responsive">
            <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="btn-group">
                                <a class="btn sbold green" href="{{route('users.create',['type'=> 0])}}"> إضافة جديد
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover table-checkable order-column"
                    id="sample_1">
                    <thead>
                        <tr>
                            <th>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />
                                    <span></span>
                                </label>
                            </th>
                            
                            <th> رقم العميل</th>
                            <th> الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>التفعيل</th>
                            {{--  <th>الحظر</th>  --}}
                            {{--  <th>التوثيق</th>  --}}
                            <th> العمليات </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=0 ?>
                        @foreach($users as $user)
                        <tr class="odd gradeX">
                            <td>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="checkboxes" value="1" />
                                    <span></span>
                                </label>
                            </td>
                            
                            <td> {{$user->id}} </td>
                            <td> {{$user->name}} </td>
                            <td>{{$user->phone_number}}</td>

                            @if($user->active == 0)
                            <td><button type="button" class="btn btn-circle red btn-sm">غير مفعل</button></td>
                            @else
                            <td><button type="button" class="btn btn-circle blue btn-sm"> مفعل</button></td>
                            @endif

                            <!--
                                <td>
                                    <input type="checkbox" id="activation-{{$user->id}}"
                                        onchange="testActive({{$user->blocked}},{{$user->id}})"
                                        {{$user->blocked == 1 ? 'checked' : ''}} data-toggle="toggle">
                                </td>
                            -->


                            {{--  @if($user->verified == 0)
                            <td><button type="button" class="btn btn-circle red btn-sm">غير موثق</button></td>
                            @else
                            <td><button type="button" class="btn btn-circle blue btn-sm"> موثق</button></td>
                            @endif  --}}

                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs green dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false"> العمليات
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-left" role="menu">
                                        <li>
                                            <a href="{{route('users.edit',['id' => $user->id])}}">
                                                <i class="icon-docs"></i> تعديل </a>
                                        </li>

                                        <li>
                                            <a class="delete_user" data="{{ $user->id }}"
                                        data_name="{{ $user->name }}">
                                        <i class="fa fa-key"></i> مسح
                                        </a>
                                        </li>
                                        

                                    </ul>
                                </div>
                            </td>
                        </tr>
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
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    function testActive(state, id){
        $.ajax({
            url: 'update/blocked/'+id,
            type: 'GET',
            datatype: 'json',
            success: function (data) {
                console.log(data);
            }
        });

    }
</script>
<script>
    $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');
            $('body').on('click', '.delete_user', function() {
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
                    window.location.href = "{{ url('/') }}" + "/admin/delete/"+id+"/user";
                });
            });
        });
</script>

@endsection
