<!-- Start School Sidebar Menus -->
@if(Auth::user()->role_id == 5)
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
                        <!-- @if (in_array('leaderboard_read', $permissions))
                        <li class="nav-item {{ (request()->is('student/leaderboard')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('student/leaderboard')}}">
                                <span class="fa fa-user"></span>
                                <span class="text">{{__('languages.sidebar.leaderboard')}}</span>
                            </a>
                        </li>
                        @endif -->
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
                    <span class="text">{{__('languages.exercise')}}</span>
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