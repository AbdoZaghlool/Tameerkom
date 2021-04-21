@extends('admin.layouts.master')

@section('title')
المنتجات
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/datatables.bootstrap-rtl.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
@endsection

@section('page_header')
{{-- bread crumb section  --}}
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ url('admin/home') }}">لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ url('admin/products') }}">المنتجات</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>عرض المنتجات</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> المنتجات
    {{--<small>عرض جميع المنتجات</small>--}}
</h1>
@include('flash::message')
@endsection
@section('content')

<div class="row">
    <div class="col-lg-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="btn-group">
                                <a class="btn sbold green" href="{{ route('products.create') }}"> إضافة جديد
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
                            <th>ID</th>
                            <th> أسم المنتج </th>
                            <th>تفاصيل المنتج</th>
                            <th>سعر المنتج</th>
                            <th>القسم</th>
                            <th>المزود</th>
                            <th> العمليات </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach( $products as $product )
                        <tr class="odd gradeX">
                            <td>{{$product->id}}</td>
                            <td class="no_dec">{{ $product->name }}</td>
                            <td class="no_dec">{{ $product->details }}</td>
                            <td class="no_dec">{{ $product->price }}</td>

                            <td class="no_dec">
                                <a href="{{route('main-categories.edit',$product->category->id)}}">
                                    {{ $product->category->name }}
                                </a>
                            </td>

                            <td class="no_dec">
                                <a href="{{route('users.edit',$product->provider->id)}}">
                                    {{ $product->provider->name }}
                                </a>
                            </td>

                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs green dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false"> العمليات
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-left" role="menu">

                                        <li>
                                            <a href="{{ route('products.edit',['id'=>$product->id]) }}">
                                                <i class="icon-pencil"></i> تعديل
                                            </a>
                                        </li>
                                        @if ($product->id > 3)
                                        <li>
                                            <a class="delete_data" data="{{ $product->id }}"
                                                data_name="{{ $product->name }}">
                                                <i class="fa fa-times"></i> حذف
                                            </a>
                                        </li>
                                        @endif
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
@section('content')
@if (session('information'))
<div class="alert alert-success">
    {{ session('information') }}
</div>
@endif
@if (session('pass'))
<div class="alert alert-success">
    {{ session('pass') }}
</div>
@endif
@if (session('privacy'))
<div class="alert alert-success">
    {{ session('privacy') }}
</div>
@endif
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
        $('body').on('click', '.delete_data', function() {
            var id = $(this).attr('data');
            // console.log(id);
            var swal_text = 'حذف ' + $(this).attr('data_name');
            var swal_title = 'هل أنت متأكد من الحذف ؟';

            swal({
                title: swal_title
                , text: swal_text
                , type: "warning"
                , showCancelButton: true
                , confirmButtonClass: "btn-warning"
                , confirmButtonText: "تأكيد"
                , cancelButtonText: "إغلاق"
            }, function() {

                window.location.href = "{{ url('/') }}" + "/admin/products/" + id + "/delete";

            });

        });
    });

</script>
@endsection