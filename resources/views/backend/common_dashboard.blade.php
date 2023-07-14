@extends('backend.layouts.app')
    @section('content')
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
    {{-- <style>
        .dashboard-card b:hover{
            color: <?php echo $RoleBasedColor['headerColor'];?> !important;
        }
    </style> --}}
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            <div class="sm-right-detail-sec pl-5 pr-5">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="sec-title">
                                <h2 class="mb-4 main-title">{{__('languages.sidebar.dashboard')}}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="sec-title">
                                
                                <div class="row">
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.students')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card">
                                            <a href="{{(auth()->user()->role_id == 2) ? url('my-class') : route('Student.index')}}" >
                                                <b>{{ $UsersCount['student']}} </b>
                                            </a>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.teachers')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card">
                                            @if(Auth::user()->is_school_admin_privilege_access == "yes")
                                                <a href="{{url('school-users?Role=2&username=&email=&filter=filter')}}">
                                                    <b>{{ $UsersCount['teacher']}} </b>
                                                </a>
                                            @else   
                                                <b>{{ $UsersCount['teacher']}} </b>
                                            @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.co_ordinators')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card">
                                            @if(Auth::user()->is_school_admin_privilege_access == "yes")
                                                <a href="{{url('school-users?Role=9&username=&email=&filter=filter')}}"> 
                                                    <b>{{ $UsersCount['co-ordinator']}} </b>
                                                </a>
                                            @else
                                                <b>{{ $UsersCount['co-ordinator']}} </b>
                                            @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.panel_heads')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card">
                                            @if(Auth::user()->is_school_admin_privilege_access == "yes")
                                                <a href="{{url('school-users?Role=8&username=&email=&filter=filter')}}">
                                                    <b>{{ $UsersCount['panel_head']}} </b>
                                                </a>
                                            @else
                                            <b>{{ $UsersCount['panel_head']}} </b>
                                            @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.principals')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card">
                                            @if(Auth::user()->is_school_admin_privilege_access == "yes")
                                                <a href="{{url('school-users?Role=7&username=&email=&filter=filter')}}">
                                                    <b>{{ $UsersCount['principal']}} </b>
                                                </a>
                                            @else
                                                <b>{{ $UsersCount['principal']}} </b>
                                            @endif
                                        </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.self_learnings')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{route('myteaching/selflearning-exercise')}}"><b>{{ $ExamsCount['self_learning']}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.exercises')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{route('myteaching/assignment-exercise')}}"><b>{{ $ExamsCount['exercise']}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.tests')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{route('myteaching.assignment-tests')}}"><b>{{ $ExamsCount['test']}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.ai_based_assessments')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card" ><a href="{{route('myteaching.selflearning-tests')}}"><b>{{ $ExamsCount['ai_based_assessment']}} </b></a></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.forms')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{route('my-class')}}"><b>{{ $gradesCount}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.classes')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{route('my-class')}}"><b>{{ $classesCount}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.sidebar.peer_groups')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{url('peer-group?searchName=&status=1&group_type=peer_group&filter=filter')}}" ><b>{{ $peerGroupCount}} </b></a></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('languages.groups')}} </b></span>
                                        <hr/>
                                            <span class="d-flex justify-content-center align-items-center pb-3 dashboard-card"><a href="{{url('peer-group?searchName=&creator=&status=1&group_type=group&filter=filter')}}" ><b>{{ $groupCount}}</b></a> </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Latest Excercise--}}
                                @if(!empty($AssignmentExerciseList))
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-4">
                                        <h3>{{__('languages.latest')}} {{__('languages.exercise')}}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-4">
                                            <div class="card dashboard_table">
                                                <table class="styled-table">
                                                <thead>
                                                    <tr>
                                                        {{-- <th class="selec-opt">{{__('languages.publish_date_time')}}</th> --}}
                                                        <th>{{__('languages.report.start_date')}} & {{__('languages.time')}}</th>
                                                        {{-- <th>{{__('languages.report.end_date')}} & {{__('languages.time')}}</th> --}}
                                                        <th>{{__('languages.report.result_release_date')}}</th>
                                                        <th>{{__('languages.reference_number')}}</th>
                                                        <th>{{__('languages.title')}}</th>
                                                        <th>{{__('languages.class')}}/{{__('languages.group')}}</th>
                                                        {{-- <th>{{__('languages.students')}}</th> --}}
                                                        <th>{{__('languages.submission')}} {{__('languages.status')}}</th>
                                                        {{-- <th>{{__('languages.average_accuracy')}}</th> --}}
                                                        <th>{{__('languages.study_status')}}</th>
                                                        <th>{{__('languages.question_difficulties')}}</th>
                                                        <th>{{__('languages.action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="scroll-pane">
                                                    @if(!empty($AssignmentExerciseList))
                                                        @foreach($AssignmentExerciseList as $assignmentExcercise)
                                                        <tr>
                                                            {{-- <td>{{ date('d/m/Y H:i:s',strtotime($assignmentExcercise->exams->publish_date)) }}</td> --}}
                                                            <td>
                                                                @if(isset($assignmentExcercise->exams->ExamGradeClassConfigurations->start_date) && $assignmentExcercise->exams->ExamGradeClassConfigurations->start_date !="")
                                                                    @php
                                                                        $start_time='00:00:00';
                                                                    @endphp
                                                                    @if(isset($assignmentExcercise->exams->ExamGradeClassConfigurations->start_time) && $assignmentExcercise->exams->ExamGradeClassConfigurations->start_time !="")
                                                                        @php
                                                                            $start_time=$assignmentExcercise->exams->ExamGradeClassConfigurations->start_time;
                                                                        @endphp
                                                                    @endif
                        
                                                                    {{ date('d/m/Y H:i:s',strtotime($assignmentExcercise->exams->ExamGradeClassConfigurations->start_date.' '.$start_time)) }}
                                                                @else
                                                                    --
                                                                @endif
                                                            </td>
                                                            {{-- <td>
                                                                @if(isset($assignmentExcercise->exams->ExamGradeClassConfigurations->end_date) && $assignmentExcercise->exams->ExamGradeClassConfigurations->end_date !="")
                                                                    @php
                                                                        $end_time='00:00:00';
                                                                    @endphp
                                                                    @if(isset($assignmentExcercise->exams->ExamGradeClassConfigurations->end_time) && $assignmentExcercise->exams->ExamGradeClassConfigurations->end_time !="")
                                                                        @php
                                                                            $end_time=$assignmentExcercise->exams->ExamGradeClassConfigurations->end_time;
                                                                        @endphp
                                                                    @endif
                                                                    {{ date('d/m/Y H:i:s',strtotime($assignmentExcercise->exams->ExamGradeClassConfigurations->end_date.' '.$end_time)) }}
                                                                @else
                                                                    --
                                                                @endif
                                                            </td> --}}
                                                            <td>{{date('d/m/Y',strtotime($assignmentExcercise->exams->result_date))}}</td>
                                                            <td>{{$assignmentExcercise->exams->reference_no}}</td>
                                                            <td>{{$assignmentExcercise->exams->title}}</td>
                                                            <td>
                                                                @if(!empty($assignmentExcercise->peerGroup)) 
                                                                    {{ $assignmentExcercise->peerGroup->group_name }}
                                                                @else 
                                                                    {{ $assignmentExcercise->grade_with_class }}
                                                                @endif
                                                            </td>
                                                            {{-- <td>{{ $assignmentExcercise->no_of_students }}</td> --}}
                                                            @php
                                                                $progress = json_decode($assignmentExcercise->student_progress, true);
                                                                $accuracy = json_decode($assignmentExcercise->average_accuracy, true);
                                                            @endphp
                                                            <td>
                                                                <div class="progress student-progress-report" data-examid="{{$assignmentExcercise->exam_id}}"  data-studentids="{{$assignmentExcercise->student_ids}}">
                                                                    <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$progress['progress_tooltip']}}"style="width:{{$progress['progress_percentage']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$progress['progress_percentage']}}" aria-valuemin="0" aria-valuemax="100">{{$progress['progress_percentage']}}%</div>
                                                                </div>
                                                            </td>
                                                            {{-- <td>
                                                                <div class="progress">
                                                                        <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$accuracy['average_accuracy_tooltip']}}" style="width: {{$accuracy['average_accuracy']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$accuracy['average_accuracy']}}" aria-valuemin="0" aria-valuemax="100">{{$accuracy['average_accuracy']}}%</div>
                                                                </div>
                                                            </td> --}}
                                                            
                                                            @php 
                                                                $studyProgress = json_decode($assignmentExcercise->study_status);
                                                            @endphp
                                                            <td class="study-status-progressbar-td">
                                                                <div class="progress">
                                                                    @if($studyProgress->Struggling != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Struggling}}%" style="width:{{$studyProgress->Struggling}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('struggling_color')}};">{{$studyProgress->Struggling}}%</div>
                                                                    @endif
                                                                    @if($studyProgress->Beginning != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Beginning}}%" style="width:{{$studyProgress->Beginning}}%;background-color: {{App\Helpers\Helper::getGlobalConfiguration('beginning_color')}};">{{$studyProgress->Beginning}}%</div>
                                                                    @endif
                                                                    @if($studyProgress->Approaching != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Approaching}}%" style="width:{{$studyProgress->Approaching}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('approaching_color')}};">{{$studyProgress->Approaching}}%</div>
                                                                    @endif
                                                                    @if($studyProgress->Proficient != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Proficient}}%" style="width:'{{$studyProgress->Proficient}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('proficient_color')}};">{{$studyProgress->Proficient}}%</div>
                                                                    @endif
                                                                    @if($studyProgress->Advanced != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Advanced}}%" style="width:{{$studyProgress->Advanced}}%;background-color: {{App\Helpers\Helper::getGlobalConfiguration('advanced_color')}};">{{$studyProgress->Advanced}}%</div>
                                                                    @endif
                                                                    @if($studyProgress->InComplete != 0) 
                                                                    <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->InComplete}}%" style="width:{{$studyProgress->InComplete}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('incomplete_color')}};">{{$studyProgress->InComplete}}%</div>
                                                                    @endif 
                                                                </div>
                                                            </td>
                                                            @php
                                                                $progressQuestions = json_decode($assignmentExcercise->questions_difficulties);
                                                            @endphp
                                                            <td class="question-difficulty-level-td">
                                                                <div class="progress" >
                                                                    @php
                                                                    if($progressQuestions->Level1 !=0) {
                                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level1.'%" style="width:'.$progressQuestions->Level1.'%;background-color: '.$progressQuestions->Level1_color.';">'.$progressQuestions->Level1.'%'.'</div>';																
                                                                    }
                                                                    if($progressQuestions->Level2 !=0) {
                                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level2.'%" style="width:'.$progressQuestions->Level2.'%;background-color: '.$progressQuestions->Level2_color.';">'.$progressQuestions->Level2.'%'.'</div>';																
                                                                    }
                                                                    if($progressQuestions->Level3 !=0) {
                                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level3.'%" style="width:'.$progressQuestions->Level3.'%;background-color: '.$progressQuestions->Level3_color.';">'.$progressQuestions->Level3.'%'.'</div>';																
                                                                    }
                                                                    if($progressQuestions->Level4 !=0) {
                                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level4.'%" style="width:'.$progressQuestions->Level4.'%;background-color: '.$progressQuestions->Level4_color.';">'.$progressQuestions->Level4.'%'.'</div>';																
                                                                    }
                                                                    if($progressQuestions->Level5 !=0) {
                                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level5.'%" style="width:'.$progressQuestions->Level5.'%;background-color: '.$progressQuestions->Level5_color.';">'.$progressQuestions->Level5.'%'.'</div>';																
                                                                    }
                                                                    @endphp
                                                                </div>
                                                            </td>
                                                            <td class="btn-edit dashboard_table_btn_group">
                                                                <a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $assignmentExcercise->exam_id, 'filter' => 'filter', 'grade_id' => $assignmentExcercise->grade_id, 'class_type_id' => array($assignmentExcercise->class_id), 'group_id' => $assignmentExcercise->peer_group_id]) }}" title="{{__('languages.performance_report')}}" >
                                                                    <i class="fa fa-bar-chart fa-lg ml-2" aria-hidden="true"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" title="{{__('languages.ability_analysis')}}" class="getClassAbilityAnalysisReport" data-examid="{{$assignmentExcercise->exam_id}}" data-studentids="{{$assignmentExcercise->student_ids}}" data-isGroup="{{!empty($assignmentTest->peer_group_id) ? true : false}}" data-buttonText="{{!empty($assignmentExcercise->peer_group_id) ? __('languages.My Group') : __('languages.My Class')}}">
                                                                    <i class="fa fa-bar-chart fa-lg ml-2" aria-hidden="true"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$assignmentExcercise->exam_id}}">
                                                                    <i class="fa fa-bar-chart fa-lg ml-2" aria-hidden="true"></i>
                                                                </a>
                                                                @php
                                                                    if(isset($assignmentExcercise->grade_with_class) && !empty($assignmentExcercise->grade_with_class)){
                                                                        $gradesClass=explode('-',$assignmentExcercise->grade_with_class);
                                                                    }else{
                                                                        $gradesClass = [];
                                                                    }
                                                                @endphp
                                                                <a href="{{route('exam-configuration-preview', $assignmentExcercise->exam_id)}}" class="ml-2" title="{{__('languages.configurations')}}">
                                                                    <i class="fa fa-gear fa-lg" aria-hidden="true"></i>
                                                                </a>
                                                                <a href="javascript:void(0);" class="exam_questions-info fa-lg ml-2" data-examid="{{$assignmentExcercise->exam_id}}" title="{{__('languages.preview')}}"><i class="fa fa-book" aria-hidden="true"></i></a>
                                                                <a href="javascript:void(0);" class="result_summary fa-lg ml-2" data-examid="{{$assignmentExcercise->exam_id}}" data-studentids="{{$assignmentExcercise->student_ids}}" title="{{__('languages.result_summary')}}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                        
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                    <tr><td>{{__('languages.no_data_found')}}</td></tr>
                                                    @endif
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- Latest Excercise --}}

                                {{-- Latest Test--}}
                                @if(!empty($AssignmentTestList))
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-4">
                                        <h3>{{__('languages.latest')}} {{__('languages.test_text')}}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-4">
                                            <div class="card dashboard_table">
                                                <table class="styled-table">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th class="selec-opt">{{__('languages.publish_date_time')}}</th> --}}
                                                            <th>{{__('languages.report.start_date')}} & {{__('languages.time')}}</th>
                                                            {{-- <th>{{__('languages.report.end_date')}} & {{__('languages.time')}}</th> --}}
                                                            <th>{{__('languages.report.result_release_date')}}</th>
                                                            <th>{{__('languages.reference_number')}}</th>
                                                            <th>{{__('languages.title')}}</th>
                                                            <th>{{__('languages.class')}}/{{__('languages.group')}}</th>
                                                            {{-- <th>{{__('languages.students')}}</th> --}}
                                                            <th>{{__('languages.submission')}} {{__('languages.status')}}</th>
                                                            {{-- <th>{{__('languages.average_accuracy')}}</th> --}}
                                                            <th>{{__('languages.study_status')}}</th>
                                                            <th>{{__('languages.question_difficulties')}}</th>
                                                            <th>{{__('languages.action')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="scroll-pane">
                                                        @if(!empty($AssignmentTestList))
                                                        
                                                            @foreach($AssignmentTestList as $assignmentTest)
                                                            <tr>
                                                                {{-- <td>{{ date('d/m/Y H:i:s',strtotime($assignmentTest->exams->publish_date)) }}</td> --}}
                                                                <td>
                                                                    @if(isset($assignmentTest->exams->ExamGradeClassConfigurations->start_date) && $assignmentTest->exams->ExamGradeClassConfigurations->start_date !="")
                                                                        @php
                                                                            $start_time='00:00:00';
                                                                        @endphp
                                                                        @if(isset($assignmentTest->exams->ExamGradeClassConfigurations->start_time) && $assignmentTest->exams->ExamGradeClassConfigurations->start_time !="")
                                                                            @php
                                                                                $start_time=$assignmentTest->exams->ExamGradeClassConfigurations->start_time;
                                                                            @endphp
                                                                        @endif
                                                                        {{ date('d/m/Y H:i:s',strtotime($assignmentTest->exams->ExamGradeClassConfigurations->start_date.' '.$start_time)) }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </td>
                                                                {{-- <td>
                                                                    @if(isset($assignmentTest->exams->ExamGradeClassConfigurations->end_date) && $assignmentTest->exams->ExamGradeClassConfigurations->end_date !="")
                                                                        @php
                                                                            $end_time='00:00:00';
                                                                        @endphp
                                                                        @if(isset($assignmentTest->exams->ExamGradeClassConfigurations->end_time) && $assignmentTest->exams->ExamGradeClassConfigurations->end_time !="")
                                                                            @php
                                                                                $end_time=$assignmentTest->exams->ExamGradeClassConfigurations->end_time;
                                                                            @endphp
                                                                        @endif
                                                                        {{ date('d/m/Y H:i:s',strtotime($assignmentTest->exams->ExamGradeClassConfigurations->end_date.' '.$end_time)) }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </td> --}}
                                                                <td>{{date('d/m/Y',strtotime($assignmentTest->exams->result_date))}}</td>
                                                                <td>{{$assignmentTest->exams->reference_no}}</td>
                                                                <td>{{$assignmentTest->exams->title}}</td>
                                                                <td>
                                                                    @if(!empty($assignmentTest->peerGroup)) 
                                                                        {{ $assignmentTest->peerGroup->group_name }}
                                                                    @else 
                                                                        {{ $assignmentTest->grade_with_class }}
                                                                    @endif
                                                                </td>
                                                                {{-- <td>{{ $assignmentTest->no_of_students }}</td> --}}
                                                                @php																
                                                                    $progress = json_decode($assignmentTest->student_progress, true);
                                                                    $accuracy = json_decode($assignmentTest->average_accuracy, true);
                                                                @endphp
                                                                <td>
                                                                    <div class="progress student-progress-report" data-examid="{{$assignmentTest->exam_id}}"  data-studentids="{{$assignmentTest->student_ids}}">
                                                                        <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$progress['progress_tooltip']}}"style="width:{{$progress['progress_percentage']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$progress['progress_percentage']}}" aria-valuemin="0" aria-valuemax="100">{{$progress['progress_percentage']}}%</div>
                                                                    </div>
                                                                </td>
                                                                {{-- <td>
                                                                    <div class="progress">
                                                                        <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$accuracy['average_accuracy_tooltip']}}" style="width:{{$accuracy['average_accuracy']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$accuracy['average_accuracy']}}" aria-valuemin="0" aria-valuemax="100">{{$accuracy['average_accuracy']}}%</div>
                                                                    </div>
                                                                </td> --}}
                                                                
                                                                @php 
                                                                    $studyProgress = json_decode($assignmentTest->study_status);
                                                                @endphp
                                                                <td class="study-status-progressbar-td">
                                                                    <div class="progress">
                                                                        @if($studyProgress->Struggling != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Struggling}}%" style="width:{{$studyProgress->Struggling}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('struggling_color')}};">{{$studyProgress->Struggling}}%</div>
                                                                        @endif
                                                                        @if($studyProgress->Beginning != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Beginning}}%" style="width:{{$studyProgress->Beginning}}%;background-color: {{App\Helpers\Helper::getGlobalConfiguration('beginning_color')}};">{{$studyProgress->Beginning}}%</div>
                                                                        @endif
                                                                        @if($studyProgress->Approaching != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Approaching}}%" style="width:{{$studyProgress->Approaching}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('approaching_color')}};">{{$studyProgress->Approaching}}%</div>
                                                                        @endif
                                                                        @if($studyProgress->Proficient != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Proficient}}%" style="width:'{{$studyProgress->Proficient}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('proficient_color')}};">{{$studyProgress->Proficient}}%</div>
                                                                        @endif
                                                                        @if($studyProgress->Advanced != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->Advanced}}%" style="width:{{$studyProgress->Advanced}}%;background-color: {{App\Helpers\Helper::getGlobalConfiguration('advanced_color')}};">{{$studyProgress->Advanced}}%</div>
                                                                        @endif
                                                                        @if($studyProgress->InComplete != 0) 
                                                                        <div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="{{$studyProgress->InComplete}}%" style="width:{{$studyProgress->InComplete}}%;background-color:{{App\Helpers\Helper::getGlobalConfiguration('incomplete_color')}};">{{$studyProgress->InComplete}}%</div>
                                                                        @endif 
                                                                    </div>
                                                                </td>
                                                                @php
                                                                    $progressQuestions = json_decode($assignmentTest->questions_difficulties);
                                                                @endphp
                                                                <td class="question-difficulty-level-td">
                                                                    <div class="progress" >
                                                                        @php
                                                                        if($progressQuestions->Level1 !=0) {
                                                                            echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level1.'%" style="width:'.$progressQuestions->Level1.'%;background-color: '.$progressQuestions->Level1_color.';">'.$progressQuestions->Level1.'%'.'</div>';																
                                                                        }
                                                                        if($progressQuestions->Level2 !=0) {
                                                                            echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level2.'%" style="width:'.$progressQuestions->Level2.'%;background-color: '.$progressQuestions->Level2_color.';">'.$progressQuestions->Level2.'%'.'</div>';																
                                                                        }
                                                                        if($progressQuestions->Level3 !=0) {
                                                                            echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level3.'%" style="width:'.$progressQuestions->Level3.'%;background-color: '.$progressQuestions->Level3_color.';">'.$progressQuestions->Level3.'%'.'</div>';																
                                                                        }
                                                                        if($progressQuestions->Level4 !=0) {
                                                                            echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level4.'%" style="width:'.$progressQuestions->Level4.'%;background-color: '.$progressQuestions->Level4_color.';">'.$progressQuestions->Level4.'%'.'</div>';																
                                                                        }
                                                                        if($progressQuestions->Level5 !=0) {
                                                                            echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions->Level5.'%" style="width:'.$progressQuestions->Level5.'%;background-color: '.$progressQuestions->Level5_color.';">'.$progressQuestions->Level5.'%'.'</div>';																
                                                                        }
                                                                        @endphp
                                                                    </div>
                                                                </td>
                                                                <td class="btn-edit dashboard_table_btn_group">
                                                                    <a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $assignmentTest->exam_id, 'filter' => 'filter', 'grade_id' => $assignmentTest->grade_id, 'class_type_id' => array($assignmentTest->class_id), 'group_id' => $assignmentTest->peer_group_id]) }}" title="{{__('languages.performance_report')}}"><i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i></a>
                                                                    <a href="javascript:void(0);" title="{{__('languages.ability_analysis')}}" class="getClassAbilityAnalysisReport ml-2" data-examid="{{$assignmentTest->exam_id}}" data-studentids="{{$assignmentTest->student_ids}}" data-isGroup="{{!empty($assignmentTest->peer_group_id) ? true : false}}" data-buttonText="{{!empty($assignmentTest->peer_group_id) ? __('languages.My Group') : __('languages.My Class')}}" >
                                                                        <i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport ml-2" data-examid="{{$assignmentTest->exam_id}}">
                                                                        <i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i>
                                                                    </a>
                                                                    @php
                                                                        if(isset($assignmentTest->grade_with_class) && !empty($assignmentTest->grade_with_class)){
                                                                            $gradesClass = explode('-',$assignmentTest->grade_with_class);
                                                                        }else{
                                                                            $gradesClass = [];
                                                                        }
                                                                    @endphp
                                                                    <a href="{{route('exam-configuration-preview', $assignmentTest->exam_id)}}" class="ml-2" title="{{__('languages.configurations')}}">
                                                                        <i class="fa fa-gear fa-lg" aria-hidden="true"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$assignmentTest->exam_id}}" title="{{__('languages.preview')}}">
                                                                        <i class="fa fa-book fa-lg" aria-hidden="true"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0);" class="result_summary ml-2" data-examid="{{$assignmentTest->exam_id}}" data-studentids="{{$assignmentTest->student_ids}}" title="{{__('languages.result_summary')}}"><i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i></a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                        <tr><td>{{__('languages.no_data_found')}}</td></tr>
                                                        @endif
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- Latest Excercise --}}
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.layouts.footer')
    <!-- Start Performance Analysis Popup -->
