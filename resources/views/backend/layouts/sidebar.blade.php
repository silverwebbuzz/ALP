@php
if(Auth::user()->role_id == 1){
    $color = '#A5A6F6';
}else if(Auth::user()->role_id==2){
    $color = '#f7bfbf';
}else if(Auth::user()->role_id==3){
    $color = '#d8dc41';
}else if(Auth::user()->role_id == 7){
    $color = '#BDE5E1';
}else if(Auth::user()->role_id == 8){
    $color = '#fed08d';
}else if(Auth::user()->role_id == 9){
    $color = '#eab676';
}else{
    $color = '#a8e4b0';
}

$permissions = [];
$user_id = auth()->user()->id;
if($user_id){
    $module_permission = App\Helpers\Helper::getPermissions($user_id);
    if($module_permission && !empty($module_permission)){
        $permissions = $module_permission;
    }
}else{
    $permissions = [];
}
$RoleBasedColor = \App\Helpers\Helper::getRoleBasedColor();
@endphp
<style>
    .sm-deskbord-main-sec #sidebar.inactive ul li.active{
        background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?>
    }
    .sm-deskbord-main-sec #sidebar.active ul li.active {
        background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?>
    }
    .sm-deskbord-main-sec #sidebar.active ul.components li a , #sidebar.inactive .sidebar_icon_main{
        background-color: <?php echo $RoleBasedColor['headerColor'];?> !important;
    }
</style>

<!-- Super Admin Sidebar Menus -->
@if(Auth::user()->role_id == 1)
    @include('backend.layouts.sidebar.admin_sidebar') 
@endif
<!-- End Admin Sidebar Menus -->

<!-- Start Teacher Sidebar Menus -->
@if(Auth::user()->role_id == 2)
    @include('backend.layouts.sidebar.teacher_sidebar')
@endif
<!-- End Teacher Sidebar Menus -->


<!-- Start Student Sidebar Menus -->
@if(Auth::user()->role_id == 3)
    @include('backend.layouts.sidebar.student_sidebar')
@endif
<!-- End  Student Sidebar Menus -->


@if(Auth::user()->role_id == 4)
<!-- Menu For Parent Start -->
<nav id="sidebar" class="@if(!empty(Session::get('sidebar'))){{Session::get('sidebar')}}@endif" style="background-color:{{$color}};">
    <h1>
        <a href="javascript:void(0);" class="logo">
            @if(Auth::user()->profile_photo!="")
                <img src="{{ asset(Auth::user()->profile_photo) }}" alt="logo" class="logo-icon">
            @else
                <img src="{{ asset('images/profile_image.jpeg') }}" alt="logo" class="logo-icon">
            @endif
        </a>
    </h1>
    <ul class="list-unstyled components mb-5">
        <li class="{{ (request()->is('parent/dashboard')) ? 'active': '' }}">
            <a href="{{ route('parent.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>

        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('profile') || request()->is('change-password')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('profile') || request()->is('change-password')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('profile_management_read', $permissions))
                    <li class="nav-item {{ (request()->is('profile')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('profile.index')}}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.sidebar.profile')}}</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array('change_password_update', $permissions))
                    <li class="nav-item {{ (request()->is('change-password')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('change-password')}}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.change_password')}}</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            @endif
        </li>

        <!-- @if (in_array('profile_management_read', $permissions))
        <li class="{{ (request()->is('profile')) ? 'active': '' }}">
            <a href="{{route('profile.index')}}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.profile')}}</span>
            </a>
        </li>
        @endif -->

        <li class="{{ (request()->is('parent/list')) ? 'active': '' }}">
            <a href="{{ route('parent.list') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.child_list')}}</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" id="logout">
                <span class="fa fa-sign-out"></span>
                <span class="text">{{__('languages.sidebar.logout')}}</span>
            </a>
        </li>
    </ul>
</nav>
<!-- Menu For Parent End -->
@endif


@if(Auth::user()->role_id == 5)
    @include('backend.layouts.sidebar.school_sidebar')
@endif

<!-- Menu For External Resource Start -->
@if(Auth::user()->role_id == 6)
<nav id="sidebar" class="@if(!empty(Session::get('sidebar'))){{Session::get('sidebar')}}@endif" style="background-color:{{$color}};">
    <h1>
        <a href="javascript:void(0);" class="logo">
            @if(Auth::user()->profile_photo!="")
                <img src="{{ asset(Auth::user()->profile_photo) }}" alt="logo" class="logo-icon">
            @else
                <img src="{{ asset('images/profile_image.jpeg') }}" alt="logo" class="logo-icon">
            @endif
        </a>
    </h1>


    <ul class="list-unstyled components mb-5">
        <li class="{{ (request()->is('external_resource/dashboard')) ? 'active': '' }}">
            <a href="{{ route('external_resource.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('change-password')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('change-password')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="nav-item {{ (request()->is('change-password')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('change-password')}}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.change_password')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        @if (in_array('question_bank_read', $permissions))
        <li>
            <a href="{{ route('questions.index') }}">
                <span class="fa fa-laptop"></span>
                <span class="text"> {{__('languages.sidebar.question_bank')}} </span>
            </a>
        </li>
        @endif
        @if(in_array('upload_documents_read',$permissions))
        <li class="{{ (request()->is('upload-documents')) ? 'active': ''}}">
            <a href="{{ route('upload-documents.index') }}">
                <span class="fa fa-laptop"></span>
                <span class="text"> {{__('languages.sidebar.upload_documents')}} </span>
            </a>
        </li>
        @endif
        <li>
            <a href="javascript:void(0);" id="logout">
                <span class="fa fa-sign-out"></span>
                <span class="text">{{__('languages.sidebar.logout')}}</span>
            </a>
        </li>
    </ul>
</nav>
@endif
<!-- Menu For External Resource End -->


<!-- Start Principal Sidebar Menus -->
@if(Auth::user()->role_id == 7)
    @include('backend.layouts.sidebar.principal_sidebar')
@endif
<!-- End Principal Sidebar Menus -->

<!-- Start Panel Head Sidebar Menus -->
@if(Auth::user()->role_id == 8)
    @include('backend.layouts.sidebar.panel_head_sidebar')
@endif
<!-- End Panel Head Sidebar Menus -->


<!-- Start Co-Ordinator Sidebar Menus -->
@if(Auth::user()->role_id == 9)
    @include('backend.layouts.sidebar.co_ordinator_sidebar')
@endif
<!-- End Co-Ordinator Sidebar Menus -->

