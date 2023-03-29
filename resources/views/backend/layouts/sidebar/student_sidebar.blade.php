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
@if(Auth::user()->role_id == 3)
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
            <li class="{{ (request()->is('student/dashboard')) ? 'active' : ''}}">
                <a href="{{ route('student.dashboard') }}">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/home.png') }}"  title="{{__('languages.common_sidebar.dashboard')}}" alt="{{__('languages.common_sidebar.dashboard')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.dashboard')}}</span>
                </a>
            </li>
            {{-- Profile --}}
            <li class="nav-item">
                @if (in_array('my_account_read', $permissions))
                    <a class="nav-link text-truncate {{ (
                            request()->is('student-profile/*') || 
                            request()->is('profile') || 
                            request()->is('change-password') ||
                            request()->is('credit-point-history/*') || 
                            request()->is('student/leaderboard')) 
                        ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                        <div class="sidebar_icon_main">
                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.my_account')}}" alt="{{__('languages.my_account')}}">
                        </div>
                        <span class="text">{{__('languages.my_account')}}</span>
                    </a>
                    <div class="collapse {{ (
                                request()->is('student-profile/*') || 
                                request()->is('profile') || 
                                request()->is('change-password') || 
                                request()->is('credit-point-history/*') || 
                                request()->is('student/leaderboard')) 
                            ? 'show': '' }}" id="myaccount" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            {{-- Profile --}}
                            @if(in_array('profile_management_read', $permissions))
                                <li class="nav-item {{ (
                                            request()->is('student-profile/*') || 
                                            request()->is('profile') || 
                                            request()->is('credit-point-history/*')) 
                                        ? 'active': '' }}">
                                    <a class="nav-link" href="{{route('student-profiles',auth::user()->id)}}">
                                        <div class="sidebar_icon_main">
                                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.profile')}}" alt="{{__('languages.common_sidebar.profile')}}">
                                        </div>
                                        <span class="text">{{__('languages.common_sidebar.profile')}}</span>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('change_password_update', $permissions))
                                <li class="nav-item {{ (request()->is('change-password')) ? 'active': '' }}">
                                    <a class="nav-link" href="{{route('change-password')}}">
                                        <div class="sidebar_icon_main">
                                            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/password.png') }}"  title="{{__('languages.common_sidebar.change_password')}}" alt="{{__('languages.common_sidebar.change_password')}}">
                                        </div>
                                        <span class="text">{{__('languages.common_sidebar.change_password')}}</span>
                                    </a>
                                </li>
                            @endif

                            <!-- @if(in_array('leaderboard_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': '' }}">
                                <a class="nav-link" href="{{route('student/leaderboard')}}">
                                    <span class="fa fa-user" title="{{__('languages.common_sidebar.leaderboard')}}"></span>
                                    <span class="text">{{__('languages.common_sidebar.leaderboard')}}</span>
                                </a>
                            </li>
                            @endif -->
                        </ul>
                    </div>
                @endif
            </li>

           {{-- Learning --}}
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (
                        request()->is('student/self-learning/exercise') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || 
                        request()->is('student/create/self-learning-exercise') ||
                        request()->is('student/exercise/exam') || 
                        request()->is('student/test/exam') || 
                        request()->is('intelligent-tutor') || 
                        request()->is('my-peer-group')  || 
                        request()->is('peer-group/view/members/*') || 
                        request()->is('student/testing-zone') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true) || 
                        (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || 
                        request()->is('student/create/self-learning-test') ||
                        request()->is('student/attempt/test-exercise/*') || 
                        request()->is('exams/result/*') || 
                        request()->is('question-wizard/preview/*') || 
                        request()->is('student/progress-report/learning-units/*') ||
                        request()->is('student/progress-report/learning-objective/*')
                    ) ? 'collapsed': '' }}" href="#teaching_and_learning" data-toggle="collapse" data-target="#teaching_and_learning">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.teaching_and_learning')}}" alt="{{__('languages.common_sidebar.teaching_and_learning')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.learning')}}</span>
                </a>
                <div class="collapse {{ (
                        request()->is('student/self-learning/exercise') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || 
                        request()->is('student/create/self-learning-exercise') ||
                        request()->is('student/exercise/exam') || 
                        request()->is('student/test/exam') || 
                        request()->is('intelligent-tutor') || 
                        request()->is('my-peer-group') || 
                        request()->is('peer-group/view/members/*') || 
                        request()->is('student/testing-zone') || 
                        ((isset($isSelfLearningExam) && $isSelfLearningExam == true) || (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ||
                        request()->is('student/create/self-learning-test') ||
                        request()->is('student/attempt/test-exercise/*') || 
                        request()->is('exams/result/*') || 
                        request()->is('question-wizard/preview/*') || 
                        request()->is('student/progress-report/learning-units/*') || 
                        request()->is('student/progress-report/learning-objective/*')
                    ) ? 'show': ''  }}"
                        id="teaching_and_learning" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (
                                    request()->is('student/self-learning/exercise') || 
                                    ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || 
                                    request()->is('student/create/self-learning-exercise')) 
                                ? 'active': ''  }}">
                                <a href="{{ route('student.self-learning-exercise') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.self_learning')}}" alt="{{__('languages.common_sidebar.self_learning')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.self_learning')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('my_classes_read', $permissions))
                            <li class="nav-item {{ (
                                        request()->is('student/exercise/exam') || 
                                        (isset($isExerciseExam) && $isExerciseExam== true)) 
                                    ? 'active': '' }}">
                                <a href="{{ route('getStudentExerciseExamList') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.exercises')}}" alt="{{__('languages.common_sidebar.exercises')}}">
                                    </div>
                                    <span class="text">{{__('languages.exercise')}}</span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('peer_group_read', $permissions))
                            <li class="nav-item {{ (
                                        request()->is('student/test/exam') || 
                                        (isset($isTestExam) && $isTestExam == true)
                                    ) ? 'active': '' }}">
                                <a href="{{ route('getStudentTestExamList') }}">
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.test')}}" alt="{{__('languages.common_sidebar.test')}}">
                                    </div>
                                    <span class="text"> {{__('languages.common_sidebar.test')}} </span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item {{ (
                            request()->is('student/testing-zone') || 
                            request()->is('student/create/self-learning-test') || 
                            (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) 
                        ) ? 'active': ''}}">
                            <a href="{{ route('student.testing-zone') }}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/reports.png') }}"  title="{{__('languages.common_sidebar.ai_based_assessment')}}" alt="{{__('languages.common_sidebar.ai_based_assessment')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.ai_based_assessment')}}</span>
                            </a>
                        </li>

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (request()->is('intelligent-tutor')) ? 'active': ''  }}">
                                <a class="nav-link" href="{{ route('intelligent-tutor.index') }}">
                                    {{-- <span class="fa fa-user" title="{{__('languages.common_sidebar.intelligent_tutor')}}"></span> --}}
                                    <div class="sidebar_icon_main">
                                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.intelligent_tutor')}}" alt="{{__('languages.common_sidebar.intelligent_tutor')}}">
                                    </div>
                                    <span class="text">{{__('languages.common_sidebar.intelligent_tutor')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('peer_group_read', $permissions))
                        <li class="nav-item {{ (request()->is('my-peer-group') ||  request()->is('peer-group/view/members/*')) ? 'active': ''  }}">
                            <a class="nav-link" href="{{ route('my-peer-group') }}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/knowledge_tree.png') }}"  title="{{__('languages.common_sidebar.peer_group')}}" alt="{{__('languages.common_sidebar.peer_group')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.peer_group')}}</span>
                            </a>
                        </li>
                        @endif

                        {{-- Learning Progress Report --}}
                        <li class="nav-item">
                            <a class="nav-link text-truncate {{ (
                                    request()->is('student/progress-report/learning-units/*')|| 
                                    request()->is('student/progress-report/learning-objective/*')
                                ) ? 'show': ''  }}" 
                                href="#learning-progress" data-toggle="collapse" data-target="#learning-progress">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.admin_sidebar.learning_progress')}}" alt="{{__('languages.admin_sidebar.learning_progress')}}">
                                </div>   
                                <span class="text">{{__('languages.common_sidebar.learning_progress')}}</span>
                            </a>
                            <div class="collapse {{ (
                                    request()->is('student/progress-report/learning-units/*') || 
                                    request()->is('student/progress-report/learning-objective/*')
                                ) ? 'show': ''  }}"
                                 id="learning-progress" aria-expanded="false">
                                <ul class="flex-column pl-2 nav">
                                    <li class="nav-item {{(request()->is('student/progress-report/learning-units/*')) ? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('student.progress-report.learning-units',auth::user()->id)}}">
                                            <div class="sidebar_icon_main">
                                                <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/learning.png') }}"  title="{{__('languages.common_sidebar.learning_units')}}" alt="{{__('languages.common_sidebar.learning_units')}}">
                                            </div>
                                            <span class="text">{{__('languages.common_sidebar.learning_units')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{(request()->is('student/progress-report/learning-objective/*')) ? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('student.progress-report.learning-objective',auth::user()->id)}}">
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
        
            <li>
                <a href="#" id="game">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/intelligent_tutor.png') }}"  title="{{__('languages.common_sidebar.game')}}" alt="{{__('languages.common_sidebar.game')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.game')}}</span>
                </a>
            </li>

            {{-- Logout --}}
            <li>
                <a href="javascript:void(0);" id="logout">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/logout.png') }}"  title="{{__('languages.sidebar.logout')}}" alt="{{__('languages.sidebar.logout')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.logout')}}</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

        