<div class="modal" id="class-ability-analysis-report" tabindex="-1" aria-labelledby="class-ability-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<input type="hidden" name="grade_ids" id="grade_ids" value="">
				<input type="hidden" name="exam_ids" id="exam_ids" value="">
				<input type="hidden" name="student_ids" id="student_ids" value="">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.ability_analysis')}}</h4>
					<button type="button" class="close class-ability-analysis-report-close-pop" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="row pb-3">
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-class" data-classAbilityIsGroup="false">
								<span class="my_class_group_button">{{__('languages.my_class.my_classes')}}</span>
							</button>
						</div>
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-school" data-classAbilityIsGroup="false">{{__('languages.my_school')}}</button>
						</div>
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="all-school" data-classAbilityIsGroup="false">{{__('languages.all_schools')}}</button>
						</div>
					</div>
					<div class="row">
						<img src="" id="class-ability-analysis-report-image" class="img-fluid">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default class-ability-analysis-report-close-pop" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Performance Analysis Popup -->
<!-- Start list of difficulties of the questions in the test Analysis Popup -->
<div class="modal" id="test-difficulty-analysis-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.difficulty_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body Graph-body">
					<img src="" id="test-difficulty-analysis-report-image" class="img-fluid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of difficulties of the questions in the test Analysis Popup -->
