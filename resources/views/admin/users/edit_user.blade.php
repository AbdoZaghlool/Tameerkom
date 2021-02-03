@extends('admin.layouts.master')

@section('title')
تعديل عميل
@endsection

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
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
            <a href="/admin/users">العملاء</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>تعديل عميل</span>
        </li>
    </ul>
</div>

<h1 class="page-title"> العملاء
    <small>تعديل عميل</small>
</h1>
@endsection

@section('content')



<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('flash::message')
<div class="row">
    <div class="col-md-12">

        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">حساب الملف الشخصي</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab_1_1" data-toggle="tab">المعلومات الشخصية</a>
                                </li>
                                <li>
                                    <a href="#tab_1_4" data-toggle="tab">اعدادات الخصوصية</a>
                                </li>
                            </ul>
                        </div>
                        <form role="form" action="{{route('users.update',['type'=>0, 'id'=>$user->id])}}" method="post" enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>
                            <input type='hidden' name='type' value='0'>
                            <div class="portlet-body">
                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="tab_1_1">

                                        {{--  <!-- name -->  --}}
                                        <div class="form-group {{$errors->has('name')?'has-error':''}}">
                                            <label class="control-label">الاسم</label>
                                            <input type="text" name="name" placeholder="الاسم" class="form-control"
                                                value="{{$user->name}}" />
                                            @error('name')
                                                <span class="status-error">{{ $errors->first('name') }}</span>
                                            @enderror
                                        </div>

                                        {{--  <!-- phone -->  --}}
                                        <div class="form-group {{$errors->has('phone_number')?'has-error':''}}">
                                            <label class="control-label">رقم الهاتف</label>
                                            <input type="text" name="phone_number" placeholder="رقم الهاتف"
                                                class="form-control" value="{{$user->phone_number}}" />
                                            @error('phone_number')
                                                <span class="status-error">{{ $errors->first('phone_number') }}</span>
                                            @enderror
                                        </div>

                                        {{--  <!-- email -->  --}}
                                        <div class="form-group {{$errors->has('email')?'has-error':''}}">
                                            <label class="control-label">البريد الالكتروني</label>
                                            <input type="email" name="email" placeholder="البريد الالكتروني"
                                                class="form-control" value="{{$user->email}}" />
                                            @error('email')
                                                <span class="status-error">{{ $errors->first('email') }}</span>
                                            @enderror
                                        </div>

                                        {{--  <!-- region -->  --}}
                                        {{--<div class="form-group">
                                            <label class="control-label">المنطقة</label>
                                            {!! Form::select('region_id',
                                            App\Region::pluck('name','id'),
                                            null, ['class' => 'form-control','placeholder'=> 'اختر منطقة']) !!}
                                            </select>
                                            @error('region_id')
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        --}}

                                        {{--  <!-- city -->  --}}
                                        {{--<div class="form-group">
                                            <label class="control-label">المدينة</label>
                                            <select name="city_id" class="form-control">
                                            </select>
                                            @error('city_id')
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>  --}}

                                        {{--  <!-- password -->  --}}
                                        <div class="form-group {{$errors->has('password')?'has-error':''}}">
                                            <label class="control-label">كلمة المرور</label>
                                            <input type="password" name="password" class="form-control" />
                                            @error('password')
                                                <span class="status-error">{{ $errors->first('password') }}</span>
                                            @enderror
                                        </div>

                                        {{--  <!-- pass_confirm -->  --}}
                                        <div class="form-group {{$errors->has('password_confirmation')?'has-error':''}}">
                                            <label class="control-label">إعادة كلمة المرور</label>
                                            <input type="password" name="password_confirmation" class="form-control" />
                                            @error('password_confirmation')
                                                <span class="status-error">{{ $errors->first('password_confirmation') }}</span>
                                            @enderror
                                        </div>

                                        {{--  <!-- image -->  --}}
                                        <div class="form-body">
                                            <div class="form-group {{$errors->has('image')?'has-error':''}} ">
                                                <label class="control-label col-md-3">الصورة الشخصية</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail"
                                                            data-trigger="fileinput"
                                                            style="width: 200px; height: 150px;">
                                                            <img src="{{asset('uploads/users/'.$user->image)}}" alt="">
                                                        </div>
                                                        <div>
                                                            <span class="btn red btn-outline btn-file">
                                                                <span class="fileinput-new"> اختر الصورة </span>
                                                                <span class="fileinput-exists"> تغيير </span>
                                                                <input type="file" name="image"> </span>
                                                            <a href="javascript:;" class="btn red fileinput-exists"
                                                                data-dismiss="fileinput"> إزالة </a>
                                                        </div>
                                                    </div>
                                                    @error('image')
                                                        <span class="status-error">{{ $errors->first('image') }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{--  <!-- map -->  --}}
                                        <div class="body-site">
                                            <div class="d-flex">
                                                <div class="col-m-9">
                                                    <div class="content sections">
                                                        <div class="form-group {{$errors->has('latitude') ? 'has-error' : ($errors->has('longitude') ? 'has-error' : '')}}">
                                                            <h4 style="text-align: right"> حدد الموقع علي الخريطة </h4>
                                                            <input class="form-control" type="text" id="lat" name="latitude" readonly value="{{$user->latitude}}"/>
                                                            @error('latitude')
                                                                <span class="status-error">{{ $errors->first('latitude') }}</span>
                                                            @enderror
                                                            <input class="form-control" type="text" id="lng" name="longitude" readonly value="{{$user->longitude}}"/>
                                                            @error('longitude')
                                                                <span class="status-error">{{ $errors->first('longitude') }}</span>
                                                            @enderror
                                                        </div>
                                                        <div id="map"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- END PERSONAL INFO TAB -->
                                    <!-- PRIVACY SETTINGS TAB -->
                                    <div class="tab-pane" id="tab_1_4">
                                        <table class="table table-light table-hover">
                                            <tr>
                                                <td> تفعيل العميل</td>
                                                <td>
                                                    <div class="mt-radio-inline">
                                                        <label class="mt-radio">
                                                            <input type="radio" name="active" value="1"
                                                                {{ $user->active == "1" ? 'checked' : '' }} /> نعم
                                                            <span></span>
                                                        </label>
                                                        <label class="mt-radio">
                                                            <input type="radio" name="active" value="0"
                                                                {{ $user->active == "0" ? 'checked' : '' }} /> لا
                                                            <span></span>
                                                        </label>
                                                        @if ($errors->has('active'))
                                                        <span class="help-block">
                                                            <strong
                                                                style="color: red;">{{ $errors->first('active') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- END PRIVACY SETTINGS TAB -->
                                </div>
                            </div>
                            <div class="margiv-top-10">
                                <div class="form-actions">
                                    <button type="submit" class="btn green" value="حفظ"
                                        onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">حفظ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ URL::asset('admin/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/components-select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/bootstrap-fileinput.js') }}"></script>

<script>

    $(document).ready(function () {
        $('select[name="region_id"]').on('change' , function () {
            $('select[name="city_id"]').empty();
            var model = 'City';
            var col = 'region_id';
            var id = $(this).val();
            // alert(id);
            if(id)
            {
                console.log(id);
                $.ajax({
                    url: '/get_sub_cat/'+model+'/'+col+'/' +id,
                    type: 'GET',
                    datatype: 'json',
                    success: function (data) {
                        // console.log(data);
                        $('select[name="city_id"]').empty();
                        $.each(data , function (key , value) {
                            $('select[name="city_id"]').append('<option value="'+value+'">' +key+ '</option>');
                        });
                    }
                });
            }else{
                $('select[name="city_id"]').empty();
            }
        });
    });
</script>

<script>
    function getLocation()
    {
    if (navigator.geolocation)
    {
    navigator.geolocation.getCurrentPosition(showPosition);
    }
    else{x.innerHTML="Geolocation is not supported by this browser.";}
    }

    function showPosition(position)
    {
    lat= position.coords.latitude;
    lon= position.coords.longitude;

    document.getElementById('lat').value = lat; //latitude
    document.getElementById('lng').value = lon; //longitude
    latlon=new google.maps.LatLng(lat, lon)
    mapholder=document.getElementById('mapholder')
    //mapholder.style.height='250px';
    //mapholder.style.width='100%';

    var myOptions={
    center:latlon,zoom:14,
    mapTypeId:google.maps.MapTypeId.ROADMAP,
    mapTypeControl:false,
    navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
    };
    var map = new google.maps.Map(document.getElementById("map"),myOptions);
    var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
    }

</script>
<script type="text/javascript">
    var map;

    function initMap() {
    var latitude = {{$user->latitude ?? 24.482582269844997}}; // YOUR LATITUDE VALUE
    var longitude = {{$user->longitude ?? 39.567722188865126}}; // YOUR LONGITUDE VALUE
    var myLatLng = {lat: latitude, lng: longitude};
    map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 5,
    gestureHandling: 'true',
    zoomControl: false// disable the default map zoom on double click
    });

    var marker = new google.maps.Marker({
    position: myLatLng,
    map: map,
    //title: 'Hello World'

    // setting latitude & longitude as title of the marker
    // title is shown when you hover over the marker
    title: latitude + ', ' + longitude
    });

    //Listen for any clicks on the map.
    google.maps.event.addListener(map, 'click', function(event) {
    //Get the location that the user clicked.
    var clickedLocation = event.latLng;
    //If the marker hasn't been added.
    if(marker === false){
    //Create the marker.
    marker = new google.maps.Marker({
    position: clickedLocation,
    map: map,
    draggable: true //make it draggable
    });
    //Listen for drag events!
    google.maps.event.addListener(marker, 'dragend', function(event){
    markerLocation();
    });
    } else{
    //Marker has already been added, so just change its location.
    marker.setPosition(clickedLocation);
    }
    //Get the marker's location.
    markerLocation();
    });

    function markerLocation(){
    //Get location.
    var currentLocation = marker.getPosition();
    //Add lat and lng values to a field that we can save.
    document.getElementById('lat').value = currentLocation.lat(); //latitude
    document.getElementById('lng').value = currentLocation.lng(); //longitude
    }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUMq5htfgLMNYvN4cuHvfGmhe8AwBeKU&callback=initMap" async
    defer></script>

@endsection
