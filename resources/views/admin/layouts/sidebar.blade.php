<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item start active open">
                <a href="/admin/home" class="nav-link nav-toggle">
                    <i class="icon-home" style="color: aqua;"></i>
                    <span class="title">الرئيسية</span>
                    <span class="selected"></span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">القائمة الجانبية</h3>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admins') !== false ? 'active' : '' }}">
                <a href="{{ url('/admin/admins') }}" class="nav-link ">
                    <i class="fa fa-user" style="color: aqua;"></i>
                    <span class="title">المشرفين</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Admin::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/roles') !== false ? 'active' : '' }}">
                <a href="{{ route('roles.index')}}" class="nav-link ">
                    <i class="icon-layers" style="color: aqua;"></i>
                    <span class="title"> صلاحيات المديرين</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Role::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), '/user') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users" style="color: aqua;"></i>
                    <span class="title">المستخدمين</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/users') }}" class="nav-link ">
                            <span class="title">العملاء</span>
                            <span class="badge badge-success">{!! count(\App\User::where('type','0')->get()) !!}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin/users/providers') }}" class="nav-link disabled">
                            <span class="title"> المزودين</span>
                            <span class="badge badge-success">{!! count(\App\User::where('type','1')->get()) !!}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), '/regions') !== false ? 'active' : '' }}">
                <a href="{{  route('regions.index')}}" class="nav-link ">
                    <i class="fa fa-globe" style="color: aqua;"></i>
                    <span class="title"> المناطق</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Region::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), '/cities') !== false ? 'active' : '' }}">
                <a href="{{ route('cities.index')}}" class="nav-link ">
                    <i class="fa fa-building-o" style="color: aqua;"></i>
                    <span class="title"> المدن</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\City::get()) !!}</span>
                </a>
            </li>


            <li class="nav-item {{ strpos(URL::current(), 'admin/main-categories') !== false ? 'active' : '' }}">
                <a href="{{ route('main-categories.index')}}" class="nav-link ">
                    <i class="fa fa-sitemap" style="color: aqua;"></i>
                    <span class="title">الاقسام</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Category::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/products') !== false ? 'active' : '' }}">
                <a href="{{ route('products.index')}}" class="nav-link ">
                    <i class="fa fa-sitemap" style="color: aqua;"></i>
                    <span class="title">المنتجات</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Category::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/properties') !== false ? 'active' : '' }}">
                <a href="{{ route('properties.index')}}" class="nav-link ">
                    <i class="fa fa-sitemap" style="color: aqua;"></i>
                    <span class="title">الخصائص</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Property::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/orders') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-bag" style="color: aqua;"></i>
                    <span class="title">الطلبات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}" class="nav-link ">
                            <span class="title">الطلبات</span>
                            <span class="badge badge-success">{!! count(\App\Order::whereIn('status',['0','1'])->get()) !!}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.cancel-requests') }}" class="nav-link disabled">
                            <span class="title"> طلبات الالغاء</span>
                            <span class="badge badge-success">{!! count(\App\Order::where('status','3')->get()) !!}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.canceled') }}" class="nav-link disabled">
                            <span class="title"> الطلبات الملغية</span>
                            <span class="badge badge-success">{!! count(\App\Order::where('status','2')->get()) !!}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/commissions') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-money" style="color: aqua;"></i>
                    <span class="title">العمولات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('commissions.index') }}" class="nav-link ">
                            <span class="title">العمولات المستحقة</span>
                            <span class="badge badge-success">{!! count(\App\Order::where('status','1')->get()) !!}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('commissions.paid') }}" class="nav-link disabled">
                            <span class="title"> العمولات المدفوعة</span>
                            <span class="badge badge-success">{!! count(\App\Order::where('status','1')->get()) !!}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'complaints') !== false ? 'active' : '' }}">
                <a href="/admin/complaints" class="nav-link ">
                    <i class="fa fa-flag" style="color: aqua;"></i>
                    <span class="title"> الشكاوى </span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Complaint::get()) !!}</span>
                </a>
            </li>





            <li class="nav-item {{ strpos(URL::current(), 'admin/notifications') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-bell" style="color: aqua;"></i>
                    <span class="title">الاشعارات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item  ">
                        <a href="/admin/notifications" class="nav-link ">
                            <span class="title">اشعار لجميع المستخدمين</span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a href="/admin/notifications/user" class="nav-link ">
                            <span class="title">اشعار لمستخدمين محددين</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/splashs') !== false ? 'active' : '' }}">
                <a href="/admin/splashs" class="nav-link ">
                    <i class="fa fa-file-image-o" style="color: aqua;"></i>
                    <span class="title">البنرات</span>
                    <span class="pull-right-container"></span>
                    <span class="badge badge-success">{!! count(\App\Slider::get()) !!}</span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/setting') !== false ? 'active' : '' }}">
                <a href="/admin/setting" class="nav-link ">
                    <i class="icon-settings" style="color: aqua;"></i>
                    <span class="title">الاعدادات العامة</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>

            <li class="nav-item {{ strpos(URL::current(), 'admin/pages') !== false ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-layers" style="color: aqua;"></i>
                    <span class="title">الصفحات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item  ">
                        <a href="/admin/pages/about" class="nav-link ">
                            <span class="title">من نحن</span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a href="/admin/pages/terms" class="nav-link ">
                            <span class="title">الشروط والاحكام</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
