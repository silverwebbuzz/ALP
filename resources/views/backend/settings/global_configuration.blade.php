@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.global_configurations')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
                            <form class="global-configuration-form" id="global-configuration-form" method="post" action="{{ route('global-configuration') }}" enctype='multipart/form-data'>
                                @csrf()
                                @method('PATCH')
                                <div class="form-row select-data">
                                    {{-- <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.maximum_ability_history')}}</label>
                                        <input type="text" class="form-control" name="maximum_ability_history" value="{{$ConfigurationArray['maximum_ability_history'] ?? ''}}" placeholder="{{__('languages.maximum_ability_history')}}">
                                    </div> --}}
                                    {{-- <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.minimum_ability_history')}}</label>
                                        <input type="text" class="form-control" name="minimum_ability_history" value="{{$ConfigurationArray['minimum_ability_history'] ?? ''}}" placeholder="{{__('languages.minimum_ability_history')}}">
                                    </div> --}}
                                    <div class="form-group col-md-4 mb-50">
                                        <label class="text-bold-600"> {{__('languages.school_year')}}</label>
                                        <select name="current_curriculum_year" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="current_curriculum_year">
                                            @if(!empty($getCurriculumYear))
                                            <option value=""> {{__('languages.school_year')}}</option>
                                                @foreach($getCurriculumYear as $curriculumYear)
                                                    <option value="{{$curriculumYear->id}}" @if(isset($ConfigurationArray['current_curriculum_year']) && $ConfigurationArray['current_curriculum_year'] == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                                                @endforeach
                                            @else
                                                <option value=""> {{__('languages.school_year')}}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.max_trial_attempt')}}</label>
                                        <input type="text" class="form-control" name="maximum_trials_attempt" value="{{$ConfigurationArray['maximum_trials_attempt'] ?? ''}}" placeholder="{{__('languages.maximum_trials_attempt')}}">
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.default_second_per_question')}}</label>
                                        <input type="text" class="form-control" name="default_second_per_question" value="{{$ConfigurationArray['default_second_per_question'] ?? ''}}" placeholder="{{__('languages.default_second_per_question')}}">
                                    </div>
                                    <div class="form-group col-md-4 mb-50">
                                        <label class="text-bold-600">{{__('languages.difficulty_used')}}</label>
                                        <select name="difficulty_selection_type" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="pass_only_and_or">
                                            <option value="1" @if(isset($ConfigurationArray['difficulty_selection_type']) && $ConfigurationArray['difficulty_selection_type'] == '1') selected @endif>{{__('languages.pre_defined_difficulty')}}</option>
                                            <option value="2" @if(isset($ConfigurationArray['difficulty_selection_type']) && $ConfigurationArray['difficulty_selection_type'] == '2') selected @endif>{{__('languages.ai_difficulty')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.question_generator_configurations')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.min_no_of_question_per_learning_skill')}}</label>
                                                <input type="text" class="form-control" name="no_of_questions_per_learning_skills" value="{{$ConfigurationArray['no_of_questions_per_learning_skills'] ?? ''}}" placeholder="{{__('languages.min_no_of_question_per_learning_skill')}}">
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.max_no_of_question_per_learning_objective')}}</label>
                                                <input type="text" class="form-control" name="max_no_question_per_learning_objectives" value="{{$ConfigurationArray['max_no_question_per_learning_objectives'] ?? ''}}" placeholder="{{__('languages.max_no_of_question_per_learning_objective')}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.n')}}</label>
                                                <input type="text" class="form-control" name="question_generator_n" value="{{$ConfigurationArray['question_generator_n'] ?? ''}}" placeholder="{{__('languages.question_generator_n')}}">
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.repeated_rate')}}</label>
                                                <input type="text" class="form-control" name="repeated_rate" value="{{$ConfigurationArray['repeated_rate'] ?? ''}}" placeholder="{{__('languages.repeated_rate')}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.learning_unit_progress_status_colors')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.achieved')}}</label>
                                                <input type="color" id="accomplished_objective" name="accomplished_objective" value="{{$ConfigurationArray['accomplished_objective'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.to_be_achieved')}}</label>
                                                <input type="color" id="not_accomplished_objective" name="not_accomplished_objective" value="{{$ConfigurationArray['not_accomplished_objective'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.result_color')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.correct')}}</label>
                                                <input type="color" id="question_correct_color" name="question_correct_color" value="{{$ConfigurationArray['question_correct_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.incorrect')}}</label>
                                                <input type="color" id="question_incorrect_color" name="question_incorrect_color" value="{{$ConfigurationArray['question_incorrect_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <!-- Start This is to define the Criteria of the Status "Passed" of a Learning Objectives. -->
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.learning_progress_report_configurations')}}</legend>
                                    <p>{{__('languages.update_learning_progress_report_not')}}</p>
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.status_achieved')}}</label>
                                                <select name="study_status_master" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="study_status_master">
                                                    <option value="">{{__('languages.select_study_status')}}</option>
                                                    <option value="struggling" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'struggling') selected @endif>{{__('languages.struggling')}}</option>
                                                    <option value="beginning" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'beginning') selected @endif>{{__('languages.beginning')}}</option>
                                                    <option value="approaching" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'approaching') selected @endif>{{__('languages.approaching')}}</option>
                                                    <option value="proficient" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'proficient') selected @endif>{{__('languages.proficient')}}</option>
                                                    <option value="advanced" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'advanced') selected @endif>{{__('languages.advanced')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <fieldset>
                                            <legend class="global-config-title">{{__('languages.questions_per_learning_objective')}}</legend> 
                                            <div class="form-row main-config-section">
                                                <div class="row">
                                                    <div class="form-group col-md-6 mb-50">
                                                        <label class="text-bold-600">{{__('languages.minimum_questions')}}</label>
                                                        <input type="text" class="form-control" name="min_no_question_per_study_progress" value="{{$ConfigurationArray['min_no_question_per_study_progress'] ?? ''}}" placeholder="{{__('languages.min_no_question_per_study_progress')}}">
                                                    </div>
                                                    <div class="form-group col-md-6 mb-50">
                                                        <label class="text-bold-600">{{__('languages.history_window_size')}}</label>
                                                        <input type="text" class="form-control" name="question_window_size_of_learning_objective" value="{{$ConfigurationArray['question_window_size_of_learning_objective'] ?? ''}}" placeholder="{{__('languages.question_window_size_of_learning_objective')}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </fieldset>
                                
                                <!-- End This is to define the Criteria of the Status "Passed" of a Learning Objectives. -->                                
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.study_status_ability_configurations')}}</legend>
                                    <p>{{__('languages.update_learning_progress_report_not')}}</p>
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.struggling')}}</label>
                                            </div>
                                            @php 
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['struggling']) && !empty($ConfigurationArray['struggling'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['struggling']);
                                                }
                                            @endphp
                                            
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control" name="struggling_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control" name="struggling_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="struggling_color" name="struggling_color" value="{{$ConfigurationArray['struggling_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.beginning')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['beginning']) && !empty($ConfigurationArray['beginning'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['beginning']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="beginning_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="beginning_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="beginning_color" name="beginning_color" value="{{$ConfigurationArray['beginning_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.approaching')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['approaching']) && !empty($ConfigurationArray['approaching'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['approaching']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="approaching_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="approaching_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="approaching_color" name="approaching_color" value="{{$ConfigurationArray['approaching_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.proficient')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['proficient']) && !empty($ConfigurationArray['proficient'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['proficient']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="proficient_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="proficient_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="proficient_color" name="proficient_color" value="{{$ConfigurationArray['proficient_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.advanced')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['advanced']) && !empty($ConfigurationArray['advanced'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['advanced']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="advanced_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="advanced_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="advanced_color" name="advanced_color" value="{{$ConfigurationArray['advanced_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.incomplete')}}</label>
                                            </div>
                                            <div class="form-group col-md-4"></div>
                                            <div class="form-group col-md-4"></div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="incomplete_color" name="incomplete_color" value="{{$ConfigurationArray['incomplete_color'] ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <!-- Start credit configurations -->
                                <fieldset class="credit-points-assign-section">
                                    <legend class="global-config-title">{{__('languages.credit_configurations')}}</legend>
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.exercise')}}</h5>
                                                <div class="row" style="margin-left: 24px;">
                                                    <div class="col-md-12">
                                                        <h6 class="float-left font-weight-bold">{{__('languages.credit_points_for_submission_on_time')}}:</h6>  
                                                        <input type="text" name="assignment_credit_points_for_submission_on_time" class="form-control digits w-50 float-left ml-2" placeholder="{{__('languages.enter_credit_points_for_submission')}}" value="{{$ConfigurationArray['assignment_credit_points_for_submission_on_time'] ?? ''}}" style="">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['assignment_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                <input type="text" name="assignment_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}}" value="{{$ConfigurationArray['assignment_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                <input type="text" name="assignment_accuracy_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}}" value="{{$ConfigurationArray['assignment_accuracy_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                <input type="text" name="assignment_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}}" value="{{$ConfigurationArray['assignment_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                <input type="text" name="assignment_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['assignment_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['assignment_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                <input type="text" name="assignment_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}}" value="{{$ConfigurationArray['assignment_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_ability_number_of_stages_to_earn_extra_credit_point" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}}" value="{{$ConfigurationArray['assignment_ability_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                <input type="text" name="assignment_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}}" value="{{$ConfigurationArray['assignment_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                <input type="text" name="assignment_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['assignment_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.self_learning')}}</h5>
                                                <div class="row" style="margin-left: 24px;">
                                                    <div class="col-md-12">
                                                        <h6 class="float-left font-weight-bold">{{__('languages.minimum_of_questions_in_an_exercise_to_enable_credit_earning')}}:</h6>  
                                                        <input type="text" name="self_learning_credit_points_for_exercise" class="form-control digits w-50 float-left ml-2" placeholder="{{__('languages.enter_credit_points_for_exercise')}}" value="{{$ConfigurationArray['self_learning_credit_points_for_exercise'] ?? ''}}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_exercise_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                <input type="text" name="self_learning_exercise_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}}" value="{{$ConfigurationArray['self_learning_exercise_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                <input type="text" name="self_learning_exercise_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}}" value="{{$ConfigurationArray['self_learning_exercise_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                <input type="text" name="self_learning_exercise_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}}" value="{{$ConfigurationArray['self_learning_exercise_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                <input type="text" name="self_learning_exercise_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['self_learning_exercise_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_exercise_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}} " value="{{$ConfigurationArray['self_learning_exercise_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_number_of_stages_to_earn_extra_credit_points" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}} " value="{{$ConfigurationArray['self_learning_exercise_number_of_stages_to_earn_extra_credit_points'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}} " value="{{$ConfigurationArray['self_learning_exercise_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                <input type="text" name="self_learning_exercise_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_exercise_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.common_sidebar.ai_based_assessment')}}</h5>
                                                <div class="row" style="margin-left: 24px;">
                                                    <div class="col-md-12">
                                                        <h6 class="float-left font-weight-bold">{{__('languages.minimum_of_questions_in_an_test_to_enable_credit_earning')}}:</h6>  
                                                        <input type="text" name="self_learning_credit_points_for_test" class="form-control digits w-50 float-left ml-2" placeholder="{{__('languages.enter_credit_points_for_test')}}" value="{{$ConfigurationArray['self_learning_credit_points_for_test'] ?? ''}}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_test_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_test_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                <input type="text" name="self_learning_test_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}} " value="{{$ConfigurationArray['self_learning_test_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                <input type="text" name="self_learning_test_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}} " value="{{$ConfigurationArray['self_learning_test_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                <input type="text" name="self_learning_test_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}} " value="{{$ConfigurationArray['self_learning_test_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                <input type="text" name="self_learning_test_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_test_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_test_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_test_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                <input type="text" name="self_learning_test_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}} " value="{{$ConfigurationArray['self_learning_test_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                <input type="text" name="self_learning_test_number_of_stages_to_earn_extra_credit_points" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}} " value="{{$ConfigurationArray['self_learning_test_number_of_stages_to_earn_extra_credit_points'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                <input type="text" name="self_learning_test_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}} " value="{{$ConfigurationArray['self_learning_test_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: circle;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                <input type="text" name="self_learning_test_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_test_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.default_selection_for_credit_point_rules')}}</h5>
                                                <ol>
                                                    <li>
                                                        <h6 class="font-weight-bold">{{__('languages.exercise')}}:</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <div class="row">
                                                                    <div class="col-md-12 credit_point_rules_default_option">
                                                                        <label>{{__('languages.submission_on_time')}}:</label>
                                                                        <input type="radio" name="submission_on_time" value="yes"
                                                                        @if(isset($ConfigurationArray["submission_on_time"]) && $ConfigurationArray["submission_on_time"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        ><span>{{__('languages.question_generators_menu.yes')}}</span>
                                                                        <input type="radio" name="submission_on_time" value="no"
                                                                        @if(isset($ConfigurationArray["submission_on_time"]) && $ConfigurationArray["submission_on_time"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["submission_on_time"])) checked
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
                                                                        @if(isset($ConfigurationArray["credit_points_of_accuracy"]) && $ConfigurationArray["credit_points_of_accuracy"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        >{{__('languages.question_generators_menu.yes')}}
                                                                        <input type="radio" name="credit_points_of_accuracy" value="no"
                                                                        @if(isset($ConfigurationArray["credit_points_of_accuracy"]) && $ConfigurationArray["credit_points_of_accuracy"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["credit_points_of_accuracy"])) checked
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
                                                                        @if(isset($ConfigurationArray["credit_points_of_normalized_ability"]) && $ConfigurationArray["credit_points_of_normalized_ability"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        >{{__('languages.question_generators_menu.yes')}}
                                                                        <input type="radio" name="credit_points_of_normalized_ability" value="no" 
                                                                        @if(isset($ConfigurationArray["credit_points_of_normalized_ability"]) && $ConfigurationArray["credit_points_of_normalized_ability"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["credit_points_of_normalized_ability"])) checked
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
                                    </div>
                                </fieldset>
                                <!-- End credit points system rules configurations -->

                                {{-- AI Calibration --}}
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.ai_calibration_configurations')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.completion_criteria')}} (%)</label>
                                                <input type="text" name="ai_calibration_percentage" class="form-control" placeholder="{{__('languages.ai_calibration')}} (%)" value="{{$ConfigurationArray['ai_calibration_percentage'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.exclude_ai_calibration_question_limit')}}</label>
                                                <input type="text" name="exclude_ai_calibration_question_limit" class="form-control" placeholder="{{__('languages.exclude_ai_calibration_question_limit')}}" value="{{$ConfigurationArray['exclude_ai_calibration_question_limit'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.ai_calibration_included_question_seed_limit')}}</label>
                                                <input type="text" name="ai_calibration_included_question_seed_limit" class="form-control" placeholder="{{__('languages.ai_calibration_included_question_seed_limit')}}" value="{{$ConfigurationArray['ai_calibration_included_question_seed_limit'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>

                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.calibration_constant')}}</label>
                                                <select name="calibration_constant" class="form-control select-option" data-show-subtext="true" data-live-search="true" id="calibration_constant">
                                                <option value="1" @if(isset($ConfigurationArray['calibration_constant']) && $ConfigurationArray['calibration_constant'] == 1) selected @endif>{{__('Zero')}}</option>
                                                <option value="2" @if(isset($ConfigurationArray['calibration_constant']) && $ConfigurationArray['calibration_constant'] == 2) selected @endif>{{__('As a percentile of calibration abilities')}}</option>
                                                </select>
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50 calibration_constant_percentile_sec" <?php if(isset($ConfigurationArray['calibration_constant']) && $ConfigurationArray['calibration_constant'] == 2){echo 'style="display:block;"';}else{ echo 'style="display:none;"';}?>>
                                                <label class="text-bold-600"> {{__('languages.percentile_of_calibration_abilities')}} (%) </label>
                                                <input type="text" id="calibration_constant_percentile" name="calibration_constant_percentile" class="form-control" placeholder="{{__('languages.percentile_of_calibration_abilities')}}" value="{{$ConfigurationArray['calibration_constant_percentile'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.ai_calibration_minimum_student_accuracy')}} (%)</label>
                                                <input type="text" name="ai_calibration_minimum_student_accuracy" class="form-control" placeholder="{{__('languages.ai_calibration_minimum_student_accuracy')}} (%)" value="{{$ConfigurationArray['ai_calibration_minimum_student_accuracy'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                {{-- AI Calibration --}}
                                
                                {{-- Custom Message For Student Attempt Exam --}}
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.attempt_exam_notification_message')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.english')}} {{__('languages.message')}}</label>
                                                <input type="text" name="attempt_exam_restrict_notification_en" class="form-control" placeholder="{{__('languages.english')}} {{__('languages.message')}}" value="{{$ConfigurationArray['attempt_exam_restrict_notification_en'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.chinese')}} {{__('languages.message')}}</label>
                                                <input type="text" name="attempt_exam_restrict_notification_ch" class="form-control" placeholder="{{__('languages.chinese')}} {{__('languages.message')}}" value="{{$ConfigurationArray['attempt_exam_restrict_notification_ch'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                 {{-- Custom Message For Student Attempt Exam --}}

                                {{-- Game Configuration --}}
                                {{--<fieldset>
                                    <legend class="global-config-title">{{__('languages.game_configuration')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.planet_entry_color')}}</label>
                                                <input type="color" id="planet_entry_color" name="planet_entry_color" value="{{$ConfigurationArray['planet_entry_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.planet_castle_color')}}</label>
                                                <input type="color" id="planet_castle_color" name="planet_castle_color" value="{{$ConfigurationArray['planet_castle_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.planet_keys_color')}}</label>
                                                <input type="color" id="planet_keys_color" name="planet_keys_color" value="{{$ConfigurationArray['planet_keys_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.unexplored_planet_color')}}</label>
                                                <input type="color" id="unexplored_planet_color" name="unexplored_planet_color" value="{{$ConfigurationArray['unexplored_planet_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.general_planet_color')}}</label>
                                                <input type="color" id="general_planet_color" name="general_planet_color" value="{{$ConfigurationArray['general_planet_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.planet_deduct_step_color')}}</label>
                                                <input type="color" id="planet_deduct_step_color" name="planet_deduct_step_color" value="{{$ConfigurationArray['planet_deduct_step_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.planet_increase_step_color')}}</label>
                                                <input type="color" id="planet_increase_step_color" name="planet_increase_step_color" value="{{$ConfigurationArray['planet_increase_step_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.max_deduction_steps')}}</label>
                                                <input type="text" name="max_deduction_steps" class="form-control" placeholder="{{__('languages.max_deduction_steps')}}" value="{{$ConfigurationArray['max_deduction_steps'] ?? ''}}">
                                                <span class="error-msg"></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.max_addition_steps')}}</label>
                                                <input type="text" name="max_addition_steps" class="form-control" placeholder="{{__('languages.max_addition_steps')}}" value="{{$ConfigurationArray['max_addition_steps'] ?? ''}}">
                                                <span class="error-msg"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.no_of_game_spot_keys')}}</label>
                                                <input type="text" name="no_of_game_spot_keys" class="form-control" placeholder="{{__('languages.no_of_game_spot_keys')}}" value="{{$ConfigurationArray['no_of_game_spot_keys'] ?? ''}}">
                                                <span class="error-msg"></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.game_introduction_video_url')}}</label>
                                                <input type="text" name="game_introduction_video_url" class="form-control" placeholder="{{__('languages.game_introduction_video_url')}}" value="{{$ConfigurationArray['game_introduction_video_url'] ?? ''}}">
                                                <span class="error-msg"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('Upload Game Music')}} [Note : Allowed only Audio File]</label>
                                                <input type="file" name="game_music_file" class="form-control">
                                                <span class="error-msg"></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                --}}
                                {{-- Game Configuration --}}

                                 {{-- Custom Layout Color --}}
                                 <fieldset>
                                    <legend class="global-config-title">{{__('languages.all_panel_backgrounds')}}</legend>
                                        <div class="form-row main-config-section">
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600"></label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="text-bold-600">{{__('languages.background_color')}}</label>
                                                </div>
                                                <div class="form-group col-md-3"> 
                                                    <label class="text-bold-600">{{__('languages.header_background_color')}}</label>
                                                </div>
                                                <div class="form-group col-md-3"> 
                                                    <label class="text-bold-600">{{__('languages.active_tab_color')}}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.super_admin')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="super_admin_panel_color" name="super_admin_panel_color" value="{{$ConfigurationArray['super_admin_panel_color'] ?? '#86a0cb'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="super_admin_header_color" name="super_admin_header_color" value="{{$ConfigurationArray['super_admin_header_color'] ?? '#5e91c3'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="super_admin_panel_active_color" name="super_admin_panel_active_color" value="{{$ConfigurationArray['super_admin_panel_active_color'] ?? '#8687fd'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.principal')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="principal_panel_color" name="principal_panel_color" value="{{$ConfigurationArray['principal_panel_color'] ?? '#bde5e1'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="principal_header_color" name="principal_header_color" value="{{$ConfigurationArray['principal_header_color'] ?? '#57dbba'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="principal_panel_active_color" name="principal_panel_active_color" value="{{$ConfigurationArray['principal_panel_active_color'] ?? '#46a59b'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.panel_head')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="panel_head_panel_color" name="panel_head_panel_color" value="{{$ConfigurationArray['panel_head_panel_color'] ?? '#fed08d'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="panel_head_header_color" name="panel_head_header_color" value="{{$ConfigurationArray['panel_head_header_color'] ?? '#e3bc4f'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="panel_head_panel_active_color" name="panel_head_panel_active_color" value="{{$ConfigurationArray['panel_head_panel_active_color'] ?? '#f7b350'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.coordinator')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="co_ordinator_panel_color" name="co_ordinator_panel_color" value="{{$ConfigurationArray['co_ordinator_panel_color'] ?? '#eab676'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="co_ordinator_header_color" name="co_ordinator_header_color" value="{{$ConfigurationArray['co_ordinator_header_color'] ?? '#e4d153'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="co_ordinator_panel_active_color" name="co_ordinator_panel_active_color" value="{{$ConfigurationArray['co_ordinator_panel_active_color'] ?? '#f4a23d'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.teacher')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="teacher_panel_color" name="teacher_panel_color" value="{{$ConfigurationArray['teacher_panel_color'] ?? '#f7bfbf'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="teacher_header_color" name="teacher_header_color" value="{{$ConfigurationArray['teacher_header_color'] ?? '#d897a1'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="teacher_panel_active_color" name="teacher_panel_active_color" value="{{$ConfigurationArray['teacher_panel_active_color'] ?? '#ef8787'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label class="text-bold-600">{{__('languages.common_sidebar.student')}}</label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="student_panel_color" name="student_panel_color" value="{{$ConfigurationArray['student_panel_color'] ?? '#d8dc41'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="student_header_color" name="student_header_color" value="{{$ConfigurationArray['student_header_color'] ?? '#afb927'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="color" id="student_panel_active_color" name="student_panel_active_color" value="{{$ConfigurationArray['student_panel_active_color'] ?? '#a3ad07'}}">
                                                    <span class="error-msg"></span>
                                                </div>
                                            </div>
                                        </div>
                                </fieldset>
                                 {{-- Custom Layout Color --}}

                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')
        <script>
            $("#calibration_constant").on("change",function(){
               if($("#calibration_constant").val() != 1 ){
                    $(".calibration_constant_percentile_sec").show();
               }else{
                    $("#calibration_constant_percentile").val('');
                    $(".calibration_constant_percentile_sec").hide();
               }
            })
        </script>
@endsection