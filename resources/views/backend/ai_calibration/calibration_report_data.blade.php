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
								<h2 class="mb-4 main-title">{{__('languages.ai_calibration_report')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="ai-calibration-report card">
                        <div class="pb-5 card-body">
                           <div class="ai-calibration-head-label">
                                <input type="hidden" name="calibration_report_id" id="calibration_report_id" value="{{$CalibrationReport->id}}">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.calibration_number')}} :</label>
                                        <span>{{$CalibrationReport->calibration_number}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.reference_adjusted_calibration_number')}} :</label>
                                        <span>{{$CalibrationReport->ReferenceAdjustedCalibration ?? '----'}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.start_date')}} :</label>
                                        <span>{{ \App\Helpers\Helper::dateConvertDDMMYYY('-','/',$CalibrationReport->start_date)}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.end_date')}} :</label>
                                        <span>{{ \App\Helpers\Helper::dateConvertDDMMYYY('-','/',$CalibrationReport->end_date)}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        @php
                                            $no_of_involved_school = (!empty($CalibrationReport->school_ids)) ? count(explode(',',$CalibrationReport->school_ids)) : 0;
                                        @endphp
                                        <label>{{__('languages.schools')}} :</label>
                                        <span>{{$no_of_involved_school}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        @php
                                            $no_of_involved_question_seed = (!empty($CalibrationReport->included_question_ids)) ? count(explode(',',$CalibrationReport->included_question_ids)) : 0;
                                        @endphp
                                        <label>{{__('languages.question_seeds')}} :</label>
                                        <span>{{$no_of_involved_question_seed}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        @php
                                            $no_of_involved_student = (!empty($CalibrationReport->included_student_ids)) ? count(explode(',',$CalibrationReport->included_student_ids)) : 0;
                                        @endphp
                                        <label>{{__('languages.students')}} :</label>
                                        <span>{{$no_of_involved_student}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.calibration_constant')}} :</label>
                                        <span>{{$CalibrationReport->calibration_constant ?? 0}}</span>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>{{__('languages.median_of_calibration_abilities')}} :</label>
                                        <span>{{ \App\Helpers\Helper::DisplayingAbilities($CalibrationReport->median_calibration_ability)}} ({{$CalibrationReport->median_calibration_ability}})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ai-calibration-lvl-diff">
                                @php
                                    $median_difficulty_levels = json_decode($CalibrationReport->median_difficulty_levels,true);
                                @endphp
                                @if(!empty($median_difficulty_levels))
                                    @foreach($median_difficulty_levels as $level => $LevelDifficulty)
                                    <div>
                                        <label>{{__('languages.median')}} {{$level}}-{{__('languages.level_of_difficulty')}} :</label>
                                        <span>{{round((exp($LevelDifficulty)/(1+exp($LevelDifficulty)) * 10), 3)}} ({{$LevelDifficulty}})</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="ai-calibration-lvl-diff">
                                @php
                                    $standard_deviation_difficulty_levels = json_decode($CalibrationReport->standard_deviation_difficulty_levels);
                                @endphp
                                @if(!empty($standard_deviation_difficulty_levels))
                                    @foreach($standard_deviation_difficulty_levels as $level => $StandardDeviationDifficultyLevel)
                                    <div>
                                        <!-- <label>{{__('languages.standard_deviation_of')}} {{$level}}-{{__('languages.level_difficulty')}} :</label> -->
                                        <label>{{$level}}-{{__('languages.level_difficulty')}} {{__('languages.standard_deviation_of')}} :</label>
                                        <span>{{round((exp($StandardDeviationDifficultyLevel)/(1+exp($StandardDeviationDifficultyLevel)) * 10), 3)}} ({{$StandardDeviationDifficultyLevel}})</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            @if(!empty($AICalibrationReport['calibration_questions']))
                                <div class="ai-calibration-table mb-3">
                                    <h2>{{__('languages.question_seeds')}}</h2>
                                    <table class="styled-table">
                                    <thead>
                                        <tr>
                                            <th>#{{__('languages.sr_no')}}</th>
                                            <th>{{__('languages.question_seed_code')}}</th>
                                            <th>{{__('languages.previous_difficulty')}}</th>
                                            <th>{{__('languages.calibration_difficulty')}}</th>
                                            <th>{{__('languages.change')}} (%)</th>
                                        </tr>
                                        <tbody>
                                            @foreach($AICalibrationReport['calibration_questions'] as $question)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$question['question_code']}}</td>
                                                <td>{{$question['previous_question_difficulties']}}</td>
                                                <td>{{$question['new_question_difficulties']}}</td>
                                                <td>{{$question['difference_percentage']}}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </thead>
                                    </table>
                                </div>
                            @endif
                            @if(!empty($AICalibrationReport['calibration_students']))
                                <div class="ai-calibration-table">
                                    <h2>{{__('languages.involved_students')}}</h2>
                                    <table class="styled-table">
                                        <thead>
                                            <tr>
                                                <th>#{{__('languages.sr_no')}}</th>
                                                <th>{{__('languages.school_name')}}</th>
                                                <th>{{__('languages.email')}}</th>
                                                <th>{{__('languages.std_number')}}</th>
                                                <th>{{__('languages.calibration_ability')}}</th>
                                            </tr>
                                            <tbody>
                                                @foreach($AICalibrationReport['calibration_students'] as $student)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$student['school_name']}}</td>
                                                    <td>{{$student['email']}}</td>
                                                    <td>{{$student['permanent_reference_number']}}</td>
                                                    <td>{{$student['calibration_abilities']}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </thead>
                                    </table>
                                </div>
                            @endif

                            @if($isUpdatedCalibration)
                            <div class="form-group m-0 btn-sec d-flex">
                                <a href="{{ route('ai-calibration.question-log', $CalibrationReport->id) }}">
                                    <button type="button" class="blue-btn btn btn-primary px-4 mt-4 black-button">{{__('languages.view_calibration_log')}}</button>
                                </a>
                            </div>
                            @else
                            <div class="calibration-report-option mt-5">
                                <div class="form-group m-0 btn-sec d-flex">
                                    <h4>{{__('languages.decide_whether_to_execute_calibration_adjustment_of_this_calibration')}}</h4>
                                    <!-- <button type="button" class="blue-btn btn btn-primary mx-2 px-4 isUpdateCalibrationDifficulty">{{__('languages.yes')}}</button> -->
                                    <!-- <button type="button" class="blue-btn btn btn-primary px-4">{{__('languages.no')}}</button> -->
                                </div>
                                <p class="mt-3 mb-2">{{__('languages.calibration_report_message')}}</p>
                                <div class="form-check">
                                    <input class="form-check-input isUpdateNonIncludedQuestions" type="radio" name="isUpdateNonIncludedQuestions" id="isUpdateNonIncludedQuestions1" value="yes">
                                    <label class="form-check-label" for="isUpdateNonIncludedQuestions1">{{__('languages.yes')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input isUpdateNonIncludedQuestions" type="radio" name="isUpdateNonIncludedQuestions" id="isUpdateNonIncludedQuestions2" value="no">
                                    <label class="form-check-label" for="isUpdateNonIncludedQuestions2">{{__('languages.no')}}</label>
                                </div>
                                <label id="isUpdateNonIncludedQuestionsError" style="display:none;color: red;">{{__('languages.please_select_option')}}</label>
                                <div class="form-group m-0 btn-sec d-flex">
                                    <button type="button" class="blue-btn btn btn-primary px-4 mt-4 black-button isUpdateCalibrationDifficulty">{{__('languages.yes')}}</button>
                                </div>
                            </div>
                            @endif
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        <script>
            $( document ).ready(function() {
                $(document).on('click', '.isUpdateCalibrationDifficulty', function() {
                    if($('.isUpdateNonIncludedQuestions:checked').val()){
                        $('#isUpdateNonIncludedQuestionsError').hide();
                    }else{
                        $('#isUpdateNonIncludedQuestionsError').show();
                        return false;
                    }
                    var CalibrationReportId = $('#calibration_report_id').val();
                    ExecuteCalibrationAdjustment(CalibrationReportId);
                });
                /**
                 * Trigger : On change update difficulty excluded question
                 */
                $(document).on('change', '.isUpdateNonIncludedQuestions', function() {
                    if($('.isUpdateNonIncludedQuestions:checked').val()){
                        $('#isUpdateNonIncludedQuestionsError').hide();
                    }else{
                        $('#isUpdateNonIncludedQuestionsError').show();
                        return false;
                    }
                });
            });

            function ExecuteCalibrationAdjustment(CalibrationReportId){
                if(CalibrationReportId){
                    $("#cover-spin").show();
                    $.ajax({
                        url: BASE_URL + "/ai-calibration/execute-calibration-adjustment/"+CalibrationReportId,
                        method: "GET",
                        async: true,
                        data:{
                            isUpdateNonIncludedQuestions : $('.isUpdateNonIncludedQuestions:checked').val(),
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if(data){
                                if(data.data.redirect_url){
                                    window.location = BASE_URL+'/'+data.data.redirect_url;
                                }
                            }
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                }
            }
        </script>
        @include('backend.layouts.footer')  
@endsection