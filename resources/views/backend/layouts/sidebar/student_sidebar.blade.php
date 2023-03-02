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
@if(Auth::user()->role_id == 3)
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
            <li class="{{ (request()->is('student/dashboard')) ? 'active' : ''}}">
                <a href="{{ route('student.dashboard') }}">
                    <span class="fa fa-home"></span>
                    <span class="text">{{__('languages.sidebar.dashboard')}}</span>
                </a>
            </li>
            {{-- Profile --}}
            <li class="nav-item">
                @if (in_array('my_account_read', $permissions))
                    <a class="nav-link text-truncate {{ (request()->is('student-profile/*') || request()->is('profile') || request()->is('change-password') 
                    || request()->is('credit-point-history/*') || request()->is('student/progress-report/learning-units/*') || request()->is('student/progress-report/learning-objective/*') || request()->is('student/leaderboard')) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                        <span class="fa"><i class="fa fa-user"></i></span>
                        <span class="text">{{__('languages.my_account')}}</span>
                    </a>
                    <div class="collapse {{ (request()->is('student-profile/*') || request()->is('profile') || request()->is('change-password') || request()->is('credit-point-history/*') || request()->is('student/progress-report/learning-units/*') || request()->is('student/progress-report/learning-objective/*') || request()->is('student/leaderboard')) ? 'show': '' }}" id="myaccount" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            {{-- Profile --}}
                            @if(in_array('profile_management_read', $permissions))
                                <li class="nav-item {{ (request()->is('student-profile/*') || request()->is('profile') || request()->is('credit-point-history/*') || request()->is('student/progress-report/learning-units/*') || request()->is('student/progress-report/learning-objective/*')) ? 'active': '' }}">
                                    <a class="nav-link" href="{{route('student-profiles',auth::user()->id)}}">
                                        <span class="fa fa-user"></span>
                                        <span class="text">{{__('Profile')}}</span>
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

                            @if (in_array('leaderboard_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': '' }}">
                                <a class="nav-link" href="{{route('student/leaderboard')}}">
                                    <span class="fa fa-user"></span>
                                    <span class="text">{{__('languages.sidebar.leaderboard')}}</span>
                                </a>
                            </li>
                        @endif
                        </ul>
                    </div>
                @endif
            </li>

           {{-- Learning --}}
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (request()->is('student/self-learning/exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-exercise')
                || request()->is('student/exercise/exam') || request()->is('student/test/exam') || request()->is('intelligent-tutor') || request()->is('my-peer-group')
                || request()->is('student/testing-zone') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) || (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-test')
                || request()->is('student/attempt/test-exercise/*') || request()->is('exams/result/*') || request()->is('question-wizard/preview/*') 
                ) ? 'collapsed': '' }}" href="#teaching_and_learning" data-toggle="collapse" data-target="#teaching_and_learning">
                    <span class="fa"><i class="fa fa-book"></i></span>
                    <span class="text">{{__('Learning')}}</span>
                </a>
                <div class="collapse {{ (request()->is('student/self-learning/exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-exercise')
                || request()->is('student/exercise/exam') || request()->is('student/test/exam') || request()->is('intelligent-tutor') || request()->is('my-peer-group')
                || request()->is('student/testing-zone') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) || (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-test') 
                || request()->is('student/attempt/test-exercise/*') || request()->is('exams/result/*') || request()->is('question-wizard/preview/*')
                ) ? 'show': ''  }}"
                        id="teaching_and_learning" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">

                        @if (in_array('exam_management_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/self-learning/exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-exercise')) ? 'active': ''  }}">
                                <a href="{{ route('student.self-learning-exercise') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Self Learning')}} </span>
                                </a>
                            </li>
                        @endif

                        @if(in_array('my_classes_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/exercise/exam') || (isset($isExerciseExam) && $isExerciseExam== true)) ? 'active': '' }}">
                                <a href="{{ route('getStudentExerciseExamList') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text">{{__('Exercise')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('peer_group_read', $permissions))
                            <li class="nav-item {{ (request()->is('student/test/exam') || (isset($isTestExam) && $isTestExam == true)) ? 'active': '' }}">
                                <a href="{{ route('getStudentTestExamList') }}">
                                    <span class="fa fa-file"></span>
                                    <span class="text"> {{__('Test')}} </span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item {{ (request()->is('student/testing-zone') || request()->is('student/create/self-learning-test') || (isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) 
                        ) ? 'active': ''}}">
                            <a href="{{ route('student.testing-zone') }}">
                                <span class="fa fa-file"></span>
                                <span class="text">{{__('AI-Based Assesement')}}</span>
                            </a>
                        </li>

                        @if(in_array('intelligent_tutor_read',$permissions))
                            <li class="nav-item {{ (request()->is('intelligent-tutor')) ? 'active': ''  }}">
                                <a class="nav-link" href="{{ route('intelligent-tutor.index') }}">
                                    <span class="fa fa-sitemap"></span>
                                    <span class="text">{{__('Intelligent Tutor')}}</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('peer_group_read', $permissions))
                        <li class="nav-item {{ (request()->is('my-peer-group')) ? 'active': ''  }}">
                            <a class="nav-link" href="{{ route('my-peer-group') }}">
                                <span class="fa fa-sitemap"></span>
                                <span class="text">{{__('Peer Group')}}</span>
                            </a>
                        </li>
                    @endif
                    </ul>
                </div>
            </li>
        
            <li>
                <a href="#" id="game">
                    <span class="fa fa-file"></span>
                    <span class="text">{{__('Game')}}</span>
                </a>
            </li>

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

        