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
@if(Auth::user()->role_id == 7)
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
            <li class="{{ (request()->is('principal/dashboard')) ? 'active' : ''}}">
                <a href="{{ route('principal.dashboard') }}">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/home.png') }}"  title="{{__('languages.common_sidebar.dashboard')}}" alt="{{__('languages.common_sidebar.dashboard')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.dashboard')}}</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-truncate {{(
                        request()->is('change-password')|| 
                        request()->is('profile') 
                    ) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                    <!-- <span class="fa"><i class="fa fa-user" title="{{__('languages.my_account')}}"></i></span> -->
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.my_account')}}" alt="{{__('languages.my_account')}}">
                    </div>
                    <span class="text">{{__('languages.my_account')}}</span>
                </a>
                <div class="collapse {{(
                        request()->is('change-password') || 
                        request()->is('profile')
                    ) ? 'show': '' }}" id="myaccount" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        {{-- Profile --}}
                        {{-- @if(in_array('profile_management_read', $permissions)) --}}
                            <li class="nav-item {{(request()->is('profile')) ? 'active' : ''}}">
                                <a class="nav-link" href="{{route('profile.index')}}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.profile')}}" alt="{{__('languages.common_sidebar.profile')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.profile')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if (in_array('change_password_update', $permissions)) --}}
                            <li class="nav-item  {{ (request()->is('change-password')) ? 'active': '' }}">
                                <a class="nav-link" href="{{route('change-password')}}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/password.png') }}"  title="{{__('languages.common_sidebar.change_password')}}" alt="{{__('languages.common_sidebar.change_password')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.change_password')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}
                    </ul>
                </div>
            </li>

            {{-- Teaching And Learning --}}
            <li class="nav-item">
                <a class="nav-link text-truncate 
                {{(
                    request()->is('question-wizard') ||
                    request()->is('question-wizard') || 
                    request()->is('exams/attempt/students/*') ||
                    request()->is('generate-questions') ||
                    request()->is('generate-questions-edit/*') ||
                    request()->is('learning-unit-ordering') ||
                    request()->is('learning-objectives-ordering') ||
                    request()->is('question-wizard/preview') ||
                    request()->is('student-profile/*') ||
                    request()->is('credit-point-history/*') ||
                    request()->is('my-class') ||
                    request()->is('peer-group') || 
                    request()->is('peer-group/*') ||
                    request()->is('auto-peer-group') ||
                    request()->is('student/leaderboard')
                    ? 'collapse' : '')
                }}" href="#teaching_and_learning" data-toggle="collapse" data-target="#teaching_and_learning">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.teaching_and_learning')}}" alt="{{__('languages.common_sidebar.teaching_and_learning')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.teaching_and_learning')}}</span>
                </a>
                <div class="collapse 
                    {{(
                        request()->is('question-wizard') ||
                        request()->is('question-wizard') ||
                        request()->is('exams/attempt/students/*') ||
                        request()->is('generate-questions') ||
                        request()->is('generate-questions-edit/*') ||
                        request()->is('learning-unit-ordering') ||
                        request()->is('learning-objectives-ordering') ||
                        request()->is('question-wizard/preview') ||
                        request()->is('student-profile/*') ||
                        request()->is('credit-point-history/*') ||
                        request()->is('my-class') ||
                        request()->is('peer-group') || 
                        request()->is('peer-group/*') ||
                        request()->is('auto-peer-group') ||
                        request()->is('student/leaderboard')
                        ? 'show' : '')
                }}"id="teaching_and_learning" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">

                        {{-- @if (in_array('exam_management_read', $permissions)) --}}
                            <li class="nav-item {{ (request()->is('learning-unit-ordering')) ? 'active': ''  }}">
                                <a href="{{ route('learning-unit-ordering') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.manage')}} {{__('languages.common_sidebar.learning_unit')}}" alt="{{__('languages.common_sidebar.learning_unit')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.learning_unit')}} </span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if (in_array('exam_management_read', $permissions)) --}}
                            <li class="nav-item {{ (request()->is('learning-objectives-ordering')) ? 'active': ''  }}">
                                <a href="{{ route('learning-objectives-ordering') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning_objective.png') }}"  title="{{__('languages.common_sidebar.learning_objective')}}" alt="{{__('languages.common_sidebar.learning_objective')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.learning_objective')}} </span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('question-wizard') || 
                                    request()->is('generate-questions') ||
                                    request()->is('exams/attempt/students/*') ||
                                    request()->is('exams/questions/add/*') ||
                                    request()->is('exams/students/add/*') ||
                                    request()->is('generate-questions') ||
                                    request()->is('generate-questions-edit/*') ||
                                    (request()->is('question-wizard/preview'))
                                ) ? 'active': ''  }}">
                                <a href="{{ route('question-wizard') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.exercise_and_test_wizard')}}" alt="{{__('languages.common_sidebar.exercise_and_test_wizard')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.exercise_and_test_wizard')}} </span>
                                </a>
                            </li>
                        @endif

                        {{-- @if (in_array('grade_management_read', $permissions)) --}}
                            <li class="nav-item {{ (
                                request()->is('my-class') ||
                                request()->is('credit-point-history/*') ||
                                request()->is('student-profile/*')
                            ) ? 'active': '' }} ">
                                <a href="{{ route('my-class') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.class')}}" alt="{{__('languages.common_sidebar.class')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.class')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if (in_array('peer_group_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('peer-group') || 
                                    request()->is('peer-group/*') || 
                                    request()->is('auto-peer-group')
                                ) ? 'active': '' }}">
                                <a href="{{ route('peer-group.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/knowledge_tree.png') }}"  title="{{__('languages.common_sidebar.peer_group')}}" alt="{{__('languages.common_sidebar.peer_group')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.peer_group')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('assign_credit_points_read', $permissions))
                            <li class="nav-item {{ (request()->is('assign-credit-points')) ? 'active': ''}}">
                                <a href="{{ route('assign-credit-points') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning_objective.png') }}"  title="{{__('languages.common_sidebar.assign_credit')}}" alt="{{__('languages.common_sidebar.assign_credit')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.assign_credit')}}</span>
                                </a>
                            </li>
                        @endif

                        <!-- @if(in_array('leaderboard_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': ''  }}">
                                <a class="nav-link" href="{{route('student/leaderboard')}}">
                                    <span class="fa fa-sitemap" title="{{__('languages.common_sidebar.leaderboard')}}"></span>
                                    <span class="text">{{__('languages.common_sidebar.leaderboard')}}</span>
                                </a>
                            </li>
                        @endif -->
                    </ul>
                </div>
            </li>

            {{-- Report --}}
            @if (in_array('reports_read', $permissions))
                <li class="nav-item">
                    <a class="nav-link text-truncate {{ (
                        request()->is('myteaching/selflearning-exercise') ||
                        (isset($isSelfLearningExam) && $isSelfLearningExam == true)|| 
                        request()->is('exams/result/*/*') || 
                        request()->is('myteaching/selflearning-tests') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ||
                        request()->is('myteaching/assignment-exercise') || request()->is('myteaching/assignment-tests') ||
                        (isset($menuItem) && ($menuItem == 'exercise' || $menuItem == 'test' || $menuItem == 'self_learning' || $menuItem == 'testing_zone')) || 
                        (request()->is('learning-progress/learning-units')) ||
                        request()->is('student/progress-report/learning-units/*') ||
                        request()->is('student/progress-report/learning-objective/*') ||
                        request()->is('learning-progress/learning-objectives')
                    ) ? 'collapsed': '' }}" href="#report" data-toggle="collapse" data-target="#report">
                        <div class="sidebar_icon_main">
                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.reports')}}" alt="{{__('languages.admin_sidebar.reports')}}">
                        </div>
                        <span class="text">{{__('languages.common_sidebar.reports')}}</span>
                    </a>
                    <div class="collapse {{ (
                            request()->is('myteaching/selflearning-exercise') || 
                            (isset($isSelfLearningExam) && $isSelfLearningExam == true) || 
                            request()->is('exams/result/*/*') || 
                            request()->is('myteaching/selflearning-tests') || 
                            ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '')
                            || request()->is('myteaching/assignment-exercise') || request()->is('myteaching/assignment-tests') || 
                            (isset($menuItem) && ($menuItem == 'exercise' || $menuItem == 'test' || $menuItem == 'self_learning' || $menuItem == 'testing_zone')) || 
                            (request()->is('learning-progress/learning-units')) ||
                            request()->is('student/progress-report/learning-units/*') ||
                            request()->is('student/progress-report/learning-objective/*') ||
                            request()->is('learning-progress/learning-objectives')
                        ) ? 'show': '' }}" id="report" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            @if(in_array('self_learning_read', $permissions))
                                <li class="nav-item {{ (
                                        request()->is('myteaching/selflearning-exercise') || 
                                        (isset($isSelfLearningExam) && $isSelfLearningExam == true) || 
                                        request()->is('exams/result/*/*') || 
                                        (isset($menuItem) && $menuItem == 'self_learning')
                                    ) ? 'active': '' }}">
                                    <a class="nav-link" href="{{ route('myteaching/selflearning-exercise') }}">
                                        <div class="sidebar_icon_main">
                                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.self_learning')}}" alt="{{__('languages.common_sidebar.self_learning')}}">
                                        </div>
                                        <span class="text">{{__('languages.common_sidebar.self_learning')}}</span>
                                    </a>
                                </li>
                            @endif

                            @if(in_array('assignment_or_test_read', $permissions))
                                <li class="nav-item {{ (
                                        request()->is('myteaching/assignment-exercise') || 
                                        (isset($menuItem) && $menuItem == 'exercise')
                                    ) ? 'active': '' }}">
                                    <a class="nav-link" href="{{ route('myteaching/assignment-exercise') }}">
                                        <div class="sidebar_icon_main">
                                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.exercises')}}" alt="{{__('languages.common_sidebar.exercises')}}">
                                        </div>
                                        <span class="text">{{__('languages.exercise')}}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item {{ (
                                    request()->is('myteaching/assignment-tests') || 
                                    (isset($menuItem) && $menuItem == 'test')
                                ) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.assignment-tests') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.test')}}" alt="{{__('languages.common_sidebar.test')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.test')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ (
                                    request()->is('myteaching/selflearning-tests') || 
                                    ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || 
                                    (isset($menuItem) && $menuItem == 'testing_zone') 
                                ) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.selflearning-tests') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.ai_based_assessment')}}" alt="{{__('languages.common_sidebar.ai_based_assessment')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.ai_based_assessment')}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-truncate {{(
                                        request()->is('principal/progress-report/learning-objective') || 
                                        (request()->is('learning-progress/learning-units') ||
                                        request()->is('student/progress-report/learning-units/*') ||
                                        request()->is('student/progress-report/learning-objective/*') ||
                                        request()->is('learning-progress/learning-objectives')
                                    ) ? 'collapse' : '')}}" href="#learning_progress_report" data-toggle="collapse" data-target="#learning_progress_report">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.learning_progress')}}" alt="{{__('languages.admin_sidebar.learning_progress')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.learning_progress')}}</span>
                                </a>
                                <div class="collapse {{(
                                            request()->is('learning-progress/learning-units') || 
                                            request()->is('student/progress-report/learning-units/*') ||  
                                            request()->is('student/progress-report/learning-objective/*') ||
                                            request()->is('learning-progress/learning-objectives')
                                        ? 'show' : '')}}" id="learning_progress_report" aria-expanded="false">
                                    <ul class="flex-column pl-2 nav">
                                        <li class="nav-item {{(
                                                    request()->is('learning-progress/learning-units') || 
                                                    request()->is('student/progress-report/learning-units/*') 
                                                ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('learning-progress.learning-units')}}">
                                                <div class="sidebar_icon_main">
                                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.common_sidebar.learning_units')}}" alt="{{__('languages.common_sidebar.learning_units')}}">
                                                </div>
                                                <span class="text">{{__('languages.common_sidebar.learning_units')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{(
                                                request()->is('principal/progress-report/learning-objectives') || 
                                                request()->is('student/progress-report/learning-objective/*') ||
                                                request()->is('learning-progress/learning-objectives') 
                                            ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('learning-progress.learning-objectives')}}">
                                                <div class="sidebar_icon_main">
                                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.common_sidebar.learning_objectives')}}" alt="{{__('languages.common_sidebar.learning_objectives')}}">
                                                </div>
                                                <span class="text">{{__('languages.common_sidebar.learning_objectives')}}</span>
                                            </a>
                                        </li>           
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- Video --}}
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (request()->is('intelligent-tutor')) ? 'collapsed': '' }}" href="#video" data-toggle="collapse" data-target="#video">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.video')}}" alt="{{__('languages.common_sidebar.video')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.video')}}</span>
                </a>
                <div class="collapse {{ (request()->is('intelligent-tutor')) ? 'show': '' }}" id="video" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item">
                            <a href="#">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.game_intro_video')}}" alt="{{__('languages.common_sidebar.game_intro_video')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.game_intro_video')}}</span>
                            </a>
                        </li>

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
                                <a href="{{ route('intelligent-tutor.index') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.intelligent_tutor_video')}}" alt="{{__('languages.common_sidebar.intelligent_tutor_video')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.intelligent_tutor_video')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

             {{-- School Admin Privilege Menus --}}
             @include('backend.layouts.sidebar.school_admin_privilege_menu')

            {{-- Logout --}}
            <li>
                <a href="javascript:void(0);" id="logout">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/logout.png') }}"  title="{{__('languages.sidebar.logout')}}" alt="{{__('languages.sidebar.logout')}}">
                    </div>
                    <span class="text">{{__('languages.sidebar.logout')}}</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

        