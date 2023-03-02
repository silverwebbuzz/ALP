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
@if(Auth::user()->role_id == 7)
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
            <li class="{{ (request()->is('principal/dashboard')) ? 'active' : ''}}">
                <a href="{{ route('principal.dashboard') }}">
                    <span class="fa fa-home"></span>
                    <span class="text">{{__('languages.sidebar.dashboard')}}</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-truncate {{(request()->is('change-password') || request()->is('profile') ) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                    <span class="fa"><i class="fa fa-user"></i></span>
                    <span class="text">{{__('languages.my_account')}}</span>
                </a>
                <div class="collapse {{(request()->is('change-password') || request()->is('profile')) ? 'show': '' }}" id="myaccount" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        {{-- Profile --}}
                        {{-- @if(in_array('profile_management_read', $permissions)) --}}
                            <li class="nav-item {{(request()->is('profile')) ? 'active' : ''}}">
                                <a class="nav-link" href="{{route('profile.index')}}">
                                    <span class="fa fa-user"></span>
                                    <span class="text">{{__('Profile')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if (in_array('change_password_update', $permissions)) --}}
                            <li class="nav-item  {{ (request()->is('change-password')) ? 'active': '' }}">
                                <a class="nav-link" href="{{route('change-password')}}">
                                    <span class="fa fa-user"></span>
                                    <span class="text">{{__('languages.change_password')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}
                    </ul>
                </div>
            </li>

            {{-- Teaching And Learning --}}
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (request()->is('my-class') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') 
                || request()->is('student/progress-report/learning-objective/*') || request()->is('peer-group') || request()->is('peer-group/*') || request()->is('auto-peer-group')
                || request()->is('assign-credit-points') || request()->is('student/leaderboard')
                || request()->is('class') || request()->is('class/*') || request()->is('learning-objectives-ordering') || request()->is('learning-unit-ordering')
                ) ? 'collapsed': '' }}" href="#teaching_and_learning" data-toggle="collapse" data-target="#teaching_and_learning">
                    <span class="fa"><i class="fa fa-book"></i></span>
                    <span class="text">{{__('Teaching And Learning')}}</span>
                </a>
                <div class="collapse {{ (request()->is('peer-group') || request()->is('peer-group/*') || request()->is('auto-peer-group') || request()->is('auto-peer-group') ||request()->is('question-wizard') || request()->is('generate-questions') 
                || request()->is('question-wizard/preview/*') || request()->is('exams/attempt/students/*') || request()->is('exams/questions/add/*') || request()->is('exams/students/add/*')
                || request()->is('generate-questions') || request()->is('generate-questions-edit/*') || request()->is('assign-credit-points') || request()->is('student/leaderboard') 
                || request()->is('my-class') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') || request()->is('student/progress-report/learning-objective/*')
                || request()->is('learning-objectives-ordering') || request()->is('learning-unit-ordering')
                ) ? 'show': ''  }}"
                        id="teaching_and_learning" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">

                        {{-- @if (in_array('exam_management_read', $permissions)) --}}
                            <li class="nav-item {{ (request()->is('learning-unit-ordering')) ? 'active': ''  }}">
                                <a href="{{ route('learning-unit-ordering') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Learning Unit')}} </span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        {{-- @if (in_array('exam_management_read', $permissions)) --}}
                            <li class="nav-item {{ (request()->is('learning-objectives-ordering')) ? 'active': ''  }}">
                                <a href="{{ route('learning-objectives-ordering') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Learning Objective')}} </span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (request()->is('question-wizard') || request()->is('generate-questions') || request()->is('question-wizard/preview/*') || request()->is('exams/attempt/students/*') || request()->is('exams/questions/add/*') || request()->is('exams/students/add/*') || request()->is('generate-questions') || request()->is('generate-questions-edit/*')) ? 'active': ''  }}">
                                <a href="{{ route('question-wizard') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Exercise and Test Wizard')}} </span>
                                </a>
                            </li>
                        @endif

                        {{-- @if (in_array('grade_management_read', $permissions)) --}}
                            <li class="nav-item {{ (request()->is('my-class') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') || request()->is('student/progress-report/learning-objective/*')) ? 'active': '' }} ">
                                <a href="{{ route('my-class') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text">{{__('Class')}}</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if (in_array('peer_group_read', $permissions))
                            <li class="nav-item {{ (request()->is('peer-group') || request()->is('peer-group/create') || request()->is('peer-group/*') || request()->is('auto-peer-group')) ? 'active': '' }}">
                                <a href="{{ route('peer-group.index') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Peer Group')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('assign_credit_points_read', $permissions))
                            <li class="nav-item {{ (request()->is('assign-credit-points')) ? 'active': ''}}">
                                <a href="{{ route('assign-credit-points') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text">{{__('Assign Credit')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('leaderboard_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': ''  }}">
                                <a class="nav-link" href="{{route('student/leaderboard')}}">
                                    <span class="fa fa-sitemap"></span>
                                    <span class="text">{{__('Leaderboard')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- Report --}}
            @if (in_array('reports_read', $permissions))
                <li class="nav-item">
                    <a class="nav-link text-truncate {{ (request()->is('myteaching/selflearning-exercise') || (isset($isSelfLearningExam) && $isSelfLearningExam == true) || request()->is('exams/result/*/*')
                    || request()->is('myteaching/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '')
                    || request()->is('myteaching/assignment-exercise') || request()->is('myteaching/assignment-tests')
                    ) ? 'collapsed': '' }}" href="#report" data-toggle="collapse" data-target="#report">
                        <span class="fa"><i class="fa fa-cogs"></i></span>
                        <span class="text">{{__('Reports')}}</span>
                    </a>
                    <div class="collapse {{ (request()->is('myteaching/selflearning-exercise') || (isset($isSelfLearningExam) && $isSelfLearningExam == true) || request()->is('exams/result/*/*')
                    || request()->is('myteaching/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '')
                    || request()->is('myteaching/assignment-exercise') || request()->is('myteaching/assignment-tests')
                    ) ? 'show': '' }}" id="report" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            @if(in_array('self_learning_read', $permissions))
                                <li class="nav-item {{ (request()->is('myteaching/selflearning-exercise') || (isset($isSelfLearningExam) && $isSelfLearningExam == true) || request()->is('exams/result/*/*')) ? 'active': '' }}">
                                    <a class="nav-link" href="{{ route('myteaching/selflearning-exercise') }}">
                                        <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        <span class="text">{{__('Self Learning')}}</span>
                                    </a>
                                </li>
                            @endif

                            @if(in_array('assignment_or_test_read', $permissions))
                                <li class="nav-item {{ request()->is('myteaching/assignment-exercise') ? 'active': '' }}">
                                    <a class="nav-link" href="{{ route('myteaching/assignment-exercise') }}">
                                        <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        <span class="text">{{__('Exercise')}}</span>
                                    </a>
                                </li>
                            @endif

                            
                            <li class="nav-item {{ (request()->is('myteaching/assignment-tests')) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.assignment-tests') }}">
                                    <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                    <span class="text">{{__('Test')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ (request()->is('myteaching/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '')) ? 'active': '' }}">
                                <a class="nav-link" href="{{ route('myteaching.selflearning-tests') }}">
                                    <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                    <span class="text">{{__('AI-Based Assessment')}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-truncate {{(request()->is('principal/progress-report/learning-objective') || request()->is('principal/progress-report/learning-units') ? 'collapse' : '')}}" href="#learning_progress_report" data-toggle="collapse" data-target="#learning_progress_report">
                                    <span class="fa"><i class="fa fa-cogs"></i></span>
                                    <span class="text">{{__('Learning Progress')}}</span>
                                </a>
                                <div class="collapse {{(request()->is('principal/progress-report/learning-objective') || request()->is('principal/progress-report/learning-units') ? 'show' : '')}}" id="learning_progress_report" aria-expanded="false">
                                    <ul class="flex-column pl-2 nav">
                                        <li class="nav-item {{( request()->is('principal/progress-report/learning-units') ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('principal.progress-report.learning-units')}}">
                                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                                <span class="text">{{__('Learning Unit')}}</span>
                                            </a>
                                        </li> 
                                        <li class="nav-item {{(request()->is('principal/progress-report/learning-objective') ? 'active' : '')}}">
                                            <a class="nav-link" href="{{route('principal.progress-report.learning-objective')}}">
                                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                                <span class="text">{{__('Learning Objective')}}</span>
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
                    <span class="fa"><i class="fa fa-cogs"></i></span>
                    <span class="text">{{__('Video')}}</span>
                </a>
                <div class="collapse {{ (request()->is('intelligent-tutor')) ? 'show': '' }}" id="video" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item">
                            <a href="#">
                                <span class="fa fa-file"></span>
                                <span class="text">{{__('Game Inro Video')}}</span>
                            </a>
                        </li>

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
                                <a href="{{ route('intelligent-tutor.index') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text">{{__('Intelligent Tutor Video')}}</span>
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
                    <span class="text">{{__('languages.sidebar.logout')}}</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

        