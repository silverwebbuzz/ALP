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
@endphp

@php
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
        background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?>
    }
    .sm-deskbord-main-sec #sidebar.active ul li.active {
        background-color: <?php echo App\Helpers\Helper::getRoleBasedMenuActiveColor(); ?>
    }
</style>

<!-- Super Admin Sidebar Menus -->
@if(Auth::user()->role_id == 9)
    <nav id="sidebar" class="@if(!empty(Session::get('sidebar'))){{Session::get('sidebar')}}@endif" style="background-color:{{$color}};">
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
            <li class="{{ (request()->is('co-ordinator/dashboard')) ? 'active' : ''}}">
                <a href="{{ route('co-ordinator.dashboard') }}">
                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/home.png') }}"  title="{{__('languages.common_sidebar.dashboard')}}" alt="{{__('languages.common_sidebar.dashboard')}}">
                    <span class="text">{{__('languages.common_sidebar.dashboard')}}</span>
                </a>
            </li>
            {{-- Profile --}}
            <li class="nav-item">
                @if (in_array('my_account_read', $permissions))
                    <a class="nav-link text-truncate {{ (request()->is('change-password')) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                        <!-- <span class="fa"><i class="fa fa-user" title="{{__('languages.my_account')}}"></i></span> -->
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.my_account')}}" alt="{{__('languages.my_account')}}">
                        <span class="text">{{__('languages.my_account')}}</span>
                    </a>
                    <div class="collapse {{ (
                                request()->is('change-password') || 
                                request()->is('profile')
                            ) ? 'show': '' }}" id="myaccount" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            {{-- Profile --}}
                            @if(in_array('profile_management_read', $permissions))
                                <li class="nav-item {{(request()->is('profile')) ? 'active' : ''}}" >
                                    <a class="nav-link" href="{{route('profile.index')}}">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.profile')}}" alt="{{__('languages.common_sidebar.profile')}}">
                                        <span class="text">{{__('languages.common_sidebar.profile')}}</span>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('change_password_update', $permissions))
                                <li class="nav-item {{ (request()->is('change-password')) ? 'active': '' }}">
                                    <a class="nav-link" href="{{route('change-password')}}">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/password.png') }}"  title="{{__('languages.common_sidebar.change_password')}}" alt="{{__('languages.common_sidebar.change_password')}}">
                                        <span class="text">{{__('languages.common_sidebar.change_password')}}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </li>

            {{-- Teaching And Learning --}}
            <li class="nav-item">
                <a class="nav-link text-truncate {{(
                    request()->is('my-class') ||
                    request()->is('credit-point-history/*') || 
                    request()->is('student-profile/*') ||
                    request()->is('peer-group') || 
                    request()->is('peer-group/*') || 
                    request()->is('auto-peer-group') ||
                    request()->is('assign-credit-points') || 
                    request()->is('student/leaderboard')
                ) ? 'collapsed': '' }}" href="#teaching_and_learning" data-toggle="collapse" data-target="#teaching_and_learning">
                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.teaching_and_learning')}}" alt="{{__('languages.common_sidebar.teaching_and_learning')}}">
                    <span class="text">{{__('languages.common_sidebar.teaching_and_learning')}}</span>
                </a>
                <div class="collapse {{(
                    request()->is('my-class') ||
                    request()->is('credit-point-history/*') || 
                    request()->is('student-profile/*') ||
                    request()->is('peer-group') || 
                    request()->is('peer-group/*') || 
                    request()->is('auto-peer-group') || 
                    request()->is('auto-peer-group') ||
                    request()->is('question-wizard') || 
                    request()->is('generate-questions') ||
                    request()->is('question-wizard/preview/*') || 
                    request()->is('exams/attempt/students/*') || 
                    request()->is('exams/questions/add/*') || 
                    request()->is('exams/students/add/*') ||
                    request()->is('generate-questions') || 
                    request()->is('generate-questions-edit/*') || 
                    request()->is('assign-credit-points') || 
                    request()->is('student/leaderboard') 
                ) ? 'show': ''  }}"
                        id="teaching_and_learning" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('question-wizard') ||
                                    request()->is('generate-questions') ||
                                    request()->is('question-wizard/preview/*') ||
                                    request()->is('exams/attempt/students/*') ||
                                    request()->is('exams/questions/add/*') || 
                                    request()->is('exams/students/add/*') || 
                                    request()->is('generate-questions') || 
                                    request()->is('generate-questions-edit/*')
                                ) ? 'active': ''  }}">
                                <a href="{{ route('question-wizard') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.exercise_and_test_wizard')}}" alt="{{__('languages.common_sidebar.exercise_and_test_wizard')}}">
                                    <span class="text"> {{__('languages.common_sidebar.exercise_and_test_wizard')}} </span>
                                </a>
                            </li>
                        @endif

                        {{-- @if(in_array('my_classes_read', $permissions)) --}}
                            <li class="nav-item {{ (
                                    request()->is('my-class') ||
                                    request()->is('credit-point-history/*') || 
                                    request()->is('student-profile/*')
                                ) ? 'active': '' }}">
                                <a href="{{ route('my-class') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.class')}}" alt="{{__('languages.common_sidebar.class')}}">
                                    <span class="text">{{__('languages.common_sidebar.class')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if (in_array('peer_group_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('peer-group') || 
                                    request()->is('peer-group/create') || 
                                    request()->is('peer-group/*') || 
                                    request()->is('auto-peer-group')
                                ) ? 'active': '' }}">
                                <a href="{{ route('peer-group.index') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/knowledge_tree.png') }}"  title="{{__('languages.common_sidebar.peer_group')}}" alt="{{__('languages.common_sidebar.peer_group')}}">
                                    <span class="text"> {{__('languages.common_sidebar.peer_group')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('assign_credit_points_read', $permissions))
                            <li class="nav-item {{ (request()->is('assign-credit-points')) ? 'active': ''}}">
                                <a href="{{ route('assign-credit-points') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning_objective.png') }}"  title="{{__('languages.common_sidebar.assign_credit')}}" alt="{{__('languages.common_sidebar.assign_credit')}}">
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
                        (isset($isSelfLearningExam) && $isSelfLearningExam == true) || 
                        request()->is('exams/result/*/*') ||
                        request()->is('myteaching/selflearning-tests') ||
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ||
                        request()->is('myteaching/assignment-exercise') || 
                        request()->is('myteaching/assignment-tests') || 
                        request()->is('student/progress-report/learning-objective/*') ||
                        request()->is('learning-progress/learning-objectives') || 
                        request()->is('principal/progress-report/learning-units') || 
                        (isset($menuItem) && ($menuItem == 'exercise' || $menuItem == 'test' || $menuItem == 'self_learning' || $menuItem == 'testing_zone')) || 
                        (request()->is('learning-progress/learning-units')) || 
                        request()->is('student/progress-report/learning-units/*') || 
                        request()->is('student/progress-report/learning-objective/*')
                    ) ? 'collapsed': '' }}" href="#report" data-toggle="collapse" data-target="#report">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.reports')}}" alt="{{__('languages.admin_sidebar.reports')}}">
                        <span class="text">{{__('languages.common_sidebar.reports')}}</span>
                    </a>
                    <div class="collapse {{ (
                        request()->is('myteaching/selflearning-exercise') || 
                        (isset($isSelfLearningExam) && $isSelfLearningExam == true) || 
                        request()->is('exams/result/*/*') ||
                        request()->is('myteaching/selflearning-tests') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ||
                        request()->is('myteaching/assignment-exercise') || 
                        request()->is('myteaching/assignment-tests') || 
                        request()->is('learning-progress/learning-objectives') ||
                        request()->is('student/progress-report/learning-objective/*') || 
                        (isset($menuItem) && ($menuItem == 'exercise' || $menuItem == 'test' || $menuItem == 'self_learning' || $menuItem == 'testing_zone')) || 
                        (request()->is('learning-progress/learning-units')) || 
                        request()->is('student/progress-report/learning-units/*')
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
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.self_learning')}}" alt="{{__('languages.common_sidebar.self_learning')}}">
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
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.exercises')}}" alt="{{__('languages.common_sidebar.exercises')}}">
                                        <span class="text">{{__('languages.exercise')}}</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item {{ (
                                        request()->is('myteaching/assignment-tests') || 
                                        (isset($menuItem) && $menuItem == 'test')
                                    ) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.assignment-tests') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.test')}}" alt="{{__('languages.common_sidebar.test')}}">
                                    <span class="text">{{__('languages.common_sidebar.test')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ (
                                    request()->is('myteaching/selflearning-tests') || 
                                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || 
                                        (isset($menuItem) && $menuItem == 'testing_zone')
                                    ) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.selflearning-tests') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.ai_based_assessment')}}" alt="{{__('languages.common_sidebar.ai_based_assessment')}}">
                                    <span class="text">{{__('languages.common_sidebar.ai_based_assessment')}}</span>
                                </a>
                            </li>
                            {{-- Learning Progress --}}
                            <li class="nav-item">
                                <a class="nav-link text-truncate {{(
                                            request()->is('learning-progress/learning-units') || 
                                            request()->is('learning-progress/learning-objectives') || 
                                            request()->is('student/progress-report/learning-units/*') ||
                                            request()->is('student/progress-report/learning-objective/*')
                                        ? 'collapse' : '')}}" href="#learning_progress_report" data-toggle="collapse" data-target="#learning_progress_report">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.learning_progress')}}" alt="{{__('languages.admin_sidebar.learning_progress')}}">
                                    <span class="text">{{__('languages.common_sidebar.learning_progress')}}</span>
                                </a>
                                <div class="collapse {{(
                                            request()->is('learning-progress/learning-units') || 
                                            request()->is('learning-progress/learning-objectives') || 
                                            request()->is('student/progress-report/learning-units/*') ||
                                            request()->is('student/progress-report/learning-objective/*')
                                        ? 'show' : '')}}" id="learning_progress_report" aria-expanded="false">
                                    <ul class="flex-column pl-2 nav">
                                        <li class="nav-item {{(
                                                    request()->is('learning-progress/learning-units') || 
                                                    request()->is('student/progress-report/learning-units/*') 
                                                ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('learning-progress.learning-units')}}">
                                                <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.common_sidebar.learning_units')}}" alt="{{__('languages.common_sidebar.learning_units')}}">
                                                <span class="text">{{__('languages.common_sidebar.learning_units')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{(
                                                    request()->is('learning-progress/learning-objectives') ||
                                                    request()->is('student/progress-report/learning-objective/*')
                                                ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('learning-progress.learning-objectives')}}">
                                                <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.common_sidebar.learning_objectives')}}" alt="{{__('languages.common_sidebar.learning_objectives')}}">
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
                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.video')}}" alt="{{__('languages.common_sidebar.video')}}">
                    <span class="text">{{__('languages.common_sidebar.video')}}</span>
                </a>
                <div class="collapse {{ (request()->is('intelligent-tutor')) ? 'show': '' }}" id="video" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item">
                            <a href="#">
                                <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.game_intro_video')}}" alt="{{__('languages.common_sidebar.game_intro_video')}}">
                                <span class="text">{{__('languages.common_sidebar.game_intro_video')}}</span>
                            </a>
                        </li>

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
                                <a href="{{ route('intelligent-tutor.index') }}">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.intelligent_tutor_video')}}" alt="{{__('languages.common_sidebar.intelligent_tutor_video')}}">
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
                    <span class="fa fa-sign-out"></span>
                    <span class="text" title="{{__('languages.common_sidebar.logout')}}">{{__('languages.common_sidebar.logout')}}</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

        