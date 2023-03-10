@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
    @include('backend.layouts.sidebar')
    <div id="content" class="pl-2 pb-5">
        @include('backend.layouts.header')
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="sm-right-detail-sec pl-5 pr-5">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{__('languages.question_generators_menu.question_generators')}}</h2>
                        </div>
                        <hr class="blue-line">
                    </div>
                </div>
                 @if(session()->has('success_msg'))
                        <div class="alert alert-success">
                    {{ session()->get('success_msg') }}
                </div>
                @endif
                @if(session()->has('error_msg'))
                <div class="alert alert-danger">
                    {{ session()->get('error_msg') }}
                </div>
                @endif
                @php
                    if(Auth::user()->role_id==2){
                        $color = '#f7bfbf';
                    }else if(Auth::user()->role_id==3){
                        $color = '#d8dc41';
                    }else if(Auth::user()->role_id == 7){
                        $color = '#BDE5E1';
                    }else if(Auth::user()->role_id == 8){
                        $color = '#fed08d';
                    }else if(Auth::user()->role_id == 5){
                        $color = '#a8e4b0';
                    }else{
                        $color = '#A5A6F6';
                    }
                @endphp
                <style type="text/css">
                    .question-generator-option-headings .admin-tab {
                        background-color:<?php echo $color;?> !important;
                    }
                    .question-generator-option-headings li.admin-tab.tab_active {
                        background-color:<?php echo str_replace(';','', App\Helpers\Helper::getRoleBasedMenuActiveColor());?> !important;
                    }
                    .bg-pink {
                        background-color:<?php echo $color;?> !important;
                        border-color:<?php echo $color;?> !important;
                    }
                    .sm-deskbord-main-sec .tab-content-wrap .nav-pills .nav-link.active {
                        background-color: <?php echo $color;?> !important;
                        color: #000;
                        font-family: inherit;
                    }
                </style>
                <form name="question-generator" id="question-generator" action="{{ route('school.generate-questions') }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 admin-tab tab_active " data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="step-headings section-step2 admin-tab" data-tabid="2">2.{{__('languages.question_generators_menu.to_classes_peer_group')}}</li>
                                            <li class="step-headings section-step3 admin-tab" data-tabid="3">3. {{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                            <li class="step-headings section-step4 admin-tab" data-tabid="4">4. {{__('languages.question_generators_menu.review_of_questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.test_mode')}}</label>
                                            <select name="test_type" class="form-control select-option" id="test_type">
                                                <option value="2" @if(request()->get('type') == 2) selected @endif>{{__('languages.excercise')}}</option> 
                                                <option value="3" @if(request()->get('type') == 3) selected @endif>{{__('languages.test_text')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.title')}}</label>
                                            <input type="text" name="title" value="" class="form-control" placeholder="{{__('languages.question_generators_menu.enter_title')}}" required>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.question_generators_menu.start_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="start_date" value="{{date('d/m/Y')}}" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="start-date-error"></span>
                                            @if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>
                                            <div class="input-group date">
                                                <select name="start_time" class="form-control select-option" id="test_start_time">
                                                    <option value="">{{__('languages.question_generators_menu.select_test_start_time')}}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option value="{{$time}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('start_time'))<span class="validation_error">{{ $errors->first('start_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.question_generators_menu.end_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control end-date-picker" name="end_date" value="{{date('d/m/Y',strtotime("+1 days"))}}" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>
                                            <div class="input-group date">
                                                <select name="end_time" class="form-control select-option" id="test_end_time">
                                                    <option value="">{{ __('languages.question_generators_menu.select_test_end_time') }}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option value="{{$time}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('end_time'))<span class="validation_error">{{ $errors->first('end_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.report_date')}}</label>
                                            <select name="report_date" class="form-control select-option" id="select-report-date">
                                                <option value="end_date">{{__('languages.question_generators_menu.end_date')}}</option>
                                                <option value="after_submit" selected>{{__('languages.question_generators_menu.after_submit')}}</option>
                                                <option value="custom_date">{{__('languages.question_generators_menu.custom_date')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50 custom_date" style="display: none;">
                                            <label>{{__('languages.question_generators_menu.report_custom_date')}}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="custom_date" value="{{date('d/m/Y')}}" placeholder="{{__('languages.question_generators_menu.report_custom_date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('custom_date'))<span class="validation_error">{{ $errors->first('custom_date') }}</span>@endif
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50" id="no_of_trials_per_question_section" style="display:none;">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.no_of_trials_per_question')}}</label>
                                            <select name="no_of_trials_per_question" class="form-control select-option" id="select-no-of-per-trials-question">
                                                <option value="1" selected>1</option> 
                                                <option value="2">2</option>
                                            </select>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
                                                <option value="manual">{{__('languages.manual')}}</option>
                                                <option value="auto">{{__('languages.question_generators_menu.auto_fit')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(!empty($difficultyLevels))
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50" id="display_hints_section" style="display:none;">
                                            <label>{{__('languages.question_generators_menu.display_hints')}}</label>
                                            <select name="display_hints" class="form-control select-option" id="select-display-hints">
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                                <option value="yes" selected>{{__('languages.question_generators_menu.yes')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_full_solution_in_report')}}</label>
                                            <select name="display_full_solution" class="form-control select-option" id="select-display-full-solutions">
                                                <option value="yes" selected>{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_pr_answer_hints')}}</label>
                                            <select name="display_pr_answer_hints" class="form-control select-option" id="select-display-pr-answer-hints">
                                                <option value="yes" selected>{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_answer')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_answer" class="form-control select-option" id="select-randomize-answers">
                                                <option value="yes">{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_order')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_order" class="form-control select-option" id="select-randomize-order">
                                                <option value="yes">{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div> -->

                                        <div class="col-md-12" id="credit-point-rules-section">
                                            <h5 class="font-weight-bold">{{__('Credit Point Rules')}}</h5>
                                            <ol>
                                                <li>
                                                    <h6 class="font-weight-bold">{{__('languages.assignment')}}:</h6>
                                                    <ol>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.submission_on_time')}}:</label>
                                                                    <input type="radio" name="submission_on_time" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    ><span>{{__('languages.question_generators_menu.yes')}}</span>
                                                                    <input type="radio" name="submission_on_time" value="no"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("submission_on_time")))
                                                                        checked
                                                                    @endif
                                                                    ><span>{{__('languages.question_generators_menu.no')}}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.credit_points_of_accuracy')}}:</label>
                                                                    <input type="radio" name="credit_points_of_accuracy" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_accuracy" value="no"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy")))
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.no')}}
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.credit_points_of_normalized_ability')}}:</label>
                                                                    <input type="radio" name="credit_points_of_normalized_ability" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_normalized_ability" value="no" 
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability")))
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.no')}}
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_1" data-stepid="1">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step2" style="display:none;">
                                    <div class="form-row">
                                        <div class="form-grade-section">
                                            <div class="student-grade-class-section row">
                                                <div class="form-grade-heading col-lg-3">
                                                    <label>{{__('languages.question_generators_menu.grade-classes')}}</label>
                                                </div>
                                                <div class="form-grade-select-section col-lg-9">
                                                    @if(!empty($GradeClassData))
                                                    @foreach($GradeClassData as $grade)
                                                    <div class="form-grade-select">
                                                        <div class="form-grade-option">
                                                            <div class="form-grade-single-option">
                                                                <input type="checkbox" name="grades[]" value="{{$grade->id}}" class="question-generator-grade-chkbox">{{$grade->name}}
                                                            </div>
                                                        </div>
                                                        @if(!empty($grade->classes))
                                                        <div class="form-grade-sub-option">
                                                            <div class="form-grade-sub-single-option">
                                                                @foreach($grade->classes as $classes)
                                                                <input type="checkbox" name="classes[{{$grade->id}}][]" value="{{$classes->id}}" class="question-generator-class-chkbox" data-label="{{$grade->name}}{{$classes->name}}">
                                                                <label>{{$grade->name}}{{$classes->name}}</label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <div class="col-md-12 grade-class-date-time-list"></div>
                                            </div>

                                            <div class="form-group student_list_section mt-3 row">
                                                <div class="student_list_heading col-lg-3">
                                                    <label>{{__('languages.question_generators_menu.select_individual_students')}}</label>
                                                </div>
                                                <div class="student_list_option col-lg-3">
                                                    @if(isset($StudentList) && !empty($StudentList))
                                                    <select name="studentIds[]" class="form-control select-option" id="question-generator-student-id" multiple disabled>
                                                    @foreach($StudentList as $student)
                                                        <option value="{{$student->id}}" >
                                                            @if(app()->getLocale() == 'en') {{$student->DecryptNameEn}}  @else {{$student->DecryptNameCh}}  @endif
                                                            @if($student->class_student_number) ({{$student->class_student_number}}) @endif
                                                        </option>
                                                    @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group student_peer_group_section mt-3 row">
                                                <div class="student_peer_group_heading col-lg-3">
                                                    <label>{{__('languages.question_generators_menu.student_peer_groups')}}</label>
                                                </div>
                                                <div class="student_peer_group_option col-lg-3">
                                                    <select class="form-control select-option" data-show-subtext="true" data-live-search="true" name="peerGroupIds[]" id="question-generator-peer-group-options"  multiple>
                                                        @if($PeerGroupList)
                                                            @foreach($PeerGroupList as $peerGroup)
                                                                @if(count($peerGroup->members) > 0)
                                                                <option value="{{$peerGroup->id}}" data-label="{{$peerGroup->PeerGroupName}}">{{$peerGroup->PeerGroupName}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-12 group-date-time-list mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.next')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step3" style="display:none;">
                                    <input type="hidden" name="questionIds" value="" id="questionIds">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.strands')}}</label>
                                            <select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
                                                @if(isset($strandsList) && !empty($strandsList))
                                                    @foreach ($strandsList as $strandKey => $strand)
                                                        <option value="{{ $strand->id }}" <?php if($strandKey == 0){echo 'selected';}?>>{{ $strand->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_strands_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.learning_units')}}</label>
                                            <select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple>
                                                @if(isset($LearningUnits) && !empty($LearningUnits))
                                                    @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                        {{-- <option value="{{ $learningUnit->id }}" selected>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option> --}}
                                                        <option value="{{ $learningUnit['id'] }}" selected>{{ $learningUnit['name_en'] }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                @endif
                                            </select>
                                            <label class="error learning_unit_error_msg"></label>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.total_no_of_questions')}}</label>
                                            <input type="text" name="total_no_of_questions" id="total_no_of_questions" value="" class="form-control" placeholder="{{__('languages.question_generators_menu.total_no_of_questions')}}" required readonly>
                                        </div>
                                    </div>
                                    <hr class="blue-line">
                                    <div class="form-row question-info">
                                        {{-- <p>Minimum No of Question Per Skill Required : <strong>{{$RequiredQuestionPerSkill['minimum_question_per_skill']}}</strong></p> --}}
                                        <p>{{__('languages.question_generators_menu.maximum_no_of_question_per_objective')}} : <strong>{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}</strong></p>
                                    </div>
                                    <div class="form-row">
                                        <div class="question-generator-objectives-labels">
                                            <label>{{__('languages.question_generators_menu.learning_objectives')}}</label>
                                            <label>{{__('languages.question_generators_menu.difficulty_level')}}</label>
                                            <label>{{__('languages.question_generators_menu.no_of_question_per_learning_objectives')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-row selection-learning-objectives-section">
                                        @if(isset($LearningObjectives) && !empty($LearningObjectives))
                                        <div class="selected-learning-objectives-difficulty">
                                            <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> Select All
                                        </div>
                                        @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                        @php
                                            $noOfQuestionPerLearningObjective=App\Helpers\Helper::getNoOfQuestionPerLearningObjective($learningObjectives['learning_unit_id'],$learningObjectives['id']);
                                        @endphp
                                        {{-- <div class="selected-learning-objectives-difficulty">
                                            <input type="checkbox" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}]" value="{{ $learningObjectives->id }}" class="learning_objective_checkbox" checked>
                                            <label>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</label>
                                            <select name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                            <input type="text" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $noOfQuestionPerLearningObjective }}" class="get_no_of_question_learning_objectives" min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}">
                                        </div>
                                        @endforeach
                                        @endif
                                    </div> --}}
                                    <div class="selected-learning-objectives-difficulty">
                                        <input type="checkbox" name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{ $learningObjectives['id'] }}]" value="{{ $learningObjectives['id'] }}" class="learning_objective_checkbox" checked>
                                        <label>{{ $learningObjectives['foci_number'] }} {{ $learningObjectives['title_'.app()->getLocale()] }}</label>
                                        <select name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{ $learningObjectives['id'] }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                        <input type="text" name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{ $learningObjectives['id'] }}][get_no_of_question_learning_objectives]" value="{{ $noOfQuestionPerLearningObjective }}" class="get_no_of_question_learning_objectives" min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}">
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                {{-- <section class="form-steps step4" style="display:none;">
                                    <div class="d-flex review-question-main tab-content-wrap w-100"></div>
                                    <div class="form-row select-data float-left mt-2">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_4" data-stepid="4">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="submit" name="save_as_draft" value="save_as_draft" class="blue-btn btn btn-primary" data-stepid="4" value="save_as_draft">{{__('languages.question_generators_menu.save_as_draft')}}</button>
                                                <button type="submit" name="save_and_publish" value="save_and_publish" class="blue-btn btn btn-primary" data-stepid="4" value="save_and_publish">{{__('languages.question_generators_menu.save_and_publish')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section> --}}

                                <section class="form-steps step4" style="display:none;">
                                    <div class="d-flex review-question-main tab-content-wrap">
                                        <div class="review-question-left-section">
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" aria-orientation="vertical">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="pill" href="#Q1" role="tab" aria-controls="Q1" aria-selected="true">Q1</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="pill" href="#Q2" role="tab" aria-controls="Q2" aria-selected="false">Q2</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link"data-toggle="pill" href="#Q3" role="tab" aria-controls="Q3" aria-selected="false">Q3</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content review-question-tab" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="Q1">Q3 Content</div>
                                                <div class="tab-pane fade" id="Q2">Q2 Content</div>
                                                <div class="tab-pane fade" id="Q3">
                                                    <div class="pb-3">
                                                        <div class="question-strand-detail pl-3">
                                                            <label><b>{{__('languages.upload_document.strands')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-unit-detail">
                                                            <label><b>{{__('languages.upload_document.learning_units')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-objectives-detail pl-3">
                                                            <label><b>{{ __('languages.learning_objectives') }} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-difficulty-detail">
                                                            <label><b>{{__('languages.questions.difficulty_level')}} :</b> </label><span>Level 1</span>
                                                        </div>
                                                        <div class="question-heading pl-3">
                                                            <p class="question-title review-question-title">{{ __('languages.questions.question')}} :</p>
                                                        </div>
                                                        <div class="question-answer-content pl-3">
                                                            <div class="question_content">
                                                                <label for="question-content" class="pl-3">xyz question</label>
                                                            </div>
                                                            <div class="answer-content">
                                                                <div class="answer-review">
                                                                    <span>1</span>
                                                                    <p>option a</p>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span>2</span>
                                                                    <p>option b</p>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span>3</span>
                                                                    <p>option c</p>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span>4</span>
                                                                    <p>option d</p>
                                                                </div>
                                                                <div class="review-question-want-hint-button">
                                                                    <button type="button" class="btn-search bg-pink ml-1">{{__('languages.my_studies.want_a_hint')}}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="review-question-right-section">
                                            <div class="refresh-question-options">
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.strands')}}</label>
                                                    <select name="refresh_question_strand_id[]" class="form-control select-option" id="refresh-question-strand-id" multiple>
                                                        @if(isset($strandsList) && !empty($strandsList))
                                                            @foreach ($strandsList as $strandKey => $strand)
                                                                <option value="{{$strand->id}}" @if($strandKey == 0) selected @endif>{{$strand->{'name_'.app()->getLocale()} }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_strands_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.learning_units')}}</label>
                                                    <select name="refresh_question_learning_unit_id[]" class="form-control select-option" id="refresh-question-learning-unit" multiple>
                                                        @if(isset($LearningUnits) && !empty($LearningUnits))
                                                            @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                                {{-- <option value="{{$learningUnit->id}}" @if($learningUnitKey == 0) selected @endif>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option> --}}
                                                                <option value="{{ $learningUnit['id'] }}"  @if($learningUnitKey == 0) selected @endif>{{ $learningUnit['name_en'] }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.learning_objectives')}}</label>
                                                    <select name="refresh_question_learning_objectives_id[]" class="form-control select-option" id="refresh-question-learning-objectives" multiple>
                                                        @if(isset($LearningObjectives) && !empty($LearningObjectives))
                                                            @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                                                {{-- <option value="{{ $learningObjectives->id }}" @if($learningObjectivesKey == 0) selected @endif>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</option> --}}
                                                                <option value="{{ $learningObjectives['id'] }}" @if($learningObjectivesKey == 0) selected @endif>{{ $learningObjectives['foci_number'] }} {{ $learningObjectives['title_'.app()->getLocale()] }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_learning_objectives_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.questions.difficulty_level')}}</label>
                                                    <select name="refresh_question_difficulty_level[]" class="form-control select-option" id="refresh_question_difficulty_level" multiple>
                                                        @if(!empty($difficultyLevels))
                                                        @foreach($difficultyLevels as $difficultyLevel)
                                                        <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                        @endforeach
                                                        @endif								
                                                    </select>
                                                </div>
                                                <div class="refresh-question-btn">
                                                    <button type="button" class="btn-search bg-pink ml-1 refresh-question-data">{{__('languages.question_generators_menu.refresh_question')}}</button>
                                                </div>
                                            </div>
                                            <hr class="blue-line refresh-question-options-blue-line">
                                            <div class="refresh-question" style="display:none">
                                                <input type="hidden" id="refresh-question-id">
                                                <div class="tab-content">
                                                    <div class="pb-3">
                                                        <div class="question-strand-detail pl-3">
                                                            <label><b>{{__('languages.upload_document.strands')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-unit-detail">
                                                            <label><b>{{__('languages.upload_document.learning_units')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-objectives-detail pl-3">
                                                            <label><b>{{ __('languages.learning_objectives') }} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-difficulty-detail">
                                                            <label><b>{{__('languages.questions.difficulty_level')}} :</b> </label><span>Level 1</span>
                                                        </div>
                                                        <div class="question-heading pl-3">
                                                            <p class="question-title review-question-title">{{ __('languages.questions.question')}} :</p>
                                                        </div>
                                                        <div class="question-answer-content pl-2">
                                                            <div class="question_content">
                                                                <label for="question-content" class="pl-3">xyz question</label>
                                                            </div>
                                                            <div class="answer-content">
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">1</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option a</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">2</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option b</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">3</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option c</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">4</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option d</p>
                                                                    </div>
                                                                </div>
                                                                <div class="float-left mt-3 w-100">
                                                                    <button type="button" class="btn-search bg-pink want_a_hint">{{__('languages.my_studies.want_a_hint')}}</button>
                                                                </div>
                                                                <div class="replace-question-button">
                                                                    <button type="button" class="btn-search bg-pink ml-1 replace-question">{{__('languages.question_generators_menu.replace_question')}}</button>
                                                                    <button type="button" class="btn-search bg-pink ml-1 add-question">{{__('languages.add_questions')}}</button>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="blue-line refresh-question-options-blue-line">
                                        </div>
                                        <div class="btn_group mb-3 review-question-position-button">
                                            <button type="button" class="btn-search bg-pink btn-up"><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.up')}}</button>
                                            <button type="button" class="btn-search bg-pink  btn-down"><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.down')}}</button>
                                            <button type="button" class="btn-search bg-pink set-top"><i class="fa fa-arrow-up" aria-hidden="true"></i><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_top')}}</button>
                                            <button type="button" class="btn-search set-bottom bg-pink"><i class="fa fa-arrow-down" aria-hidden="true"></i><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_bottom')}}</button>
                                            <button type="button" class="btn-search btn-remove-tab set-bottom bg-pink"><i class="fa fa-trash  mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.remove')}}</button>
                                        </div>
                                    </div>
                                    <div class="form-row select-data float-left">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_4" data-stepid="4">{{__('languages.question_generators_menu.previous')}}</button>
                                                <!-- <button type="submit" class="blue-btn btn btn-primary" data-stepid="4">{{__('languages.question_generators_menu.submit')}}</button>-->
                                                <button type="submit" name="save_as_draft" value="save_as_draft" class="blue-btn btn btn-primary" data-stepid="4" value="save_as_draft">{{__('languages.question_generators_menu.save_as_draft')}}</button>
                                                <button type="submit" name="save_and_publish" value="save_and_publish" class="blue-btn btn btn-primary" data-stepid="4" value="save_and_publish">{{__('languages.question_generators_menu.save_and_publish')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Want a hint Modal -->
    <div class="modal fade" id="WantAHintModal" tabindex="-1" aria-labelledby="WantAHintModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
        </div>
    </div>
<!-- Want a hint Modal -->

<script>
var currentLanguage='{{app()->getLocale()}}';
var minimum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['minimum_question_per_skill'];?>');
var maximum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['maximum_question_per_skill'];?>');
var multiselectArray = {
    nSelectedText: 'Selecciones',
    enableHTML: true,
    templates: {
        filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
        filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>'
    },
    column: 1,
    placeholder: SELECT_DIFFICULTY_LEVEL,
    includeSelectAllOption: true,
    enableFiltering: true
}

$(function (){
    $(document).on('change', '.get_no_of_question_learning_objectives', function(e) {
        var minimum_question_per_skill_single = parseInt($(this).attr('min'));
        var maximum_question_per_skill_single = parseInt($(this).attr('max'));
        var noOfQuestionEntered = parseInt(e.target.value);
        if(noOfQuestionEntered != ""){
            if(noOfQuestionEntered >= minimum_question_per_skill_single && noOfQuestionEntered <= maximum_question_per_skill_single) {
            }else{
                toastr.error("Minimum question per skill required is :"+minimum_question_per_skill_single+" Maximum question per skill required is :"+maximum_question_per_skill_single);
                $(this).val('');
            }
        }
    });

    if($('#difficulty_mode').val() == 'manual'){
        // Set default difficulty level for selected first steps
        setDefaultDifficultyLevels();
    }else{
        $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
        $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','hidden');
        $(".question-generator-objectives-labels label:eq(1)").hide();
    }
    
    /**
     * USE : Set start time picker
     */
    $('#start_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    /**
     * USE : Set End time picker
     */
    $('#end_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    // event fire on click step button
    $(document).on('click', '.step-headings', function() {
        var currentStep = parseInt($(this).attr('data-tabid'))-1;
        if(currentStep != 0){
            for (var iClick = 1; iClick <= currentStep; iClick++) {
                $(".form-steps.step"+iClick+" .next-button").click();
            }
        }
    });

    // event fire on click next button
    $(document).on('click', '.next-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var nextStep = (parseInt(currentStep) + 1);
        if(checkValidation(currentStep)){
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step'+nextStep).addClass('tab_active');
            $('.step'+nextStep).show();
            if(currentStep == 3 && $('.form-steps.step1 .error').length==0 && $('.form-steps.step2 .error').length==0){
                getLearningObjectivesOptionList();
            }
        }else{
            setTimeout(function () {
                $('.step-headings').removeClass('tab_active');
                $('.section-step'+currentStep).addClass('tab_active');
                $('.section-step'+currentStep).click();
            },200)
        }
    });
    
    $(document).on('click', '.admin-tab', function() {
        var TabId = $(this).attr('data-tabid');
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+TabId).addClass('tab_active');
        $('.step'+TabId).show();
    });

    // Event fire on click previous button
    $(document).on('click', '.previous-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var previousStep = (parseInt(currentStep) - 1);
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+previousStep).addClass('tab_active');
        $('.step'+previousStep).show();
    });

    /**
     * USE : Hide and show some option based on change difficulty mode
     */
    $(document).on('change', '#difficulty_mode', function(e) {
        if($(this).val() == 'manual'){
            // if manual mode then hide selection for difficulty level option
            $('#difficulty_lvl').multiselect('enable');
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
            $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','unset');
            $(".question-generator-objectives-labels label:eq(1)").show();
        }else{
            // if auto mode then hide selection for difficulty level option
            $("#difficulty_lvl").val('').multiselect("rebuild").multiselect('disable');
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','hidden');
            $(".question-generator-objectives-labels label:eq(1)").hide();
        }
    });

    /**
     * USE : Hide and show some option based on change test type
     */
    $(document).on('change', '#test_type', function() {
        if(this.value == 2){
            $('#no_of_trials_per_question_section').show();
            $('#display_hints_section').show();
            $("#randomize_answer select").val("yes").change();
            $("#randomize_order select").val("yes").change();
            $('#select-randomize-answers').val('no').select2().trigger('change');
            $('#select-randomize-order').val('no').select2().trigger('change');
            $('#credit-point-rules-section').show();
        }else{
            $('#no_of_trials_per_question_section').hide();
            $('#display_hints_section').hide();
            $("#randomize_answer select").val("no").change();
            $("#randomize_order select").val("no").change();
            $('#select-randomize-answers').val('yes').select2().trigger('change');
            $('#select-randomize-order').val('yes').select2().trigger('change');
            $('#credit-point-rules-section').hide();
        }
    });

    // If type is exercise then no trials option is enabled and otherwise disabled
    if($('#test_type').find(":selected").val() == 2){
        $('#no_of_trials_per_question_section').show();
        $('#display_hints_section').show();
        $("#randomize_answer select").val("yes").change();
        $("#randomize_order select").val("yes").change();
        $('#select-randomize-answers').val('no').select2().trigger('change');
        $('#select-randomize-order').val('no').select2().trigger('change');
    }else{
        $('#no_of_trials_per_question_section').hide();
        $('#display_hints_section').hide();
        $("#randomize_answer select").val("no").change();
        $("#randomize_order select").val("no").change();
        $('#select-randomize-answers').val('yes').select2().trigger('change');
        $('#select-randomize-order').val('yes').select2().trigger('change');
    }

    /**
	 * USE : Get Learning Units from multiple strands
	 * **/
	$(document).on('change', '#strand_id,#refresh-question-strand-id', function() {
        $("#cover-spin").show();
        var classNameLearningUnit = '#learning_unit';
        if($(this).attr('id') == 'refresh-question-strand-id'){
            classNameLearningUnit = '#refresh-question-learning-unit';
        }
		$strandIds = $(this).val();
		if($strandIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningUnitFromMultipleStrands',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strands_ids': $strandIds
				},
				success: function(response) {
					$('#learning_unit').html('');
					$("#cover-spin").hide();
					var data = JSON.parse(JSON.stringify(response));
					if(data){
						if(data.data){
							// $(data.data).each(function() {
                            $.each(data.data,function(index,value) {
								var option = $('<option />');
								option.attr('value', this.id).text(this["name_"+APP_LANGUAGE]);
								option.attr('selected', 'selected');
								$(classNameLearningUnit).append(option);
							});
						}else{
							$(classNameLearningUnit).html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
						}
					}else{
						$(classNameLearningUnit).html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
					}
					$(classNameLearningUnit).multiselect("rebuild");
					$(classNameLearningUnit).trigger("change");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
            $("#cover-spin").hide();
			$('#learning_unit, #learning_objectives').html('');
			$('#learning_unit, #learning_objectives').multiselect("rebuild");
		}        
	});

    /**
     * USE : Default select all learning objectives wise difficulty levels
     */
    $(document).on('change', '#difficulty_lvl', function(){
        if($('#difficulty_mode').val() == 'manual'){
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
        }
    });

	/**
	 * USE : Get Multiple Learning units based on multiple learning units ids
	 * **/
	$(document).on('change', '#learning_unit', function() {
        $("#cover-spin").show();
        $('.learning_unit_error_msg').text('');
        $('.selection-learning-objectives-section').html('');
		$strandIds = $('#strand_id').val();
		$learningUnitIds = $('#learning_unit').val();
		if($learningUnitIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnitsInGenerateQuestions',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strand_id': $strandIds,
					'learning_unit_id': $learningUnitIds,
				},
				success: function(response) {
					$('#learning_objectives').html('');
					var data = JSON.parse(JSON.stringify(response));
                    if(data){
                        var html = '';
						if(data.data.LearningObjectives){
                            html += '<div class="selected-learning-objectives-difficulty">\
                                        <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> Select All\
                                    </div>';
							// $(data.data.LearningObjectives).each(function() {
                            $.each(data.data.LearningObjectives,function(index,value) {
                                var learningObjectivesTitle=eval('this.title_'+currentLanguage);
                                html += '<div class="selected-learning-objectives-difficulty">\
                                            <input type="checkbox" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+']" value="'+this.learning_unit_id+'" class="learning_objective_checkbox" checked>\
                                            <label>'+this.foci_number+' '+learningObjectivesTitle+'</label>';
                                            if($('#difficulty_mode').val() == 'manual'){
                                            html += '<select name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>\
                                                        <option value="1">1</option>\
                                                        <option value="2">2</option>\
                                                        <option value="3">3</option>\
                                                        <option value="4">4</option>\
                                                        <option value="5">5</option>\
                                                    </select>';
                                            }
                                            html += '<input type="text" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][get_no_of_question_learning_objectives]" value="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" class="get_no_of_question_learning_objectives" min="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" max="'+maximum_question_per_skill+'">\
                                        </div>';
							});
                            $('.selection-learning-objectives-section').html(html);
                            if($('#difficulty_mode').val() == 'manual'){
                                // Set default difficulty level for selected first steps
                                setDefaultDifficultyLevels();
                            }
						}else{
							$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
						}
					}else{
						$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
					}
                    $('.learning_objectives_difficulty_level').multiselect(multiselectArray);
                    total_no_of_questions();
                    $("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
            $("#cover-spin").hide();
            $('.learning_unit_error_msg').text(VALIDATIONS.PLEASE_SELECT_LEARNING_OBJECTIVES);
			$('#learning_objectives').html('');
			$('#learning_objectives').multiselect("rebuild");
            total_no_of_questions();
		}
	});

    /**
     * USE : Hide and show custom report date on change select report date
     */
    $(document).on('change', '#select-report-date', function() { 
        $(".custom_date").hide();
        if($(this).val() == 'custom_date'){
            $(".custom_date").show();
        }
    });

   /**
    * USE : On click event click on the grade checkbox
    */
    $(document).on('click', '.question-generator-grade-chkbox', function(){
        //$('.question-generator-class-chkbox').prop('checked',false);
        if(!$(this).is(":checked")) {
            $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').prop('checked',false);
        }
        var GradeIds = [];
        $('.question-generator-grade-chkbox').each(function(){
            if($(this).is(":checked")) {
                $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').prop('checked',true);
                GradeIds.push($(this).val());
            }
        });

        var ClassIds = [];
        $('.question-generator-class-chkbox').each(function(){
            if($(this).is(":checked")) {
                ClassIds.push($(this).val());
            }
        });
        
        // Function call to get student list
        getStudents(GradeIds,ClassIds);
        setGradeClassDateTimeList();
    });

    /**
    * USE : On click event click on the class checkbox
    */
    $(document).on('click', '.question-generator-class-chkbox', function(){
        var ClassIds = [];
        $('.question-generator-class-chkbox').each(function(){
            if($(this).is(":checked")) {
                ClassIds.push($(this).val());
            }
        });
        var GradeIds = [];
        $('.question-generator-grade-chkbox').each(function(){
            if($(this).is(":checked")) {
                GradeIds.push($(this).val());
            }
        });
        // Function call to get student list
        getStudents(GradeIds,ClassIds);
        setGradeClassDateTimeList();
    });

    /**
     * USE : On change start time event disabled end time before options
     */
    // $(document).on('change', '#test_start_time', function(){
    //     var selectedStartTimeIndex = this.selectedIndex;
    //     $.each($("#test_end_time option"), function(){
    //         var endOptionSelectedStartTimeIndex = $(this).index();
    //         if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
    //             $(this).attr("disabled", "disabled");
    //         }else{
    //             $(this).removeAttr("disabled");
    //         }
    //     });
    //     $('#test_end_time').val('').select2().trigger('change');
    // });

    /**
     * USE : On click on the select all learning objectives events
     */
    $(document).on("click", ".all_learning_objective_checkbox", function (){
        $("#cover-spin").show();
		if($(this).is(":checked")){
            $(".learning_objective_checkbox").each(function () {
                $(this).prop('checked', true);
            });
		}else{
			$(".learning_objective_checkbox").each(function () {
                $(this).prop('checked', false);
            });
		}
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        if($('#difficulty_mode').val() == 'manual'){
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
            total_no_of_questions();
        $("#cover-spin").hide();
	});
    /**
     * USE : On click on the select learning objectives events
     */
    $(document).on("click", ".learning_objective_checkbox", function (){
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        if($('#difficulty_mode').val() == 'manual'){
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
        total_no_of_questions();
    });
});

/**
 * USE : Set default difficulty level set in based on selected in first steps
 */
function setDefaultDifficultyLevels(){
    var difficultyLevels = $('#difficulty_lvl').val();
    if(difficultyLevels.length){
        $('.learning_objectives_difficulty_level').each(function(){
            $(this).val(difficultyLevels).change().multiselect(multiselectArray).multiselect('rebuild');
        });
    }
}

/**
 * USE : Get the questions from AIAPi
 */
 function getLearningObjectivesOptionList(){
    // $('.form-steps.step4 #pills-tab').html('');
    // $('.form-steps.step4 #pills-tabContent').html('');
    // $(".review-question-main").html('');
    // $('.form-steps.step4 .tab-content-wrap .error').remove();
    // $("#cover-spin").show();
    $('.form-steps.step4 #pills-tab').html('');
    $('.form-steps.step4 #pills-tabContent').html('');
    $('.form-steps.step4 .tab-content-wrap .error').remove();
    $("#cover-spin").show();
    var formData=$( "#question-generator" ).serialize();
    $.ajax({
        url: BASE_URL + '/question-generator/get-questions-id-learning-objectives-school',
        type: 'POST',
        data:formData,
        success: function(response) {
            $("#question-generator button[type=submit]").prop('disabled',false);
            var response = JSON.parse(JSON.stringify(response));
            // if(response.data){
            //     var qLength = Object.keys(response.data.questionIds).length;
            //     var total_no_of_questions = parseInt($("#total_no_of_questions").val());
            //     if(qLength < total_no_of_questions){
            //         toastr.warning(NOT_ENOUGH_QUESTIONS_INTO_SOME_OBJECTIVES);
            //     }
            //     // Set input hidden into question ids
            //     $('#questionIds').val(response.data.questionIds);
            //     $(".review-question-main").html(response.data.html);
            //     MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
            //     $('#cover-spin').hide();
            // }else{
            //     $('.form-steps').hide();
            //     $('.step-headings').removeClass('tab_active');
            //     $('.section-step3').addClass('tab_active');
            //     $('.step3').show();
            // }
            if(response.data){
                var qLength=Object.keys(response.data.questionIds).length;
                console.log(qLength);
                var total_no_of_questions=parseInt($("#total_no_of_questions").val());
                if(qLength < total_no_of_questions){
                    toastr.warning(NOT_ENOUGH_QUESTIONS_INTO_SOME_OBJECTIVES);
                }
                // Set input hidden into question ids
                $('#questionIds').val(response.data.questionIds);
                var tab_left = '';
                var tab_right = '';
                var qIndex = 1;
                $.each(response.data.question_list, function(K,Q) {
                    var tab_active = '';
                    var tab_active_contact = '';
                    if(qIndex == 1){
                        tab_active = 'active';
                        tab_active_contact = 'show active';
                    }
                    var questionTitle = eval('Q.question_'+currentLanguage);
                    //var difficultyLevelName = eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);
                    var difficultyLevelName = eval('Q.PreConfigurationDifficultyLevel.difficulty_level_name_'+currentLanguage);
                    var answer1Title = eval('Q.answers.answer1_'+currentLanguage);
                    var answer2Title = eval('Q.answers.answer2_'+currentLanguage);
                    var answer3Title = eval('Q.answers.answer3_'+currentLanguage);
                    var answer4Title = eval('Q.answers.answer4_'+currentLanguage);
                    tab_left+='<li class="nav-item">\
                        <input type="hidden" name="qIndex[]" value="'+Q.id+'" />\
                        <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
                    </li>';
                    tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                        <div class="d-flex pb-3">\
                        <div class="question-content pl-2">\
                            <div class="row">\
                                <div class="col-md-6"><b>{{__('languages.upload_document.strands')}}</b> : <span class="q-strand-name" data-q-strand-id="'+Q.objective_mapping.strand_id+'">'+Q.objective_mapping.strandName+'</span></div>\
                                <div class="col-md-6"><b>{{__('languages.upload_document.learning_units')}}</b> : <span class="q-learning-units-name" data-q-learning-units-id="'+Q.objective_mapping.learning_unit_id+'">'+Q.objective_mapping.learningUnitsName+'</span></div>\
                                <div class="col-md-6"><b>{{ __('languages.learning_objectives') }}</b> : <span class="q-learning-objectives-title" data-q-learning-objectives-id="'+Q.objective_mapping.learning_objectives_id+'">'+Q.objective_mapping.learningObjectivesTitle+'</span></div>\
                                <div class="col-md-6"><b>{{__('languages.questions.difficulty_level')}}</b> : <span class="q-difficulty-level" data-q-difficulty-level-id="'+Q.dificulaty_level+'">'+difficultyLevelName+'</span></div>\
                            </div>\
                            <div class="question-heading">\
                                <p class="question-title review-question-title">{{ __('languages.questions.question')}}:</p>\
                            </div>\
                            <div class="question-answer-content pl-2">\
                                <div class="question_content">\
                                    <label for="question-content" class="pl-3">'+'( '+Q.naming_structure_code+' )'+questionTitle+'</label>\
                                </div>\
                                <div class="answer-content">\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">1</span>\
                                        <div class="review-answer-detail">'+answer1Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">2</span>\
                                        <div class="review-answer-detail">'+answer2Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">3</span>\
                                        <div class="review-answer-detail">'+answer3Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">4</span>\
                                        <div class="review-answer-detail">'+answer4Title+'</div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="d-flex align-items-center float-left mt-5">\
                                <button type="button" class="btn-search bg-pink want_a_hint" question-id="'+Q.id+'">{{__('languages.my_studies.want_a_hint')}}</button>\
                            </div>\
                        </div>\
                        </div>\
                    </div>';
                    if(qIndex == qLength){
                        $('.form-steps.step4 #pills-tab').html(tab_left);
                        $('.form-steps.step4 #pills-tabContent').html(tab_right);
                        setTimeout(function() {
                            if(tab_right != ""){
                                $("#pills-tab li:eq(0) a").click();
                                //updateMathHtmlById("pills-tabContent");
                                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                            }
                        },1000);
                        if(tab_left == ''){
                            $('.form-steps.step4 .tab-content-wrap').append('<label class="error">'+PLEASE_RESELECT_QUESTION_CONFIGURATION+'</label>')
                            $("#question-generator button[type=submit]").prop('disabled',true);
                        }else{
                            $("#question-generator button[type=submit]").prop('disabled',false);   
                        }
                    }
                    qIndex++;
                });
                $('#cover-spin').hide();
            }else{
                $('.form-steps').hide();
                $('.step-headings').removeClass('tab_active');
                $('.section-step3').addClass('tab_active');
                $('.step3').show();
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
            $('.form-steps.step4 .tab-content-wrap').append('<label class="error">'+PLEASE_RESELECT_QUESTION_CONFIGURATION+'</label>')
            $("#question-generator button[type=submit]").prop('disabled',true);
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step3').addClass('tab_active');
            $('.step3').show();
        }
    });
}

 /**
 * USE : Get the student list based on select grades and classes
 * Trigger : on select the grades and class
 * Return data : All the student list based on select grade and classes
 */
function getStudents(gradeIds, classIds){
    $('#question-generator-student-id').prop('disabled',false);
    $('#question-generator-student-id').html('');
    $("#cover-spin").show();
    if(gradeIds.length == 0 && classIds.length == 0){
        $('#question-generator-student-id').html('');
        $("#question-generator-student-id").multiselect("rebuild");
        $("#cover-spin").hide();
        return null;
    }
    $.ajax({
        url: BASE_URL + '/question-generator/get-students-list',
        type: 'GET',
        data: {
            'gradeIds': gradeIds,
            'classIds': classIds
        },
        success: function(response) {
            $("#cover-spin").hide();
            if(response.data){
                $('#question-generator-student-id').html(response.data);
                $("#question-generator-student-id").find('option').attr('selected','selected');
                $("#question-generator-student-id").multiselect("rebuild");
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
        }
    });
    $("#cover-spin").hide();
}

function questionsReindex() {
    var qIndex=1;
    $("#pills-tab li .nav-link").each(function () {
        $(this).text('Q'+qIndex);
        qIndex++;
    })
}
$(document).ready(function () {
    $(".btn-up").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $current.prev('li');
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
        questionsReindex();
    });
    $(".btn-down").click(function(){
      var $current = $("#pills-tab li .nav-link.active").closest('li');
      var $next = $current.next('li');
      if($next.length !== 0){
        $current.insertAfter($next);
      }
        questionsReindex();
    });
    $(".set-top").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li:eq(0)");
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
        questionsReindex();
    });
    $(".set-bottom").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li").last();
        if($previous.length !== 0){
            $current.insertAfter($previous);
        }
        questionsReindex();
    });
    // $(".btn-remove-tab").click(function () {
    //     $("#pills-tab li .nav-link.active").closest('li').remove();
    //     $("#pills-tabContent .tab-pane.fade.show.active").remove();
    //     setTimeout(function () {
    //         $(document).find("#pills-tab li:eq(0) a").click();
    //     },200);
        
    // });
    $(".btn-remove-tab").click(function () {
        var QuestionCount = $('.review-question-main').find('.review-question-left-section ul li').length;
        if(QuestionCount > 1){
            $("#pills-tab li .nav-link.active").closest('li').remove();
            $("#pills-tabContent .tab-pane.fade.show.active").remove();
            setTimeout(function () {
                $(document).find("#pills-tab li:eq(0) a").click();
            },200);
        }else{
            toastr.error(VALIDATIONS.ALLOW_MINIMUM_ONE_QUESTION);
        }
    });

    $(document).on("change",".get_no_of_question_learning_objectives",function () {
        if($.trim($(this).val())=="" || $.trim($(this).val())==0){
            var minimum_question_per_skill = $(this).attr('min');
            $(this).val(minimum_question_per_skill);
        }
       total_no_of_questions();
    });

    $(document).on("click",".want_a_hint",function () {
        $("#cover-spin").show();
        var qId=$(this).attr('question-id');
        $('#WantAHintModal .modal-content').html('');
        $.ajax({
            url: BASE_URL + '/get-question-hint/'+qId,
            type: 'GET',
            success: function(response) {
                $("#cover-spin").hide();
                $('#WantAHintModal .modal-content').html(response.data.html);
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                $('#WantAHintModal').modal('show');
            }
        });
    });
// old refresh data 
    // $(document).on("click",".refresh-question-data",function () {
    //     var refresh_question_strand_id=$('#refresh-question-strand-id').val();
    //     var refresh_question_learning_unit=$('#refresh-question-learning-unit').val();
    //     var refresh_question_learning_objectives=$('#refresh-question-learning-objectives').val();
    //     var refresh_question_difficulty_level=$('#refresh_question_difficulty_level').val();
    //     var refresh_question_difficulty_mode=$('#difficulty_mode').val();
    //     var refresh_question_test_type=$('#test_type').val();
    //     if(refresh_question_strand_id.length != 0 
    //         && refresh_question_learning_unit.length != 0 
    //         && refresh_question_learning_objectives.length != 0 
    //         && refresh_question_difficulty_level.length != 0 
    //         && refresh_question_difficulty_mode.length != 0){
    //         $(".refresh-question").hide();
    //         $("#cover-spin").show();
    //         $.ajax({
    //             url: BASE_URL + '/get-refresh-question',
    //             type: 'POST',
    //             data: {
    //                 '_token': $('meta[name="csrf-token"]').attr('content'),
    //                 'strands_ids': refresh_question_strand_id,
    //                 'learning_unit_id': refresh_question_learning_unit,
    //                 'learning_objectives_id': refresh_question_learning_objectives,
    //                 'dificulty_level':refresh_question_difficulty_level,
    //                 'difficulty_mode':refresh_question_difficulty_mode,
    //                 'test_type':refresh_question_test_type
    //             },
    //             success: function(response) {
    //                 if(response.data.question_list.length != 0){
    //                     var Q = response.data.question_list[0];
    //                     var questionTitle = eval('Q.question_'+currentLanguage);
    //                     var answer1Title = eval('Q.answers.answer1_'+currentLanguage);
    //                     var answer2Title = eval('Q.answers.answer2_'+currentLanguage);
    //                     var answer3Title = eval('Q.answers.answer3_'+currentLanguage);
    //                     var answer4Title = eval('Q.answers.answer4_'+currentLanguage);
    //                     //var difficultyLevelName = eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);
    //                     var difficultyLevelName = eval('Q.PreConfigurationDifficultyLevel.difficulty_level_name_'+currentLanguage);

    //                     $("#refresh-question-id").val(Q.id);
    //                     $(".refresh-question .question-strand-detail span").text(Q.objective_mapping.strandName);
    //                     $(".refresh-question .question-learning-unit-detail span").text(Q.objective_mapping.learningUnitsName);
    //                     $(".refresh-question .question-learning-objectives-detail span").text(Q.objective_mapping.learningObjectivesTitle);
    //                     $(".refresh-question .question-difficulty-detail span").text(difficultyLevelName);
    //                     $(".refresh-question .question_content label").html(questionTitle);
    //                     $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail p").html(answer1Title);
    //                     $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail p").html(answer2Title);
    //                     $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail p").html(answer3Title);
    //                     $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail p").html(answer4Title);
    //                     $(".refresh-question .want_a_hint").attr("question-id",Q.id);
    //                 }
    //                 $(".refresh-question").show();
    //                 //updateMathHtmlById("pills-tabContent");
    //                 MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
    //                 $("#cover-spin").hide();
    //             },
    //             error: function(response) {
    //                 ErrorHandlingMessage(response);
    //             }
    //         });
    //     }else{
    //         toastr.error(SELECT_REFRESH_QUESTION_CONFIGURATION);
    //     }
    // });

    // new flow on right side  refresh data then new question get.
    $(document).on("click",".refresh-question-data",function () {
        var refresh_question_strand_id=$('#refresh-question-strand-id').val();
        var refresh_question_learning_unit=$('#refresh-question-learning-unit').val();
        var refresh_question_learning_objectives=$('#refresh-question-learning-objectives').val();
        var refresh_question_difficulty_level=$('#refresh_question_difficulty_level').val();
        var refresh_question_difficulty_mode=$('#difficulty_mode').val();
        var refresh_question_test_type=$('#test_type').val();
        var old_question_ids=[];
        $('input[name="qIndex[]"]').each(function () {
            old_question_ids.push($(this).val());
        });
        if(
            refresh_question_strand_id.length != 0
            && refresh_question_learning_unit.length != 0 
            && refresh_question_learning_objectives.length != 0 
            && refresh_question_difficulty_level.length != 0 
            && refresh_question_difficulty_mode.length != 0){
            $(".refresh-question").hide();
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + '/get-refresh-question',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'strands_ids': refresh_question_strand_id,
                    'learning_unit_id': refresh_question_learning_unit,
                    'learning_objectives_id': refresh_question_learning_objectives,
                    'dificulty_level':refresh_question_difficulty_level,
                    'difficulty_mode':refresh_question_difficulty_mode,
                    'test_type':refresh_question_test_type,
                    'old_question_ids':old_question_ids
                },
                success: function(response) {
                    if(response.data.question_list.length != 0){
                        var Q = response.data.question_list[0];
                        var questionTitle = eval('Q.question_'+currentLanguage);
                        var answer1Title = eval('Q.answers.answer1_'+currentLanguage);
                        var answer2Title = eval('Q.answers.answer2_'+currentLanguage);
                        var answer3Title = eval('Q.answers.answer3_'+currentLanguage);
                        var answer4Title = eval('Q.answers.answer4_'+currentLanguage);
                        //var difficultyLevelName = eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);
                        var difficultyLevelName = eval('Q.PreConfigurationDifficultyLevel.difficulty_level_name_'+currentLanguage);
                        
                        $("#refresh-question-id").val(Q.id);

                        $(".refresh-question .question-strand-detail span").attr('data-q-strand-id',Q.objective_mapping.strand_id);
                        $(".refresh-question .question-strand-detail span").text(Q.objective_mapping.strandName);

                        $(".refresh-question .question-learning-unit-detail span").attr('data-q-learning-units-id',Q.objective_mapping.learning_unit_id);
                        $(".refresh-question .question-learning-unit-detail span").text(Q.objective_mapping.learningUnitsName);

                        $(".refresh-question .question-learning-objectives-detail span").attr('data-q-learning-objectives-id',Q.objective_mapping.learning_objectives_id);
                        $(".refresh-question .question-learning-objectives-detail span").text(Q.objective_mapping.learningObjectivesTitle);

                        $(".refresh-question .question-difficulty-detail span").attr('data-q-difficulty-level-id',Q.dificulaty_level);
                        $(".refresh-question .question-difficulty-detail span").text(difficultyLevelName);

                        $(".refresh-question .question_content label").html('( '+Q.naming_structure_code+' )'+questionTitle);
                        $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail p").html(answer1Title);
                        $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail p").html(answer2Title);
                        $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail p").html(answer3Title);
                        $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail p").html(answer4Title);
                        $(".refresh-question .want_a_hint").attr("question-id",Q.id);

                    }
                    $(".refresh-question").show();
                    //updateMathHtmlById("pills-tabContent");
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                    $("#cover-spin").hide();
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        }else{
            toastr.error(SELECT_REFRESH_QUESTION_CONFIGURATION);
        }
    });
    // this click to get Refresh Question and Replace Question in active Question
    $(document).on("click",".replace-question",function () {
        var StrandId = $(".refresh-question .question-strand-detail span").attr('data-q-strand-id');
        var strandName = $(".refresh-question .question-strand-detail span").text();
        var learningUnitsId = $(".refresh-question .question-learning-unit-detail span").attr('data-q-learning-units-id');
        var learningUnitsName = $(".refresh-question .question-learning-unit-detail span").text();
        var learningObjectivesId = $(".refresh-question .question-learning-objectives-detail span").attr('data-q-learning-objectives-id');
        var learningObjectivesTitle = $(".refresh-question .question-learning-objectives-detail span").text();
        var difficulty_level = $(".refresh-question .question-difficulty-detail span").attr('data-q-difficulty-level-id');
        var difficulty_level_name_en = $(".refresh-question .question-difficulty-detail span").text();
        var question_en = $(".refresh-question .question_content label").html();
        var answer1_en = $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en = $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en = $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en = $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id = $("#refresh-question-id").val();

        if($("#pills-tab li input[value="+refresh_question_id+"]").length == 0){
            $(".review-question-tab .tab-pane.show.active .q-strand-name").attr('data-q-strand-id',StrandId);
            $(".review-question-tab .tab-pane.show.active .q-strand-name").text(strandName);

            $(".review-question-tab .tab-pane.show.active .q-learning-units-name").attr('data-q-learning-units-id',learningUnitsId);
            $(".review-question-tab .tab-pane.show.active .q-learning-units-name").text(learningUnitsName);

            $(".review-question-tab .tab-pane.show.active .q-learning-objectives-title").attr('data-q-learning-objectives-id',learningObjectivesId);
            $(".review-question-tab .tab-pane.show.active .q-learning-objectives-title").text(learningObjectivesTitle);

            $(".review-question-tab .tab-pane.show.active .q-difficulty-level").attr('data-q-difficulty-level-id',difficulty_level);
            $(".review-question-tab .tab-pane.show.active .q-difficulty-level").text(difficulty_level_name_en);

            $(".review-question-tab .tab-pane.show.active .question_content label").html(question_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(0) .review-answer-detail").html(answer1_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(1) .review-answer-detail").html(answer2_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(2) .review-answer-detail").html(answer3_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(3) .review-answer-detail").html(answer4_en);
            $("#pills-tab li .nav-link.active").parent().find('input').val(refresh_question_id)
            $(".want_a_hint").attr("question-id",refresh_question_id);
        }else{
            toastr.error(THIS_QUESTION_ALREADY_EXISTS);
        }
    });
    
    // this click to get Refresh Question and add Question in Question list
    $(document).on("click",".add-question",function () {
        var StrandId = $(".refresh-question .question-strand-detail span").attr('data-q-strand-id');
        var strandName = $(".refresh-question .question-strand-detail span").text();

        var learningUnitsId = $(".refresh-question .question-learning-unit-detail span").attr('data-q-learning-units-id');
        var learningUnitsName = $(".refresh-question .question-learning-unit-detail span").text();

        var learningObjectivesId = $(".refresh-question .question-learning-objectives-detail span").attr('data-q-learning-objectives-id');
        var learningObjectivesTitle = $(".refresh-question .question-learning-objectives-detail span").text();

        var difficulty_level = $(".refresh-question .question-difficulty-detail span").attr('data-q-difficulty-level-id');
        var difficulty_level_name_en = $(".refresh-question .question-difficulty-detail span").text();

        var question_en = $(".refresh-question .question_content label").html();
        var answer1_en = $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en = $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en = $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en = $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id = $("#refresh-question-id").val();
        if($("#pills-tab li input[value="+refresh_question_id+"]").length == 0){
            var tab_left = '';
            var tab_right = '';
            var tab_active = '';
            var tab_active_contact = '';
            var getIndex = $("#pills-tab li").last().find('a').attr('aria-controls');
            var qIndex = parseInt(getIndex.replace('pills-contact-',''));
            qIndex = qIndex+1;
            tab_left+='<li class="nav-item">\
                <input type="hidden" name="qIndex[]" value="'+refresh_question_id+'" />\
                <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
            </li>';
            tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                <div class="d-flex pb-3">\
                <div class="question-content pl-2">\
                    <div class="row">\
                        <div class="col-md-6"><b>Strand</b> : <span class="q-strand-name" data-q-strand-id="'+StrandId+'">'+strandName+'</span></div>\
                        <div class="col-md-6"><b>Learning Units</b> : <span class="q-learning-units-name" data-q-learning-units-id="'+learningUnitsId+'">'+learningUnitsName+'</span></div>\
                        <div class="col-md-6"><b>Learning Objectives</b> : <span class="q-learning-objectives-title" data-q-learning-objectives-id="'+learningObjectivesId+'">'+learningObjectivesTitle+'</span></div>\
                        <div class="col-md-6"><b>Difficulty Level</b> : <span class="q-difficulty-level" data-q-difficulty-level-id="'+difficulty_level+'">'+difficulty_level_name_en+'</span></div>\
                    </div>\
                    <p class="question-title review-question-title">Question:</p>\
                    <div class="question-answer-content pl-2">\
                        <div class="d-flex question_content">\
                            <label for="question-content">'+question_en+'</label>\
                        </div>\
                        <div class="answer-content">\
                            <div class="answer-review">\
                                <span class="answer-detail-number">1</span>\
                                <div class="review-answer-detail">'+answer1_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">2</span>\
                                <div class="review-answer-detail">'+answer2_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">3</span>\
                                <div class="review-answer-detail">'+answer3_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">4</span>\
                                <div class="review-answer-detail">'+answer4_en+'</div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="d-flex align-items-center float-left mt-5">\
                        <button type="button" class="btn-search bg-pink want_a_hint" question-id="'+refresh_question_id+'">{{__('languages.my_studies.want_a_hint')}}</button>\
                    </div>\
                </div>\
                </div>\
            </div>';
            $('.form-steps.step4 #pills-tab li a.nav-link.active').parent().after(tab_left);
            $('.form-steps.step4 #pills-tabContent .tab-pane.fade.active').after(tab_right);
            questionsReindex();
        }else{
            toastr.error(THIS_QUESTION_ALREADY_EXISTS);
        }
    });

    // this click to get Strands, Learning Units, Learning Objectives and Difficulty Level set in Refresh Question
    $(document).on("click","#pills-tab li a",function () {
        var getIndex = $(this).attr('aria-controls');
        var qIndex = parseInt(getIndex.replace('pills-contact-',''));
        var qId = qIndex;
        var q_strand_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-strand-name').attr('data-q-strand-id');
        var q_learning_units_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-learning-units-name').attr('data-q-learning-units-id');
        var q_learning_objectives_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-learning-objectives-title').attr('data-q-learning-objectives-id');
        var q_difficulty_level_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-difficulty-level').attr('data-q-difficulty-level-id');
        $("#refresh-question-strand-id").val(q_strand_id).multiselect('rebuild');
        $("#refresh-question-learning-unit").val(q_learning_units_id).multiselect('rebuild');
        $("#refresh-question-learning-objectives").val(q_learning_objectives_id).multiselect('rebuild');
        $("#refresh_question_difficulty_level").val(q_difficulty_level_id).multiselect('rebuild');
    });
})

function checkValidation(currentStep) {
    var formIsValid = 0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    switch('step_'+currentStep){
        case 'step_1':
            var addValid = '';
            if($("#difficulty_mode").val() == 'manual'){
                addValid=',[name="difficulty_lvl[]"]';
            }
            $('.form-steps.step'+currentStep).find('[name=use_of_modes],[name=test_type],[name=title],[name=start_date],[name=end_date]'+addValid).each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none' ){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">'+PLEASE_ENTER+label+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_2':
            $('.form-steps.step'+currentStep).find('[name="studentIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();                    
                    formIsValid++;
                }
            });
            $('.form-steps.step'+currentStep).find('[name="peerGroupIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    formIsValid++;
                }
            });
            
            if(formIsValid == 2){
                $('.form-steps.step'+currentStep).find('[name="studentIds[]"]').parent().append('<label class="error">'+VALIDATIONS.PLEASE_SELECT_STUDENTS_OR_PEER_GROUP+' </label>');
                $('.form-steps.step'+currentStep).find('[name="peerGroupIds[]"]').parent().append('<label class="error w-100">'+VALIDATIONS.PLEASE_SELECT_STUDENTS_OR_PEER_GROUP+'</label>');
                formIsValid++;
            }else{
                formIsValid = 0;
            }
            break;
        case 'step_3':
            break;
        case 'step_4':
            break;
        default:
    }

    // Check the steps all field is completed then next step open
    if(formIsValid == 0){
        return true;
    }else{
        return false;
    }
}
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
        total_no_of_questions();
        $('#question-generator-student-id,.form-grade-select-section').change(function () {
            setTimeout(function () {
                if($("#question-generator-student-id").val().length != 0){
                    $("#question-generator-peer-group-options").multiselect('clearSelection');
                    $("#question-generator-peer-group-options").multiselect({
                        enableHTML: true,
                        templates: {
                            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                            filterClearBtn:'<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                        },
                        columns: 1,
                        placeholder: SELECT_PEER_GROUP,
                        search: true,
                        selectAll: true,
                        includeSelectAllOption: true,
                        enableFiltering: true,
                    });
                    $("#question-generator-peer-group-options").multiselect('disable');
                }else{
                    $("#question-generator-peer-group-options").multiselect('enable');   
                }
            },700)
        });

        $('#question-generator-peer-group-options').change(function () {
            if($(this).val().length != 0){
                $("#question-generator-student-id").multiselect('clearSelection');
                $("#question-generator-student-id").multiselect('disable');
            }else{
                if($('.question-generator-grade-chkbox:checked').length == 0){
                    $('#question-generator-student-id').prop('disabled',true);
                    $("#question-generator-student-id").multiselect('disable');
                }
            }
            setGroupDateTimeList();
        });
        $('#question-generator-student-id').change();
        $(document).on('change',".grade-class-date-time-list .startDate",function () {
            var startdata = $(this).val();
            $(this).closest('.form-row').find('.endDate').datepicker('option', 'minDate',startdata);
        });
        $(document).on('change',".grade-class-date-time-list .endDate",function () {
            var enddata = $(this).val();
            $(this).closest('.form-row').find('.startDate').datepicker('option', 'maxDate',enddata);
        });

        $(document).on('change',".grade-class-date-time-list .start_time,.group-date-time-list .start_time",function () {
            var selectedStartTimeIndex = this.selectedIndex;
            var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
            $(this).closest('.form-row').find(".end_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if((endOptionSelectedStartTimeIndex >= selectedEndTimeIndex ) && selectedEndTimeIndex > 0){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });
        });

    /**
     * USE : On change start Date end time Manage if Start time is Selected
     */
     $(document).on('change', '.date-picker', function(){
        $startDate = $(this).val();
        $endDate = $('.end-date-picker').val();
        $startTime = $('#test_start_time').val();
        if($startTime != ''){
            if($startDate != $endDate){
                var endOptionSelectedStartTimeIndex = $("#test_start_time option").index();
                var selectedStartTimeIndex = $("#test_start_time").selectedIndex;
                $("#test_end_time option").removeAttr("disabled");
            }else{
                var selectedStartTimeIndex = $("#test_start_time").prop('selectedIndex');
                $.each($("#test_end_time option"), function(index,element){
                    var endOptionSelectedStartTimeIndex = index;
                    if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
                        $(this).attr("disabled", "disabled");
                    }
                });
            }
        }
    });

        $(document).on('change',".grade-class-date-time-list .end_time,.group-date-time-list .end_time",function () {
            var selectedStartTimeIndex = this.selectedIndex;
            var selectedEndTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
            $(this).closest('.form-row').find(".start_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex >= selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if(endOptionSelectedStartTimeIndex < selectedEndTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });
        });
        if($('.question-generator-grade-chkbox:checked').length != 0){
            setGradeClassDateTimeList();
        }
        if($('#question-generator-peer-group-options option:selected').length != 0){
            setGroupDateTimeList();
        }
        $("#test_start_time,#test_end_time,input[name=start_date],input[name=end_date]").change(function () {
            setGradeClassDateTimeList();
            setGroupDateTimeList();
        });
    });

    function total_no_of_questions() {
        $("#total_no_of_questions").val(0);
        if($('.get_no_of_question_learning_objectives:not(:disabled)').length != 0){
            var total_data = $('.get_no_of_question_learning_objectives:not(:disabled)').map((_,el) => el.value).get();
            var total_data_sum = total_data.reduce((x, y) => parseInt(x) + parseInt(y));
            $("#total_no_of_questions").val(total_data_sum);
        }
    }

    // Grade Class data time
    function setGradeClassDateTimeList() {
        $(".group-date-time-list").html('');
        $(".grade-class-date-time-list").html('');
        var testStartTimeHtml=$('#test_start_time').html();
        var testEndTimeHtml=$('#test_end_time').html();
        if($('.question-generator-grade-chkbox:checked').length == 0){
            $('#question-generator-student-id').prop('disabled',true);
            $("#question-generator-student-id").multiselect('disable');
        }
        var htmlData='';
        $('.question-generator-grade-chkbox').each(function(){
            var generatorValue=$(this).val();
            if($(this).is(":checked")) {
                var generatorClassChkboxLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').length;
                var generatorClassChkboxAllLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').length;
                if(generatorClassChkboxLength == 0){
                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').each(function(){
                        var generatorClassValue = $(this).val();
                        htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                    });
                }else{
                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
                        var generatorClassValue=$(this).val();
                        htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                    });
                }
            }else{
                $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
                    var generatorClassValue=$(this).val();
                    htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                });
            }
        });

        if(htmlData == ''){
            $('.question-generator-class-chkbox:checked').each(function(){
                var generatorValue=$(this).closest('.form-grade-select').find('.question-generator-grade-chkbox').val();
                var generatorClassValue=$(this).val();
                htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
            });
        }

        $(".grade-class-date-time-list").html(htmlData);
        var mainStartDate = $("input[name=start_date]").val();
        var mainEndDate = $("input[name=end_date]").val();
        $(".date-picker-stud").datepicker({
            dateFormat: "dd/mm/yy",
            minDate:mainStartDate,
            maxDate:mainEndDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        var selectedStartTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
        var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
        $(".grade-class-date-time-list .end_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex > 0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });

        $(".grade-class-date-time-list .start_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex > 0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        $(".grade-class-date-time-list .start_time").val($('#test_start_time').val());
        $(".grade-class-date-time-list .end_time").val($('#test_end_time').val());
    }

    // Group data time
    function setGroupDateTimeList() {
        $(".grade-class-date-time-list").html('');
        $(".group-date-time-list").html('');
        var testStartTimeHtml = $('#test_start_time').html();
        var testEndTimeHtml = $('#test_end_time').html();
        var htmlData = '';
        $('#question-generator-peer-group-options option:selected').each(function(){
            var generatorGroupValue=$(this).attr('value');
            htmlData+=groupDateTimeList($(this),generatorGroupValue,testStartTimeHtml,testEndTimeHtml);
        });
        $(".group-date-time-list").html(htmlData);
        var mainStartDate = $("input[name=start_date]").val();
        var mainEndDate = $("input[name=end_date]").val();

        $(".date-picker-stud").datepicker({
            dateFormat: "dd/mm/yy",
            minDate:mainStartDate,
            maxDate:mainEndDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        var selectedStartTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
        var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
        $(".group-date-time-list .end_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex > 0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });

        $(".group-date-time-list .start_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex > 0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        $(".group-date-time-list .start_time").val($('#test_start_time').val());
        $(".group-date-time-list .end_time").val($('#test_end_time').val());
    }

    //generator  Grade-Classes  date and time html
    function dateTimeList(E,generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml){
        var mainStartDate=$("input[name=start_date]").val();
        var mainEndDate=$("input[name=end_date]").val();
        dataHtmlData='<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorClassValue+'" name="generator_class_start_date['+generatorValue+']['+generatorClassValue+']" value="'+mainStartDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_class_start_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option start_time">'+testStartTimeHtml+'</select>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud endDate" name="generator_class_end_date['+generatorValue+']['+generatorClassValue+']" value="'+mainEndDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_class_end_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option end_time">'+testEndTimeHtml+'</select>\
                </div>\
            </div>\
        </div></div><div class="col-md-12"><hr></div></div>';
        return dataHtmlData;
    }

    //generator Group date and time html
    function groupDateTimeList(E,generatorGroupValue,testStartTimeHtml,testEndTimeHtml){
        var mainStartDate = $("input[name=start_date]").val();
        var mainEndDate = $("input[name=end_date]").val();
        dataHtmlData = '<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
                            <div class="form-group col-md-3 mb-50">\
                                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
                                <div class="input-group date">\
                                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorGroupValue+'" name="generator_group_start_date['+generatorGroupValue+']" value="'+mainStartDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">\
                                    <div class="input-group-addon input-group-append">\
                                        <div class="input-group-text">\
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="form-group col-md-3 mb-50">\
                                <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>\
                                <div class="input-group date">\
                                    <select name="generator_group_start_time['+generatorGroupValue+']" class="form-control select-option start_time">'+testStartTimeHtml+'</select>\
                                </div>\
                            </div>\
                            <div class="form-group col-md-3 mb-50">\
                                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
                                <div class="input-group date">\
                                    <input type="text" class="form-control date-picker-stud endDate" name="generator_group_end_date['+generatorGroupValue+']" value="'+mainEndDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">\
                                    <div class="input-group-addon input-group-append">\
                                        <div class="input-group-text">\
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="form-group col-md-3 mb-50">\
                                <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>\
                                <div class="input-group date">\
                                    <select name="generator_group_end_time['+generatorGroupValue+']" class="form-control select-option end_time">'+testEndTimeHtml+'</select>\
                                </div>\
                            </div>\
                        </div></div><div class="col-md-12"><hr></div></div>';
        return dataHtmlData;
    }
</script>
@include('backend.layouts.footer')
@endsection