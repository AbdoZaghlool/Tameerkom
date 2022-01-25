@extends('admin.layouts.master')

@section('title')
لوحة التحكم
@endsection

@section('styles')
#myChart
{
width:50%; !important;
height:250px; !important;
float: left;
}

#myChart_2
{
width: 50%;
float: right;
}
@endsection

@section('content')

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin/home"> لوحة التحكم</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>الإحصائيات</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> الإحصائيات
    <small>عرض الإحصائيات</small>
</h1>

<div class="row">

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red" href="{{route('admins.index')}}">
            <div class="visual">
                <i class="fa fa-user-circle-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{$admins}}</span>
                </div>
                <div class="desc"> المشرفين </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue" href="{{route('users.index')}}">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\User::whereType('0')->count()}}</span>
                </div>
                <div class="desc"> المستخدمين </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light yellow" href="{{route('providers.index')}}">
            <div class="visual">
                <i class="fa fa-coffee"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\User::whereType('1')->count()}}</span>
                </div>
                <div class="desc"> المزودين </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="{{route('regions.index')}}">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Region::count()}}</span>
                </div>
                <div class="desc"> المناطق </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue" href="{{route('cities.index')}}">
            <div class="visual">
                <i class="fa fa-building-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\City::count()}}</span>
                </div>
                <div class="desc"> المدن </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light yellow" href="{{route('main-categories.index')}}">
            <div class="visual">
                <i class="fa fa-sitemap"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Category::count()}}</span>
                </div>
                <div class="desc"> الاقسام </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="{{route('properties.index')}}">
            <div class="visual">
                <i class="fa fa-sitemap"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Property::count()}}</span>
                </div>
                <div class="desc"> خصائص الاقسام </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red" href="{{route('products.index')}}">
            <div class="visual">
                <i class="fa fa-cutlery" aria-hidden="true"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Product::count()}}</span>
                </div>
                <div class="desc">المنتجات</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light yellow" href="{{route('orders.index')}}">
            <div class="visual">
                <i class="fa fa-shopping-basket"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Order::where('status','0')->count()}}</span>
                </div>
                <div class="desc"> الطلبات النشطة</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue" href="{{route('orders.index')}}">
            <div class="visual">
                <i class="fa fa-shopping-basket"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Order::where('status','1')->count()}}</span>
                </div>
                <div class="desc"> الطلبات المكتملة</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red" href="{{route('orders.canceled')}}">
            <div class="visual">
                <i class="fa fa-shopping-basket"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Order::where('status','2')->count()}}</span>
                </div>
                <div class="desc"> الطلبات الملغية</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="{{route('commissions.index')}}">
            <div class="visual">
                <i class="fa fa-money"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Order::where('status','1')->count()}}</span>
                </div>
                <div class="desc"> العمولات المستحقة </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="{{route('commissions.paid')}}">
            <div class="visual">
                <i class="fa fa-money"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Order::where('status','1')->count()}}</span>
                </div>
                <div class="desc"> العمولات المدفوعة </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red" href="{{route('complaints.index')}}">
            <div class="visual">
                <i class="icon-arrow-down"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Complaint::count()}}</span>
                </div>
                <div class="desc"> الشكاوى </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue" href="{{route('splashs.index')}}">
            <div class="visual">
                <i class="fa fa-image"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Slider::get()->count()}}</span>
                </div>
                <div class="desc">البنرات الاعلانية</div>
            </div>
        </a>
    </div>



</div>



@endsection
