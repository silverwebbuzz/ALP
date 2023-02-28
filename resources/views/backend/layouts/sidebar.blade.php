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
@if(Auth::user()->role_id == 1)
    @include('backend.layouts.sidebar.admin_sidebar') 
@endif
<!-- End Admin Sidebar Menus -->

<!-- Start Teacher Sidebar Menus -->
@if(Auth::user()->role_id == 2)
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
        <li class="{{ (request()->is('teacher/dashboard')) ? 'active': '' }}">
            <a href="{{ route('teacher.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('teacher/profile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'collapsed': '' }}" href="#my_account_teacher" data-toggle="collapse" data-target="#my_account_teacher">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('teacher/profile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'show': '' }}" id="my_account_teacher" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('profile_management_read', $permissions))
                    <li class="nav-item {{ (request()->is('teacher/profile')) ? 'active': '' }}">
                        <a href="{{ route('teacher.profile') }}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.sidebar.profile')}}</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(in_array('change_password_update', $permissions))
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
        </li>

         <!-- Upload Documents -->
         @if(in_array('upload_documents_read',$permissions))
            <li class="{{ (request()->is('upload-documents')) ? 'active': ''}}">
                <a href="{{ route('upload-documents.index') }}">
                    <span class="fa fa-file"></span>
                    <span class="text"> {{__('languages.sidebar.upload_documents')}} </span>
                </a>
            </li>
        @endif

        @if (in_array('exam_management_read', $permissions))
        <li class="{{ (request()->is('question-wizard') || request()->is('generate-questions') || request()->is('question-wizard/preview/*') || request()->is('exams/attempt/students/*') || request()->is('exams/questions/add/*') || request()->is('exams/students/add/*') || request()->is('generate-questions') || request()->is('generate-questions-edit/*')) ? 'active': ''  }}">
            <a href="{{ route('question-wizard') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.question-wizard')}}</span>
            </a>
        </li>
        @endif

        @if(in_array('my_classes_read', $permissions))
        <li class="{{ (request()->is('my-class') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*')) ? 'active': '' }}">
            <a href="{{ route('my-class') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.my_classes')}}</span>
            </a>
        </li>
        @endif

        @if(in_array('assign_credit_points_read', $permissions))
        <li class="{{ (request()->is('assign-credit-points') ) ? 'active': '' }}">
            <a href="{{ route('assign-credit-points')}}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.assign_credit_points')}}</span>
            </a>
        </li>
        @endif

        @if (in_array('peer_group_read', $permissions))
        <li class="{{ (request()->is('peer-group') || request()->is('peer-group/create') || request()->is('peer-group/*/edit')) ? 'active': '' }}">
            <a href="{{ route('peer-group.index') }}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.peer_groups')}}</span>
            </a>
        </li>
        @endif

        @if(in_array('self_learning_read', $permissions))
            <li class="{{ (request()->is('myteaching/selflearning-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                <a href="{{ route('myteaching/selflearning-exercise') }}"> 
                    <span class="fa fa-home"></span>
                    <span class="text">{{__('languages.sidebar.self_learning')}}</span>
                </a>
            </li>
        @endif

        <li class="{{ (request()->is('myteaching/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('myteaching.selflearning-tests') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.testing_zone')}}</span>
            </a>
        </li>

        @if(in_array('assignment_or_test_read', $permissions))
        <li class="{{ request()->is('myteaching/assignment-exercise') ? 'active': '' }}">
            <a href="{{ route('myteaching/assignment-exercise') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.exercises')}}</span>
            </a>
        </li>
        @endif

        {{-- @if(in_array('test_read', $permissions)) --}}
        <li class="{{ (request()->is('myteaching/assignment-tests')) ? 'active': '' }}">
            <a href="{{ route('myteaching.assignment-tests') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.tests')}}</span>
            </a>
        </li>
        {{-- @endif --}}

        @if(in_array('progress_report_read', $permissions))
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('teacher/progress-report/learning-objective') || request()->is('teacher/progress-report/learning-units')) ? 'collapsed': '' }}" href="#teacher_report_menu" data-toggle="collapse" data-target="#teacher_report_menu">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.progress_report')}}</span>
            </a>
            <div class="collapse {{ (request()->is('teacher/progress-report/learning-objective') || request()->is('teacher/progress-report/learning-units')) ? 'show': '' }}" id="teacher_report_menu" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('progress_report_read', $permissions))
                    <li class="nav-item {{ (request()->is('teacher/progress-report/learning-objective')) ? 'active': '' }}">
                        <a href="{{ route('teacher.progress-report.learning-objective') }}">
                            <span class="fa fa-tags"></span>
                            <span class="text">{{__('languages.learning_objectives')}}</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ (request()->is('teacher/progress-report/learning-units')) ? 'active': '' }}">
                        <a href="{{ route('teacher.progress-report.learning-units') }}">
                            <span class="fa fa-tags"></span>
                            <span class="text">{{__('languages.learning_units')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
            
        </li>
        @endif

        {{-- @if(in_array('documents_read', $permissions)) 
        <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
            <a href="{{ route('myteaching.document-list') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.teaching_content')}}</span>
            </a>
        </li>
        @endif  --}}

        {{-- <li class="nav-item">
            @if(in_array('my_teaching_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('exams') || request()->is('myteaching/self-learning') || request()->is('myteaching/assignment-tests') || request()->is('myteaching/selflearning-tests') || request()->is('myteaching/selflearning-exercise') || request()->is('myteaching/assignment-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('myteaching/progress-report')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.sidebar.my_teaching')}}</span>
            </a>
            <div class="collapse {{ (request()->is('exams') || request()->is('myteaching/self-learning') || request()->is('myteaching/assignment-tests') || request()->is('myteaching/selflearning-tests') || request()->is('myteaching/selflearning-exercise') || request()->is('myteaching/assignment-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('myteaching/progress-report')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    {{-- @if(in_array('self_learning_read', $permissions))
                    <li class="{{ (request()->is('myteaching/self-learning') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                        <a href="{{ route('myteaching.self-learning') }}"> 
                            <span class="fa fa-home"></span>
                            <span class="text">{{__('languages.self_learning')}}</span>
                        </a>
                    </li>
                    @endif --}}
                    
                    {{-- @if(in_array('self_learning_read', $permissions))
                    <li class="{{ (request()->is('myteaching.selflearning-tests') || request()->is('myteaching/selflearning-tests') || request()->is('myteaching/selflearning-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                        <a href="{{ route('myteaching.selflearning-tests') }}"> 
                            <span class="fa fa-home"></span>
                            <span class="text">{{__('languages.sidebar.self_learning')}}</span>
                        </a>
                    </li>
                    @endif 

                    @if(in_array('assignment_or_test_read', $permissions))
                    <li class="{{ (request()->is('myteaching/assignment-tests') || request()->is('myteaching/assignment-exercise')) ? 'active': '' }}">
                        <a href="{{ route('myteaching.assignment-tests') }}">
                            <span class="fa fa-book"></span>
                            <span class="text">{{__('languages.exercises')}} / {{__('languages.tests')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('progress_report_read', $permissions))
                    <li class="{{ (request()->is('myteaching/progress-report')) ? 'active': '' }}">
                        <a class="nav-link" href="{{ route('myteaching.progress-report') }}">
                            <span class="fa fa-sliders-h">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                            </span>
                            <span class="text">{{__('languages.sidebar.progress_report')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('documents_read', $permissions)) 
                    <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
                        <a href="{{ route('myteaching.document-list') }}">
                            <span class="fa fa-book"></span>
                            <span class="text">{{__('languages.teaching_content')}}</span>
                        </a>
                    </li>
                    @endif 
                </ul>
            </div>
            @endif
        </li>  --}}
        
        {{-- Intelligrent Tutor Start --}}
        @if(in_array('intelligent_tutor_read',$permissions))
        <li class="{{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
            <a href="{{ route('intelligent-tutor.index') }}">
                <span class="fa fa-file"></span>
                <span class="text">{{__('languages.intelligent_tutor')}}</span>
            </a>
        </li>
        @endif
        {{-- Intelligrent Tutor End --}}

        @if(in_array('my_subjects_read', $permissions))
        <li class="{{ (request()->is('my-subject')) ? 'active': '' }}">
            <a href="{{ route('my-subject') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.my_subjects')}}</span>
            </a>
        </li>
        @endif
        
        {{-- @if (in_array('reports_read', $permissions))
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/skill-weekness')) ? '' : 'collapsed' }}" href="#reports" data-toggle="collapse" data-target="#reports">
                <span class="fa"><i class="fa fa-table"></i></span>
                <span class="text">{{__('languages.sidebar.reports')}}</span>
            </a>
            <div class="collapse {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/skill-weekness')) ? 'show' : '' }}" id="reports" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="nav-item {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('report.class-test-reports.correct-incorrect-answer')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.class_performance')}}</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item {{ (request()->is('report/skill-weekness')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('report.skill-weekness')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.skill_weakness_report')}}</span>
                        </a>
                    </li> -->
                </ul>
            </div>
        </li>
        @endif --}}

        <li>
            <a href="javascript:void(0);" id="logout">
                <span class="fa fa-sign-out"></span>
                <span class="text">{{__('languages.sidebar.logout')}}</span>
            </a>
        </li>
    </ul>
</nav>
@endif
<!-- End Teacher Sidebar Menus -->


<!-- Start Student Sidebar Menus -->
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
        <li class="{{ (request()->is('student/dashboard')) ? 'active': '' }}">
            <a href="{{ route('student.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>

        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('profile') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'collapsed': '' }}" href="#myaccount" data-toggle="collapse" data-target="#myaccount">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('profile') || request()->is('change-password')) || request()->is('student/leaderboard') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') || request()->is('student/progress-report/learning-objective') || request()->is('student/progress-report/learning-units') ? 'show': '' }}" id="myaccount" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('profile_management_read', $permissions))
                    <li class="nav-item {{ (request()->is('profile')) ||  request()->is('credit-point-history/*') || request()->is('student-profile/*') || request()->is('student/progress-report/learning-objective') || request()->is('student/progress-report/learning-units')? 'active': '' }}">
                        {{-- <a class="nav-link" href="{{route('profile.index')}}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.sidebar.profile')}}</span>
                        </a> --}}
                        {{-- <a class="nav-link" href="{{route('student.student-profiles',auth::user()->id)}}">
                            <span class="fa fa-user"></span>
                            <span class="text">{{__('languages.sidebar.profile')}}</span>
                        </a> --}}
                        <a class="nav-link" href="{{route('student-profiles',auth::user()->id)}}">
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
        
        <!-- @if(in_array('profile_management_read', $permissions))
        <li class="{{ (request()->is('profile')) ? 'active': '' }}">
            <a href="{{ route('profile.index') }}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.profile')}}</span>
            </a>
        </li>
        @endif -->

        @if (in_array('peer_group_read', $permissions))
        <li class="{{ (request()->is('my-peer-group')) ? 'active': '' }}">
            <a href="{{ route('my-peer-group') }}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.peer_groups')}}</span>
            </a>
        </li>
        @endif
        
        <li class="{{ (request()->is('student/self-learning/exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningExercise) && $isSelfLearningExercise == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-exercise')) ? 'active': '' }}">
            <a href="{{ route('student.self-learning-exercise') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.self_learning')}}</span>
            </a>
        </li>

        <li class="{{ (request()->is('student/testing-zone') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') || request()->is('student/create/self-learning-test')) ? 'active': '' }}">
            <a href="{{ route('student.testing-zone') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.testing_zone')}}</span>
            </a>
        </li>

        {{-- Excercise --}}
        {{-- @if (in_array('attempt_exam_read', $permissions))
        <li class="nav-item {{ (request()->is('student/exam') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('student/attempt/test-exercise/*')) ? 'active': '' }}">
            <a class="nav-link" href="{{ route('getStudentExamList') }}">
                <span class="fa fa-sliders-h">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                </span>
                <span class="text">{{__('languages.test_text')}} & {{__('languages.exercise')}}</span>
            </a>
        </li>
        @endif --}}


         {{-- @if (in_array('attempt_exam_read', $permissions)) --}}
         <li class="nav-item {{ (request()->is('student/exercise/exam')) ? 'active': '' }}">
            <a class="nav-link" href="{{ route('getStudentExerciseExamList') }}">
                <span class="fa fa-sliders-h">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                </span>
                <span class="text"> {{__('languages.exercise')}}</span>
            </a>
        </li>
        {{-- @endif --}}

        {{-- @if (in_array('attempt_exam_read', $permissions)) --}}
        <li class="nav-item {{ (request()->is('student/test/exam')) ? 'active': '' }}">
            <a class="nav-link" href="{{ route('getStudentTestExamList') }}">
                <span class="fa fa-sliders-h">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                </span>
                <span class="text">{{__('languages.test_text')}}</span>
            </a>
        </li>
        {{-- @endif --}}

        {{-- Tests --}}
        @if (in_array('attempt_exam_read', $permissions))
        <!-- <li class="nav-item {{ (request()->is('student/exam') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*')) ? 'active': '' }}">
            <a class="nav-link" href="{{ route('getStudentExamList') }}">
                <span class="fa fa-sliders-h">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                </span>
                <span class="text">{{__('languages.tests')}}</span>
            </a>
        </li> -->
        @endif
        
        {{-- Progress --}}
        {{-- @if(in_array('progress_report_read', $permissions))
        <li class="nav-item {{ (request()->is('student/progress-report/learning-objective')) ? 'active': '' }}">
            <a class="nav-link" href="{{ route('student.progress-report.learning-objective') }}">
                <span class="fa fa-sliders-h">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                </span>
                <span class="text">{{__('languages.progress')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- Learning Content --}}
        {{-- @if(in_array('documents_read', $permissions)) 
            <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
                <a href="{{ route('myteaching.document-list') }}">
                    <span class="fa fa-book"></span>
                    <span class="text">{{__('languages.learning_content')}}</span>
                </a>
            </li>
        @endif  --}}

        {{-- <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('student/exam') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('student/self-learning') || request()->is('mystudy/progress-report')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.sidebar.my_study')}}</span>
            </a>
            <div class="collapse {{ (request()->is('student/exam') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('mystudy/progress-report') || request()->is('student/self-learning')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="{{ (request()->is('student/self-learning') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                        <a href="{{ route('student.self-learning') }}">
                            <span class="fa fa-home"></span>
                            <span class="text">{{__('languages.sidebar.self_learning')}}</span>
                        </a>
                    </li>
                    @if (in_array('attempt_exam_read', $permissions))
                    <li class="nav-item {{ (request()->is('student/exam') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*')) ? 'active': '' }}">
                        <a class="nav-link" href="{{ route('getStudentExamList') }}">
                            <span class="fa fa-sliders-h">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                            </span>
                            <span class="text">{{__('languages.exercises')}} / {{__('languages.tests')}}</span>
                        </a>
                    </li>
                    @endif
                   
                    @if(in_array('progress_report_read', $permissions))
                    <li class="nav-item {{ (request()->is('mystudy/progress-report')) ? 'active': '' }}">
                        <a class="nav-link" href="{{ route('mystudy.progress-report') }}">
                            <span class="fa fa-sliders-h">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                            </span>
                            <span class="text">{{__('languages.progress')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('documents_read', $permissions)) 
                    <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
                        <a href="{{ route('myteaching.document-list') }}">
                            <span class="fa fa-book"></span>
                            <span class="text">{{__('languages.learning_content')}}</span>
                        </a>
                    </li>
                    @endif 
                </ul>
            </div>
        </li> --}}

        <!-- My Documents in Comment -->
        <!-- <li class="{{ (request()->is('student/documents')) ? 'active': '' }}">
            <a href="{{ route('student.documents') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.documents')}}</span>
            </a>
        </li> -->

        {{-- @if(in_array('my_calendar_read', $permissions))
        <li class="{{ (request()->is('student/mycalendar')) ? 'active': '' }}">
            <a href="{{ route('student.mycalendar') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.my_calendar')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- @if(in_array('my_classes_read', $permissions))
        <li class="{{ (request()->is('student/myclass')) ? 'active': '' }}">
            <a href="{{ route('student.myclass') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.my_classmates')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- @if(in_array('my_subjects_read', $permissions))
        <li class="{{ (request()->is('student/mysubjects')) ? 'active': '' }}">
            <a href="{{ route('student.mysubjects') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.my_subjects')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- @if(in_array('my_teachers_read', $permissions))
        <li class="{{ (request()->is('student/myteachers')) ? 'active': '' }}">
            <a href="{{ route('student.myteachers') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.my_teachers')}}</span>
            </a>
        </li>
        @endif --}}

        @if (in_array('my_learning_read', $permissions))
        <li>
            <a href="{{ route('my-desk.index') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.my_learning')}}</span>
            </a>
        </li>
        @endif

         <!-- Upload Documents -->
         @if(in_array('intelligent_tutor_read',$permissions))
         <li class="{{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
             <a href="{{ route('intelligent-tutor.index') }}">
                 <span class="fa fa-file"></span>
                 <span class="text">{{__('languages.intelligent_tutor')}}</span>
             </a>
         </li>
         @endif

       {{-- <li>
        <a href="{{ route('report.class-test-reports.student-correct-incorrect-answer') }}">
            <span class="fa fa-table"></span>
            <span class="text">{{__('languages.sidebar.reports')}}</span>
        </a>
        </li> --}}
        <li>
            <a href="javascript:void(0);" id="logout">
                <span class="fa fa-sign-out"></span>
                <span class="text">{{__('languages.sidebar.logout')}}</span>
            </a>
        </li>
    </ul>
</nav>
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
<!-- Start School Sidebar Menus -->
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
        <li class="{{ (request()->is('schools/dashboard')) ? 'active': '' }}">
            <a href="{{ route('schools.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>
         {{-- @if (in_array('ordering_learning_units_read', $permissions)) --}}
        <li class="{{ (request()->is('learning-unit-ordering')) ? 'active': '' }}">
            <a href="{{ route('learning-unit-ordering') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.ordering_learning_units')}}</span>
            </a>
        </li>
        {{-- @endif --}}

        {{-- @if (in_array('ordering_learning_objectives_read', $permissions)) --}}
        <li class="{{ (request()->is('learning-objectives-ordering')) ? 'active': '' }}">
            <a href="{{ route('learning-objectives-ordering') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.ordering_learning_objectives')}}</span>
            </a>
        </li>
        {{-- @endif --}}
        
        {{-- @if (in_array('exam_management_read', $permissions)) --}}
        {{-- @endif --}}
        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('schoolprofile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('schoolprofile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('profile_management_read', $permissions))
                    <li class="nav-item {{ (request()->is('schoolprofile')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('schoolprofile')}}">
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

        @if (in_array('exam_management_read', $permissions))
        <li class="{{ (request()->is('question-wizard') || request()->is('exams') || request()->is('exams/create') || request()->is('exams/*/edit') || request()->is('exams/attempt/students/*') || request()->is('exams/questions/add/*') || request()->is('exams/students/add/*') || request()->is('generate-questions') || request()->is('generate-questions-edit/*') || request()->is('question-wizard/preview/*')) ? 'active': ''  }}">
            <a href="{{ route('question-wizard') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.question-wizard')}}</span>
            </a>
        </li>
        @endif

         <!-- Upload Documents -->
         @if(in_array('upload_documents_read',$permissions))
        <li class="{{ (request()->is('upload-documents')) ? 'active': '' }}">
            <a href="{{ route('upload-documents.index') }}">
                <span class="fa fa-file"></span>
                <span class="text"> {{__('languages.sidebar.upload_documents')}} </span>
            </a>
        </li>
        @endif

        @if (in_array('peer_group_read', $permissions))
        <li class="{{ (request()->is('auto-peer-group') || request()->is('peer-group') || request()->is('peer-group/create') || request()->is('peer-group/*/edit')) ? 'active': '' }}">
            <a href="{{ route('peer-group.index') }}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.peer_groups')}}</span>
            </a>
        </li>
        @endif

        <!-- Student Management -->
        @if (in_array('student_management_read', $permissions))
        <li class="{{ (request()->is('Student') || request()->is('Student/create') || request()->is('Student/*/edit') || request()->is('school/class/assign-students/*') || request()->is('school/class/importStudent') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*')) ? 'active': '' }}">
            <a href="{{route('Student.index')}}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.student_management')}}</span>
            </a>
        </li>
        @endif

        @if (in_array('sub_admin_management_read', $permissions))
        <li class="{{ (request()->is('sub-admin') || request()->is('sub-admin/create') || request()->is('sub-admin/*/edit')) ? 'active': '' }}">
            <a href="{{ route('sub-admin.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.sub_admin')}}</span>
            </a>
        </li>
        @endif 
        
        @if (in_array('teacher_management_read', $permissions))
        <li class="{{ (request()->is('teacher') || request()->is('teacher/create') || request()->is('teacher/*/edit')) ? 'active': '' }}">
            <a href="{{ route('teacher.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.teacher_management')}}</span>
            </a>
        </li>
        @endif

        {{-- @if (in_array('principal_management_read', $permissions)) 
        <li class="{{ (request()->is('principal') || request()->is('principal/create') || request()->is('principal/*/edit')) ? 'active': '' }}">
            <a href="{{ route('principal.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.principal_management')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- @if (in_array('subject_management_read', $permissions))
        <li class="{{ (request()->is('subject') || request()->is('subject/create') || request()->is('subject/*/edit')) ? 'active': '' }}">
            <a href="{{ route('subject.index') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.subject_management')}}</span>
            </a>
        </li>
        @endif --}}

        <li class="{{ (request()->is('school/selflearning-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('school.selflearning-exercise') }}"> 
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.self_learning')}}</span>
            </a>
        </li>

        <li class="{{ (request()->is('school/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('school.selflearning-tests') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.testing_zone')}}</span>
            </a>
        </li>

        <li class="{{ (request()->is('school/assignment-exercise')) ? 'active': '' }}">
            <a href="{{ route('school.assignment-exercise') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.exercises')}}</span>
            </a>
        </li>
        
        <li class="{{ (request()->is('school/assignment-tests')) ? 'active': '' }}">
            <a href="{{ route('school.assignment-tests') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.tests')}}</span>
            </a>
        </li>

        @if (in_array('grade_management_read', $permissions))
        <li class="{{ (request()->is('class') || request()->is('class/create') || request()->is('class/*/edit')) ? 'active': '' }}">
            <a href="{{ route('class.index') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.grade_class_management')}}</span>
            </a>
        </li>
        @endif

        @if (in_array('teacher_class_and_subject_assign_read', $permissions))
        <li class="{{ (request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/create') || request()->is('teacher-class-subject-assign/*/edit')) ? 'active': '' }}">
            <a href="{{ route('teacher-class-subject-assign.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.teacher_class_assignment')}}</span>
            </a>
        </li>
        @endif

        {{-- Intelligrent Tutor Start --}}
        @if(in_array('intelligent_tutor_read',$permissions))
        <li class="{{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
            <a href="{{ route('intelligent-tutor.index') }}">
                <span class="fa fa-file"></span>
                <span class="text">{{__('languages.intelligent_tutor')}}</span>
            </a>
        </li>
        @endif
        {{-- Intelligrent Tutor End --}}

        @if (in_array('reports_read', $permissions))
        <li class="nav-item">
            <a class="nav-link collapsed text-truncate" href="#submenu1" data-toggle="collapse" data-target="#submenu1">
                <span class="fa"><i class="fa fa-table"></i></span>
                <span class="text">{{__('languages.sidebar.reports')}}</span>
            </a>
            <div class="collapse {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/exams/student-test-performance')) ? 'show': '' }}" id="submenu1" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="nav-item {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/exams/student-test-performance')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('report.class-test-reports.correct-incorrect-answer')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.class_performance')}}</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="{{route('report.skill-weekness')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('Skill Weakness Report')}}</span>
                        </a>
                    </li> -->
                </ul>
            </div>
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
<!-- End School Sidebar Menus -->

@if(Auth::user()->role_id == 8)
<!-- Start Sub Admin Sidebar Menus -->
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
        <li class="{{ (request()->is('sub-admin/dashboard')) ? 'active': '' }}">
            <a href="{{ route('sub_admin.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>
        <li class="nav-item">
            @if (in_array('my_account_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('sub-admin/profile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.my_account')}}</span>
            </a>
            <div class="collapse {{ (request()->is('sub-admin/profile') || request()->is('change-password') || request()->is('student/leaderboard')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    {{-- @if(in_array('profile_management_read', $permissions))
                    <li class="nav-item {{ (request()->is('sub-admin/profile')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('sub_admin.profile')}}">
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
                    @endif --}}
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

        @if (in_array('exam_management_read', $permissions))
        <li class="{{ (request()->is('question-wizard') || request()->is('exams') || request()->is('exams/create') || request()->is('exams/*/edit') || request()->is('exams/attempt/students/*') || request()->is('exams/questions/add/*') || request()->is('exams/students/add/*') || request()->is('generate-questions') || request()->is('generate-questions-edit/*') || request()->is('question-wizard/preview/*')) ? 'active': ''  }}">
            <a href="{{ route('question-wizard') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.question-wizard')}}</span>
            </a>
        </li>
        @endif

         <!-- Upload Documents -->
         @if(in_array('upload_documents_read',$permissions))
        <li class="{{ (request()->is('upload-documents')) ? 'active': '' }}">
            <a href="{{ route('upload-documents.index') }}">
                <span class="fa fa-file"></span>
                <span class="text"> {{__('languages.sidebar.upload_documents')}} </span>
            </a>
        </li>
        @endif

        @if (in_array('peer_group_read', $permissions))
        <li class="{{ (request()->is('auto-peer-group') || request()->is('peer-group') || request()->is('peer-group/create') || request()->is('peer-group/*/edit')) ? 'active': '' }}">
            <a href="{{ route('peer-group.index') }}">
                <span class="fa fa-user"></span>
                <span class="text">{{__('languages.sidebar.peer_groups')}}</span>
            </a>
        </li>
        @endif

        <!-- Student Management -->
        @if (in_array('student_management_read', $permissions))
        <li class="{{ (request()->is('Student') || request()->is('Student/create') || request()->is('Student/*/edit') || request()->is('school/class/assign-students/*') || request()->is('school/class/importStudent') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*')) ? 'active': '' }}">
            <a href="{{route('Student.index')}}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.student_management')}}</span>
            </a>
        </li>
        @endif

        {{-- @if (in_array('sub_admin_management_read', $permissions))
        <li class="{{ (request()->is('sub-admin') || request()->is('sub-admin/create') || request()->is('sub-admin/*/edit')) ? 'active': '' }}">
            <a href="{{ route('sub-admin.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.sub_admin')}}</span>
            </a>
        </li>
        @endif  --}}
        
        @if (in_array('teacher_management_read', $permissions))
        <li class="{{ (request()->is('teacher') || request()->is('teacher/create') || request()->is('teacher/*/edit')) ? 'active': '' }}">
            <a href="{{ route('teacher.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.teacher_management')}}</span>
            </a>
        </li>
        @endif

        {{-- @if (in_array('principal_management_read', $permissions)) 
        <li class="{{ (request()->is('principal') || request()->is('principal/create') || request()->is('principal/*/edit')) ? 'active': '' }}">
            <a href="{{ route('principal.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.principal_management')}}</span>
            </a>
        </li>
        @endif --}}

        {{-- @if (in_array('subject_management_read', $permissions))
        <li class="{{ (request()->is('subject') || request()->is('subject/create') || request()->is('subject/*/edit')) ? 'active': '' }}">
            <a href="{{ route('subject.index') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.subject_management')}}</span>
            </a>
        </li>
        @endif --}}

        <li class="{{ (request()->is('school/selflearning-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('school.selflearning-exercise') }}"> 
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.self_learning')}}</span>
            </a>
        </li>

        <li class="{{ (request()->is('school/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('school.selflearning-tests') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.testing_zone')}}</span>
            </a>
        </li>

        <li class="{{ (request()->is('school/assignment-exercise')) ? 'active': '' }}">
            <a href="{{ route('school.assignment-exercise') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.exercises')}}</span>
            </a>
        </li>
        
        <li class="{{ (request()->is('school/assignment-tests')) ? 'active': '' }}">
            <a href="{{ route('school.assignment-tests') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.tests')}}</span>
            </a>
        </li>

        @if (in_array('grade_management_read', $permissions))
        <li class="{{ (request()->is('class') || request()->is('class/create') || request()->is('class/*/edit')) ? 'active': '' }}">
            <a href="{{ route('class.index') }}">
                <span class="fa fa-list"></span>
                <span class="text">{{__('languages.sidebar.grade_class_management')}}</span>
            </a>
        </li>
        @endif

        @if (in_array('teacher_class_and_subject_assign_read', $permissions))
        <li class="{{ (request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/create') || request()->is('teacher-class-subject-assign/*/edit')) ? 'active': '' }}">
            <a href="{{ route('teacher-class-subject-assign.index') }}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.teacher_class_assignment')}}</span>
            </a>
        </li>
        @endif

        {{-- Intelligrent Tutor Start --}}
        @if(in_array('intelligent_tutor_read',$permissions))
        <li class="{{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
            <a href="{{ route('intelligent-tutor.index') }}">
                <span class="fa fa-file"></span>
                <span class="text">{{__('languages.intelligent_tutor')}}</span>
            </a>
        </li>
        @endif
        {{-- Intelligrent Tutor End --}}

        @if (in_array('reports_read', $permissions))
        <li class="nav-item">
            <a class="nav-link collapsed text-truncate" href="#submenu1" data-toggle="collapse" data-target="#submenu1">
                <span class="fa"><i class="fa fa-table"></i></span>
                <span class="text">{{__('languages.sidebar.reports')}}</span>
            </a>
            <div class="collapse {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/exams/student-test-performance')) ? 'show': '' }}" id="submenu1" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="nav-item {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/exams/student-test-performance')) ? 'active': '' }}">
                        <a class="nav-link" href="{{route('report.class-test-reports.correct-incorrect-answer')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.class_performance')}}</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="{{route('report.skill-weekness')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('Skill Weakness Report')}}</span>
                        </a>
                    </li> -->
                </ul>
            </div>
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
<!-- End Sub Admin Sidebar Menus -->

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
        <li class="{{ (request()->is('schools/dashboard')) ? 'active': '' }}">
            <a href="{{ route('schools.dashboard') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.sidebar.dashboard')}}</span>
            </a>
        </li>

        @if (in_array('leaderboard_read', $permissions))
            <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': '' }}">
                <a class="nav-link" href="{{route('student/leaderboard')}}">
                    <span class="fa fa-user"></span>
                    <span class="text">{{__('languages.sidebar.leaderboard')}}</span>
                </a>
            </li>
        @endif

        @if (in_array('exam_management_read', $permissions))
        <li class="{{ (request()->is('question-wizard') || request()->is('exams/attempt/students/*') || request()->is('generate-questions') || request()->is('generate-questions-edit/*')) ? 'active': ''  }}">
            <a href="{{ route('question-wizard') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.sidebar.question-wizard')}}</span>
            </a>
        </li>
        @endif

        <!-- Student Management -->
        @if (in_array('student_management_read', $permissions))
        <li class="{{ (request()->is('Student') || request()->is('Student/create') || request()->is('Student/*/edit') || request()->is('school/class/assign-students/*') || request()->is('school/class/importStudent') ||  request()->is('credit-point-history/*') || request()->is('student-profile/*')) ? 'active': '' }}">
            <a href="{{route('Student.index')}}">
                <span class="fa fa-users"></span>
                <span class="text">{{__('languages.sidebar.student_management')}}</span>
            </a>
        </li>
        @endif

        {{-- <li class="nav-item">
            @if(in_array('my_teaching_read', $permissions))
            <a class="nav-link text-truncate {{ (request()->is('exams') || request()->is('principal/myteaching/self-learning') || request()->is('myteaching/assignment-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('myteaching/progress-report')) ? 'collapsed': '' }}" href="#rolepermission" data-toggle="collapse" data-target="#rolepermission">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.sidebar.my_teaching')}}</span>
            </a>
            <div class="collapse {{ (request()->is('exams') || request()->is('principal/myteaching/self-learning') || request()->is('myteaching/assignment-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == false) ? request()->is('exams/result/*/*') : '') || request()->is('exam-documents/*') || request()->is('myteaching/progress-report')) ? 'show': '' }}" id="rolepermission" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('self_learning_read', $permissions))
                    <li class="{{ (request()->is('principal/myteaching/self-learning') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                        <a href="{{ route('principal.myteaching.self-learning') }}"> 
                            <span class="fa fa-home"></span>
                            <span class="text">{{__('languages.self_learning')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('assignment_or_test_read', $permissions))
                    <li class="{{ (request()->is('principal/myteaching/assignment-tests')) ? 'active': '' }}">
                        <a href="{{ route('principal.myteaching.assignment-tests') }}">
                            <span class="fa fa-book"></span>
                            <span class="text">{{__('languages.sidebar.assignments_testings')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('progress_report_read', $permissions))
                    <li class="{{ (request()->is('principla/myteaching/progress-report')) ? 'active': '' }}">
                        <a class="nav-link" href="{{ route('principal.myteaching.progress-report') }}">
                            <span class="fa fa-sliders-h">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                            </span>
                            <span class="text">{{__('languages.sidebar.progress_report')}}</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(in_array('documents_read', $permissions)) 
                    <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
                        <a href="{{ route('myteaching.document-list') }}">
                            <span class="fa fa-book"></span>
                            <span class="text">{{__('languages.sidebar.document')}}</span>
                        </a>
                    </li>
                     @endif 
                </ul>
            </div>
            @endif
        </li> --}}

        @if(in_array('self_learning_read', $permissions))
            <li class="{{ (request()->is('principal/selflearning-exercise') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
                <a href="{{ route('principal.selflearning-exercise') }}"> 
                    <span class="fa fa-home"></span>
                    <span class="text">{{__('languages.self_learning')}}</span>
                </a>
            </li>
        @endif
        
        <li class="{{ (request()->is('principal/selflearning-tests') || ((isset($isSelfLearningExam) && $isSelfLearningExam == true && isset($isSelfLearningTestingZone) && $isSelfLearningTestingZone == true) ? request()->is('exams/result/*/*') : '') ) ? 'active': '' }}">
            <a href="{{ route('principal.selflearning-tests') }}">
                <span class="fa fa-home"></span>
                <span class="text">{{__('languages.testing_zone')}}</span>
            </a>
        </li>

        @if(in_array('assignment_or_test_read', $permissions))
            <li class="{{ (request()->is('principal/assignment-exercise')) ? 'active': '' }}">
                <a href="{{ route('principal.assignment-exercise') }}">
                    <span class="fa fa-book"></span>
                    <span class="text">{{__('languages.exercises')}}</span>
                </a>
            </li>
        @endif

        @if(in_array('assignment_or_test_read', $permissions))
        <li class="{{ (request()->is('principal/assignment-tests')) ? 'active': '' }}">
            <a href="{{ route('principal.assignment-tests') }}">
                <span class="fa fa-book"></span>
                <span class="text">{{__('languages.tests')}}</span>
            </a>
        </li>
        @endif


        {{-- @if(in_array('assignment_or_test_read', $permissions)) --}}
            {{-- <li class="{{ (request()->is('principal/myteaching/assignment-tests')) ? 'active': '' }}">
                <a href="{{ route('principal.myteaching.assignment-tests') }}">
                    <span class="fa fa-book"></span>
                    <span class="text">{{__('languages.tests')}}</span>
                </a>
            </li> --}}
        {{-- @endif --}}

        @if(in_array('progress_report_read', $permissions))
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('principal/progress-report/learning-objective')||request()->is('principal/progress-report/learning-units')) ? 'collapsed': '' }}" href="#principal_report_menu" data-toggle="collapse" data-target="#principal_report_menu">
                <span class="fa"><i class="fa fa-cogs"></i></span>
                <span class="text">{{__('languages.progress_report')}}</span>
            </a>
            <div class="collapse {{ (request()->is('principal/progress-report/learning-objective')||request()->is('principal/progress-report/learning-units')) ? 'show': '' }}" id="principal_report_menu" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    @if(in_array('progress_report_read', $permissions))
                    <li class="nav-item {{ (request()->is('principal/progress-report/learning-objective')) ? 'active': '' }}">
                        <a href="{{ route('principal.progress-report.learning-objective') }}">
                            <span class="fa fa-tags"></span>
                            <span class="text">{{__('languages.learning_objectives')}}</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ (request()->is('principal/progress-report/learning-units')) ? 'active': '' }}">
                        <a href="{{ route('principal.progress-report.learning-units') }}">
                            <span class="fa fa-tags"></span>
                            <span class="text">{{__('languages.learning_units')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif
        
        {{-- @if(in_array('documents_read', $permissions)) 
            <li class="{{ (request()->is('myteaching/document-list')) ? 'active': '' }}">
                <a href="{{ route('myteaching.document-list') }}">
                    <span class="fa fa-book"></span>
                    <span class="text">{{__('languages.intelligent_tutor')}}</span>
                </a>
            </li>
        @endif  --}}

        @if(in_array('intelligent_tutor_read',$permissions))
        <li class="{{ (request()->is('intelligent-tutor')) ? 'active': ''}}">
            <a href="{{ route('intelligent-tutor.index') }}">
                <span class="fa fa-file"></span>
                <span class="text">{{__('languages.intelligent_tutor')}}</span>
            </a>
        </li>
        @endif

        @if (in_array('reports_read', $permissions))
        <li class="nav-item">
            <a class="nav-link text-truncate {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/skill-weekness')) ? '' : 'collapsed' }}" href="#reports" data-toggle="collapse" data-target="#reports">
                <span class="fa"><i class="fa fa-table"></i></span>
                <span class="text">{{__('languages.sidebar.reports')}}</span>
            </a>
            <div class="collapse {{ (request()->is('report/class-test-reports/correct-incorrect-answer') || request()->is('report/skill-weekness')) ? 'show' : '' }}" id="reports" aria-expanded="false">
                <ul class="flex-column pl-2 nav">
                    <li class="nav-item {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('report.class-test-reports.correct-incorrect-answer')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.class_performance')}}</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item {{ (request()->is('report/skill-weekness')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('report.skill-weekness')}}">
                            <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                            <span class="text">{{__('languages.sidebar.skill_weakness_report')}}</span>
                        </a>
                    </li> -->
                </ul>
            </div>
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
<!-- End Principal Sidebar Menus -->