<!-- Start list of Student Progress Report Popup -->
<div class="modal" id="modal-student-progress-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.submission')}} {{__('languages.status')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<table id="student-list" class="display" style="width:100%">
	                    <thead>
	                        <tr>
	                            <th class="first-head"><span>{{__('languages.name')}}</span></th>
								<th class="first-head"><span>{{__('languages.student_code')}}</span></th>
	                            <th class="sec-head selec-opt"><span>{{__('languages.email_address')}}</span></th>
	                            <th class="selec-head">{{__('languages.status')}}</th>
	                        </tr>
	                    </thead>
	                    <tbody class="scroll-pane">
		                </tbody>
		            </table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of Student Progress Report Popup --> 
    <script>
       $(function() {
            /**
             * USE : Display on graph Get Class APerformance Analysis
             * Trigger : On click Performance graph icon into exams list action table
             * **/
            $(document).on('click', '.getClassAbilityAnalysisReport', function(e) {
                $("#cover-spin").show();
                $('#class-ability-analysis-report').modal('show');
                $studentIds = $(this).attr('data-studentids');
                $examId = $(this).attr('data-examid');
                $buttonText = $(this).attr('data-buttonText');
                var isGroup = $(this).attr('data-isGroup');
                $('.class-ability-graph-btn').attr('data-classAbilityIsGroup',isGroup);
                $('#exam_ids').val($examId);
                $('#student_ids').val($studentIds);
                $('.my_class_group_button').html($buttonText);
                if($studentIds && $examId){
                    $.ajax({
                        url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
                        type: 'post',
                        data : {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'examid' : $examId,
                            'studentIds' : $studentIds,
                            'graph_type' : 'my-class',
                            'isGroup' : isGroup
                        },
                        success: function(response) {
                            var ResposnseData = JSON.parse(JSON.stringify(response));
                            if(ResposnseData.data != 0){
                                // Append image src attribute with base64 encode image
                                $('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
                                $('#class-ability-analysis-report').modal('show');
                            }else{
                                toastr.error(DATA_NOT_FOUND);
                            }
                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                }
            });

             /**
             * USE : Click on the diffrent button like this 'my-class', 'my-school', 'all-school'
             * **/
            $(document).on('click', '.class-ability-graph-btn', function(e) {
                $("#cover-spin").show();
                $studentIds = $('#student_ids').val();
                $examId = $('#exam_ids').val();
                if($studentIds && $examId){
                    $.ajax({
                        url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
                        type: 'post',
                        data : {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'examid' : $examId,
                            'studentIds' : $studentIds,
                            'graph_type' : $(this).attr('data-graphtype'),
                            'isGroup' : $(this).attr('data-classAbilityIsGroup')
                        },
                        success: function(response) {
                            var ResposnseData = JSON.parse(JSON.stringify(response));
                            if(ResposnseData.data != 0){
                                // Append image src attribute with base64 encode image
                                $('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
                                $('#class-ability-analysis-report').modal('show');
                            }else{
                                toastr.error(DATA_NOT_FOUND);
                            }
                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                }
            });

            // get student progress report
            $(document).on('click', '.student-progress-report', function(e) {
                $("#cover-spin").show();
                $examId = $(this).attr('data-examid');
                $studentIds = $(this).attr('data-studentids');
                if($examId && $studentIds){
                    $.ajax({
                        url: BASE_URL + '/myteaching/student-progress-report',
                        type: 'post',
                        data : {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'examid' : $examId,
                            'studentIds' : $studentIds
                        },
                        success: function(response) {
                            if(response.data.length != 0){
                                $("#student-list").DataTable().destroy();
                                $("#student-list tbody").html(response.data);
                                $("#student-list").DataTable({
                                    order: [[0, "desc"]],
                                });
                                $("#modal-student-progress-report").modal('show');
                            }else{
                                toastr.error(DATA_NOT_FOUND);
                            }
                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                }
            });
       });
    </script>
@endsection