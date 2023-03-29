{{-- @php
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
    }else{
        $color = '#a8e4b0';
    }
@endphp --}}

@php
    $RoleBasedColor = \App\Helpers\Helper::getRoleBasedColor();
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
@endphp
<style>
    .sm-deskbord-main-sec #sidebar.inactive ul li.active{ 
        /* background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?> */
        background-color: <?php echo $RoleBasedColor['active_color']; ?>
    }
    .sm-deskbord-main-sec #sidebar.active ul li.active {
        /* background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?> */
        background-color: <?php echo $RoleBasedColor['active_color']; ?>
    }
</style>

<!-- Super Admin Sidebar Menus -->
@if(Auth::user()->role_id == 1)
    <nav id="sidebar" class="@if(!empty(Session::get('sidebar'))){{Session::get('sidebar')}}@endif" style="background-color:{{$RoleBasedColor['background_color']}};">

    <h1 class="d-flex sidebar_top_thumb_main">
        <a href="javascript:void(0);" class="logo">
        @if(Auth::user()->profile_photo!="")
            <img src="{{ asset(Auth::user()->profile_photo) }}" alt="logo" class="logo-icon">
        @else
            <img src="{{ asset('images/profile_image.jpeg') }}" alt="logo" class="logo-icon">
        @endif
        </a>
        @include('backend.layouts.sidebar.user_info_sidebar')
    </h1>
    <ul class="list-unstyled components mb-5">
        <li class="{{ (request()->is('super-admin/dashboard')) ? 'active' : ''}}">
            <a href="{{ route('superadmin.dashboard') }}">
                {{-- <span class="fa fa-home" title="{{__('languages.common_sidebar.dashboard')}}"></span> --}}
                <div class="sidebar_icon_main">
                    <img class="sidebar_icon" src="{{ asset('images/sidebar_icons/home.png') }}"  title="{{__('languages.common_sidebar.dashboard')}}" alt="{{__('languages.common_sidebar.dashboard')}}">
                </div>
                <span class="text">{{__('languages.common_sidebar.dashboard')}}</span>
            </a>
        </li>
        {{-- Profile --}}
        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
                <a class="nav-link text-truncate {{ (
                        request()->is('profile') || 
                        request()->is('change-password')
                    ) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.my_account')}}" alt="{{__('languages.my_account')}}">
                    </div>
                    <span class="text">{{__('languages.my_account')}}</span>
                </a>
                <div class="collapse {{ (
                        request()->is('profile') || 
                        request()->is('change-password')
                    ) ? 'show': '' }}" id="myaccount" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        {{-- Profile --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('profile.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.profile')}}" alt="{{__('languages.common_sidebar.profile')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.profile')}}</span>
                            </a>
                        </li>
                        @if(in_array('change_password_update', $permissions))
                            <li class="nav-item {{ (request()->is('change-password')) ? 'active': '' }}">
                                <a class="nav-link" href="{{route('change-password')}}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/password.png') }}"  title="{{__('languages.common_sidebar.change_password')}}" alt="{{__('languages.common_sidebar.change_password')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.change_password')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </li>

        {{-- Syllabus Management --}}
        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
                <a class="nav-link text-truncate {{ (
                        request()->is('strands') ||
                        request()->is('strands/*') ||
                        request()->is('learning_units') ||
                        request()->is('learning_units/*') ||
                        request()->is('learning-objective') || 
                        request()->is('learning-objective/*') || 
                        request()->is('intelligent-tutor') ||
                        request()->is('intelligent-tutor/*') || 
                        request()->is('questions') || 
                        request()->is('questions/*') ||
                        request()->is('question-wizard') ||
                        request()->is('question-wizard/*') ||
                        request()->is('super-admin/generate-questions') ||
                        request()->is('exams/attempt/students/*') ||
                        request()->is('update/question/*') || 
                        request()->is('nodes') ||
                        request()->is('nodes/*')
                    ) ? 'collapsed': '' }}" href="#syllabus_management" data-toggle="collapse" data-target="#syllabus_management">
                    <!-- <span class="fa"><i class="fa fa-book" title="{{__('languages.admin_sidebar.curriculum_management')}}"></i></span> -->
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/strand.png') }}"  title="{{__('languages.admin_sidebar.curriculum_management')}}" alt="{{__('languages.admin_sidebar.curriculum_management')}}">
                    </div>
                    <span class="text">{{__('languages.admin_sidebar.curriculum_management')}}</span>
                </a>
                <div class="collapse {{ (
                        request()->is('strands') || 
                        request()->is('strands/*') || 
                        request()->is('learning_units') ||
                        request()->is('learning_units/*') ||
                        request()->is('learning-objective') || 
                        request()->is('learning-objective/*') ||
                        request()->is('intelligent-tutor') || 
                        request()->is('intelligent-tutor/*') || 
                        request()->is('questions') ||
                        request()->is('questions/*') || 
                        request()->is('question-wizard') || 
                        request()->is('question-wizard/*') || 
                        request()->is('nodes') ||
                        request()->is('nodes/*') ||
                        request()->is('super-admin/generate-questions') || 
                        request()->is('exams/attempt/students/*') || 
                        request()->is('update/question/codes')
                    ) ? 'show': '' }}" id="syllabus_management" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        @if(in_array('strands_management_read',$permissions))
                            <li class="nav-item {{ (
                                    request()->is('strands') || 
                                    request()->is('strands/create') ||
                                    request()->is('strands/*/edit')
                                ) ? 'active': ''  }}">
                                <a href="{{ route('strands.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/strand.png') }}"  title="{{__('languages.manage')}} {{__('languages.strand')}}" alt="{{__('languages.manage')}} {{__('languages.strand')}}">
                                    </div>
                                    <span class="text"> {{__('languages.manage')}} {{__('languages.strand')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('learning_units_management_read', $permissions))
                            <li class="nav-item {{ (request()->is('learning_units')) ? 'active' : '' }}">
                                <a href="{{route('learning_units.index')}}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.manage')}} {{__('languages.learning_unit')}}" alt="{{__('languages.manage')}} {{__('languages.learning_unit')}}">
                                    </div>
                                    <span class="text">{{__('languages.manage')}} {{__('languages.learning_unit')}}</span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('learning_objectives_management_read',$permissions))
                            <li class="nav-item {{ (
                                    request()->is('learning-objective') ||
                                    request()->is('learning-objective/create') ||
                                    request()->is('learning-objective/*/edit')
                                ) ? 'active': ''  }}">
                                <a href="{{ route('learning-objective.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning_objective.png') }}"  title="{{__('languages.manage')}} {{__('languages.learning_objective')}}" alt="{{__('languages.manage')}} {{__('languages.learning_objective')}}">
                                    </div>
                                    <span class="text"> {{__('languages.manage')}} {{__('languages.learning_objective')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (
                                    request()->is('intelligent-tutor') || 
                                    request()->is('intelligent-tutor/*')
                                ) ? 'active': ''}}">
                                <a href="{{ route('intelligent-tutor.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.admin_sidebar.intelligent_tutor')}}" alt="{{__('languages.admin_sidebar.intelligent_tutor')}}">
                                    </div>
                                    <span class="text">{{__('languages.admin_sidebar.intelligent_tutor')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('knowledge_tree_read', $permissions))
                            @if (in_array('node_management_read', $permissions))
                                <li class="nav-item {{ (
                                        request()->is('nodes') || 
                                        request()->is('nodes/create') ||
                                        request()->is('nodes/*/edit') || 
                                        request()->is('nodes/*')
                                    ) ? 'active': ''  }}">
                                    <a class="nav-link" href="{{ route('nodes.index') }}">
                                        <div class="sidebar_icon_main">
                                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/knowledge_tree.png') }}"  title="{{__('languages.admin_sidebar.knowledge_tree')}}" alt="{{__('languages.admin_sidebar.knowledge_tree')}}">
                                        </div>
                                        <span class="text">{{__('languages.admin_sidebar.knowledge_tree')}}</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if (in_array('question_bank_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('questions') || 
                                    request()->is('questions/create') ||
                                    request()->is('questions/*/edit') || 
                                    request()->is('update/question/codes')
                                ) ? 'active': ''  }}">
                                <a href="{{ route('questions.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_bank.png') }}"  title="{{__('languages.admin_sidebar.question_bank')}}" alt="{{__('languages.admin_sidebar.question_bank')}}">
                                    </div>
                                    <span class="text"> {{__('languages.admin_sidebar.question_bank')}} </span>
                                </a>
                            </li>
                        @endif
                        
                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('question-wizard') || 
                                    request()->is('super-admin/generate-questions') || 
                                    request()->is('question-wizard/preview/*') || 
                                    request()->is('exams/attempt/students/*') || 
                                    request()->is('super-admin/generate-questions-edit/*') ||
                                    request()->is('question-wizard/proof-reading-question')
                                ) ? 'active': ''  }}">
                                <a href="{{ route('question-wizard') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.exercise_and_test_wizard')}}" alt="{{__('languages.common_sidebar.exercise_and_test_wizard')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.exercise_and_test_wizard')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </li>

        {{-- School User Management  --}}
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (
                    request()->is('schoolmanagement') || 
                    request()->is('schoolmanagement/*')
                ) ? 'collapsed': '' }}" href="#school_management" data-toggle="collapse" data-target="#school_management">
                <div class="sidebar_icon_main">
                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.school_management')}}" alt="{{__('languages.admin_sidebar.school_management')}}">
                </div>
                <span class="text">{{__('languages.admin_sidebar.school_management')}}</span>
            </a>
            <div class="collapse {{ (
                    request()->is('schoolmanagement') || 
                    request()->is('schoolmanagement/*') || 
                    request()->is('school-users') || 
                    request()->is('school-users/*')
                ) ? 'show': '' }}" id="school_management" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('school_management_read', $permissions))
                        <li class="nav-item {{(
                                request()->is('schoolmanagement') || 
                                request()->is('schoolmanagement/*')
                            ) ? 'active' : ''}}">
                            <a class="nav-link " href="{{ route('schoolmanagement.index') }}">
                                <!-- <span class="fa fa-user" title="{{__('languages.admin_sidebar.schools')}}"></span> -->
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.admin_sidebar.schools')}}" alt="{{__('languages.admin_sidebar.schools')}}">
                                </div>
                                <span class="text">{{__('languages.admin_sidebar.schools')}}</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item {{(
                            request()->is('school-users') || 
                            request()->is('school-users/*')
                        ) ? 'active' : ''}}">
                        <a class="nav-link" href="{{route('school-users.index')}}">
                            <!-- <span class="fa fa-user" title="{{__('languages.admin_sidebar.school_user_management')}}"></span> -->
                            <div class="sidebar_icon_main">
                                <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.admin_sidebar.school_user_management')}}" alt="{{__('languages.admin_sidebar.school_user_management')}}">
                            </div>
                            <span class="text">{{__('languages.admin_sidebar.school_user_management')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Setting --}}
        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
                <a class="nav-link text-truncate {{ (
                        request()->is('ai-calibration') ||
                        request()->is('ai-calibration/list') ||
                        request()->is('ai-calibration/create') || 
                        request()->is('ai-calibration/report/*') || 
                        request()->is('ai-calibration/question-log/*') || 
                        request()->is('modulesmanagement') || 
                        request()->is('modulesmanagement/*') ||
                        request()->is('rolesmanagement') || 
                        request()->is('rolesmanagement/*') || 
                        request()->is('settings') || 
                        request()->is('pre-configure-difficulty') ||
                        request()->is('pre-configure-difficulty/*') || 
                        request()->is('global-configuration')
                    ) ? 'collapsed': '' }}" href="#setting" data-toggle="collapse" data-target="#setting">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.setting')}}" alt="{{__('languages.admin_sidebar.setting')}}">
                    </div>
                    <span class="text">{{__('languages.admin_sidebar.setting')}}</span>
                </a>
                <div class="collapse {{ (
                    request()->is('ai-calibration') ||
                    request()->is('ai-calibration/list') ||
                    request()->is('ai-calibration/create') || 
                    request()->is('ai-calibration/report/*') || 
                    request()->is('ai-calibration/question-log/*') || 
                    request()->is('modulesmanagement') || 
                    request()->is('modulesmanagement/*') || 
                    request()->is('rolesmanagement') || 
                    request()->is('rolesmanagement/*') ||
                    request()->is('settings') || 
                    request()->is('pre-configure-difficulty') || 
                    request()->is('pre-configure-difficulty/*') || 
                    request()->is('global-configuration')
                ) ? 'show': '' }}" id="setting" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        @if(in_array('ai-calibration_read',$permissions))
                            <li class="nav-item {{ (
                                        request()->is('ai-calibration') || 
                                        request()->is('ai-calibration/list') || 
                                        request()->is('ai-calibration/create') || 
                                        request()->is('ai-calibration/report/*') ||
                                        request()->is('ai-calibration/question-log/*')
                                    ) ? 'active': ''}}">
                                <a href="{{ route('ai-calibration.list') }}">
                                    <!-- <span class="fa fa-file" title="{{__('languages.admin_sidebar.ai_calibration')}}"></span> -->
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.ai_calibration')}}" alt="{{__('languages.admin_sidebar.ai_calibration')}}">
                                    </div>
                                    <span class="text">{{__('languages.admin_sidebar.ai_calibration')}}</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item {{ (
                                    request()->is('modulesmanagement') ||
                                    request()->is('modulesmanagement/*')
                                ) ? 'active': '' }}">
                            <a class="nav-link" href="{{ route('modulesmanagement.index') }}">
                                <!-- <span class="fa fa-sliders-h">
                                    <i class="fa fa-tags" title="{{__('languages.admin_sidebar.module_management')}}" aria-hidden="true"></i>
                                </span> -->
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.module_management')}}" alt="{{__('languages.admin_sidebar.module_management')}}">
                                </div>
                                <span class="text">{{__('languages.admin_sidebar.module_management')}}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ (
                                request()->is('rolesmanagement') || 
                                request()->is('rolesmanagement/create') ||
                                request()->is('rolesmanagement/*/edit')
                            ) ? 'active': ''  }}">
                            <a class="nav-link" href="{{route('rolesmanagement.index')}}">
                                <!-- <span class="fa fa-sliders-h"><i class="fa fa-unlock-alt" title="{{__('languages.admin_sidebar.roles_and_permissions')}}" aria-hidden="true"></i></span> -->
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.roles_and_permissions')}}" alt="{{__('languages.admin_sidebar.roles_and_permissions')}}">
                                </div>
                                <span class="text">{{__('languages.admin_sidebar.roles_and_permissions')}}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ (request()->is('settings')) ? 'active': '' }}">
                            <a href="{{route('settings')}}">
                                <!-- <span class="fa fa-cogs" title="{{__('languages.admin_sidebar.system_settings')}}"></span> -->
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.system_settings')}}" alt="{{__('languages.admin_sidebar.system_settings')}}">
                                </div>
                                <span class="text">{{__('languages.admin_sidebar.system_settings')}}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ (
                                request()->is('pre-configure-difficulty') || 
                                request()->is('pre-configure-difficulty/*')
                            ) ? 'active': '' }}">
                            <a href="{{route('pre-configure-difficulty.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/pre_configure.png') }}"  title="{{__('languages.pre_defined_difficulty')}}" alt="{{__('languages.pre_defined_difficulty')}}">
                                </div>
                                <span class="text">{{__('languages.pre_defined_difficulty')}}</span>
                            </a>
                        </li>

                        @if(in_array('global_configurations_update', $permissions))
                            <li class="nav-item {{ (request()->is('global-configuration')) ? 'active': '' }}">
                                <a href="{{route('global-configuration')}}">
                                    <!-- <span class="fa fa-wrench" title="{{__('languages.global_configurations')}}"></span> -->
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.global_configurations')}}" alt="{{__('languages.global_configurations')}}">
                                    </div>
                                    <span class="text">{{__('languages.global_configurations')}}</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <!-- <span class="fa fa-user" title="{{__('languages.admin_sidebar.default_annual_credit_amount')}}"></span> -->
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.admin_sidebar.default_annual_credit_amount')}}" alt="{{__('languages.admin_sidebar.default_annual_credit_amount')}}">
                                </div>
                                <span class="text">{{__('languages.admin_sidebar.default_annual_credit_amount')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </li>

        {{-- Activity Log --}}
        @if (in_array('user_activity_read', $permissions))
            <li class="nav-item {{ (request()->is('useractivity')) ? 'active': '' }}">
                <a href="{{route('useractivity.index')}}">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user_activity.png') }}"  title="{{__('languages.admin_sidebar.activity_log')}}" alt="{{__('languages.admin_sidebar.activity_log')}}">
                    </div>
                    <span class="text">{{__('languages.admin_sidebar.activity_log')}}</span>
                </a>
            </li>
        @endif

        {{-- Report --}}
        @if (in_array('reports_read', $permissions))
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (
                        request()->is('report/class-test-reports/correct-incorrect-answer') || 
                        request()->is('report/skill-weekness')
                    ) ? 'collapsed': '' }}" href="#report" data-toggle="collapse" data-target="#report">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.reports')}}" alt="{{__('languages.common_sidebar.reports')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.reports')}}</span>
                </a>
                <div class="collapse {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'show': '' }}" id="report" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('report.class-test-reports.correct-incorrect-answer')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/performance_report.png') }}"  title="{{__('languages.performance_report')}}" alt="{{__('languages.performance_report')}}">
                                </div>
                                <span class="text">{{__('languages.performance_report')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        {{-- Logout --}}
        <li>
            <a href="javascript:void(0);" id="logout">
                <!-- <span class="fa fa-sign-out" title="{{__('languages.common_sidebar.logout')}}"></span> -->
                <div class="sidebar_icon_main">
                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/performance_report.png') }}"  title="{{__('languages.common_sidebar.logout')}}" alt="{{__('languages.common_sidebar.logout')}}">
                </div>
                <span class="text">{{__('languages.common_sidebar.logout')}}</span>
            </a>
        </li>
    </ul>
    </nav>
@endif

        