@extends('backend.layouts.app')
    @section('content')
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
                                        <span class="d-flex justify-content-center"><b>{{__('Students')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $UsersCount['student']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Teachers')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $UsersCount['teacher']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Co-Ordinators')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $UsersCount['co-ordinator']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Panel Heads')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $UsersCount['panel_head']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Principals')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $UsersCount['principal']}} </b></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Self Learning')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $ExamsCount['self_learning']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Exercise')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $ExamsCount['exercise']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Test')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $ExamsCount['test']}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Ai-Based Assessment')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $ExamsCount['ai_based_assessment']}} </b></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Form')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $gradesCount}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Classes')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $classesCount}} </b></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-2 mb-4 ">
                                        <div class="card">
                                        <span class="d-flex justify-content-center"><b>{{__('Peer Groups')}} </b></span>
                                        <hr/>
                                        <span class="d-flex justify-content-center"><b>{{ $groupCount}} </b></span>
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
                                            <div class="card">
                                                <table border="1">
                                                <thead>
                                                    <tr>
                                                        {{-- <th class="selec-opt">{{__('languages.publish_date_time')}}</th> --}}
                                                        <th>{{__('languages.report.start_date')}} & {{__('languages.time')}}</th>
                                                        {{-- <th>{{__('languages.report.end_date')}} & {{__('languages.time')}}</th> --}}
                                                        <th>{{__('languages.report.result_release_date')}}</th>
                                                        <th>{{__('languages.reference_number')}}</th>
                                                        <th>{{__('languages.title')}}</th>
                                                        <th>{{__('languages.form')}} - {{__('languages.group')}}</th>
                                                        {{-- <th>{{__('languages.students')}}</th> --}}
                                                        {{-- <th>{{__('languages.submission')}} {{__('languages.status')}}</th>
                                                        <th>{{__('languages.average_accuracy')}}</th> --}}
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
                                                            {{-- <td>
                                                                <div class="progress student-progress-report" data-examid="{{$assignmentExcercise->exam_id}}"  data-studentids="{{$assignmentExcercise->student_ids}}">
                                                                    <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$progress['progress_tooltip']}}"style="width:{{$progress['progress_percentage']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$progress['progress_percentage']}}" aria-valuemin="0" aria-valuemax="100">{{$progress['progress_percentage']}}%</div>
                                                                </div>
                                                            </td>
                                                            <td>
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
                                                            <td class="btn-edit">
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
                                            <div class="card">
                                                <table border="1">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th class="selec-opt">{{__('languages.publish_date_time')}}</th> --}}
                                                            <th>{{__('languages.report.start_date')}} & {{__('languages.time')}}</th>
                                                            {{-- <th>{{__('languages.report.end_date')}} & {{__('languages.time')}}</th> --}}
                                                            <th>{{__('languages.report.result_release_date')}}</th>
                                                            <th>{{__('languages.reference_number')}}</th>
                                                            <th>{{__('languages.title')}}</th>
                                                            <th>{{__('languages.form')}}-{{__('languages.group')}}</th>
                                                            {{-- <th>{{__('languages.students')}}</th> --}}
                                                            {{-- <th>{{__('languages.submission')}} {{__('languages.status')}}</th>
                                                            <th>{{__('languages.average_accuracy')}}</th> --}}
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
                                                                {{-- <td>
                                                                    <div class="progress student-progress-report" data-examid="{{$assignmentTest->exam_id}}"  data-studentids="{{$assignmentTest->student_ids}}">
                                                                        <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$progress['progress_tooltip']}}"style="width:{{$progress['progress_percentage']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$progress['progress_percentage']}}" aria-valuemin="0" aria-valuemax="100">{{$progress['progress_percentage']}}%</div>
                                                                    </div>
                                                                </td>
                                                                <td>
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
                                                                <td class="btn-edit">
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
@endsection