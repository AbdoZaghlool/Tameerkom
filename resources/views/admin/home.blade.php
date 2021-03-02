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
        <a class="dashboard-stat dashboard-stat-light red" href="{{route('providers.index')}}">
            <div class="visual">
                <i class="fa fa-cutlery" aria-hidden="true"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>{{App\Product::count()}}</span>
                </div>
                <div class="desc"> عدد المنتجات </div>
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
                    <span>{{App\Order::where('status','1')->where('payment_status',0)->count()}}</span>
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
                    <span>{{App\Order::where('status','1')->where('payment_status',1)->count()}}</span>
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

{{-- <div class="row">
    <div class="col-md-6">
        <div class="card-body" style="display: block;">
            <div class="chartjs-size-monitor">
                <div class="chartjs-size-monitor-expand">
                    <div class="">
                    </div>
                </div>
                <div class="chartjs-size-monitor-shrink">
                    <div class="">
                    </div>
                </div>
            </div>
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-body" style="display: block;">
            <div class="chartjs-size-monitor">
                <div class="chartjs-size-monitor-expand">
                    <div class="">
                    </div>
                </div>
                <div class="chartjs-size-monitor-shrink">
                    <div class="">
                    </div>
                </div>
            </div>
            <canvas id="myChart_2"></canvas>
        </div>
    </div>

</div>  --}}
<br>

{{-- <div class="row">
    <div id="canvas-holder" style="width:70%; margin:auto;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
		<canvas id="canvas" style="display: block; width: 540px; height: 270px;" width="540" height="270" class="chartjs-render-monitor"></canvas>
	</div>
</div>  --}}

@endsection

@section('scripts')
{{-- <script>
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'pie'
        , data: {
            labels: ['الطلبات الجديدة', 'الطلبات النشطة', 'الطلبات المكتملة', 'الطلبات الملغية']
            , datasets: [{
                label: 'احصائيات الطلبات'
                , data: [{
                        {
                            App\ Order::where('status', '0') - > get() - > count()
                        }
                    }
                    , {
                        {
                            App\ Order::where('status', '2') - > get() - > count()
                        }
                    }
                    , {
                        {
                            App\ Order::where('status', '3') - > get() - > count()
                        }
                    }
                    , {
                        {
                            App\ Order::where('status', '4') - > get() - > count()
                        }
                    }
                , ]
                , backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                    , 'rgba(54, 162, 235, 0.2)'
                    , 'rgba(255, 206, 86, 0.2)'
                    , 'rgba(75, 192, 192, 0.2)'
                , ]
                , borderColor: [
                    'rgba(255, 99, 132, 1)'
                    , 'rgba(54, 162, 235, 1)'
                    , 'rgba(255, 206, 86, 1)'
                    , 'rgba(75, 192, 192, 1)'
                , ]
                , borderWidth: 1
            }]
        }
        , options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

</script>

<script>
    var ctx = document.getElementById('myChart_2');
    var myChart = new Chart(ctx, {
        type: 'pie'
        , data: {
            labels: ['الطلبات الحالية', 'الطلبات المجدولة']
            , datasets: [{
                label: 'احصائيات الطلبات'
                , data: [{
                        {
                            App\ Order::where('type_id', 1) - > get() - > count()
                        }
                    }
                    , {
                        {
                            App\ Order::where('type_id', 2) - > get() - > count()
                        }
                    }
                , ]
                , backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                    , 'rgba(54, 162, 235, 0.2)',

                ]
                , borderColor: [
                    'rgba(255, 99, 132, 1)'
                    , 'rgba(54, 162, 235, 1)',

                ]
                , borderWidth: 1
            }]
        }
        , options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

</script>  --}}

{{-- <script>

    $.ajax({
        url: '/get-orders-status',
        processData: false,
        contentType: false,
        type: 'get',
        success: function(data){
            if(data.length > 0){
                var families = data['families'];
                var count = data['count'];
                

                console.log(data);

                var ctx = document.getElementById('canvas').getContext('2d');
                var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: families,
                    datasets: [{
                        label: '# of Votes',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });





            }// end length condition 
        }// end success
        }); // end ajax call








</script>  --}}

@endsection
