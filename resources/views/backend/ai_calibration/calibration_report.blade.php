<div class="ai-calibration-head-label">
    <input type="hidden" name="calibration_report_id" id="calibration_report_id" value="{{$CalibrationReport['calibration_report_id']}}">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.start_date')}} :</label>
            <span>{{$CalibrationReport['start_date']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.end_date')}} :</label>
            <span>{{$CalibrationReport['end_date']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.no_of_involved_school')}} :</label>
            <span>{{$CalibrationReport['no_of_involved_school']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.no_of_involved_question_seeds')}} :</label>
            <span>{{$CalibrationReport['no_of_involved_question_seed']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.no_of_involved_students')}} :</label>
            <span>{{$CalibrationReport['no_of_involved_student']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.calibration_constant')}} :</label>
            <span>{{$CalibrationReport['calibration_constant']}}</span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.median_of_calibration_abilities')}} :</label>
            <span>{{$CalibrationReport['median_calibration_abilities']}}</span>
        </div>
    </div>
</div>
<div class="ai-calibration-lvl-diff">
    @foreach($CalibrationReport['median_difficulty_levels'] as $level => $LevelDifficulty)
    <div>
        <label>{{__('languages.median_of_difficulty_of')}} {{$level}}-{{__('languages.level_of_difficulty')}}</label>
        <span>{{round((exp($LevelDifficulty)/(1+exp($LevelDifficulty)) * 10), 3)}}</span>
    </div>
    @endforeach
</div>

<div class="ai-calibration-lvl-diff">
    @foreach($CalibrationReport['standard_deviation_difficulty_levels'] as $level => $StandardDeviationDifficultyLevel)
    <div>
        <label>{{__('languages.standard_deviation_of')}} {{$level}}-{{__('languages.level_difficulty')}}</label>
        <span>{{round((exp($StandardDeviationDifficultyLevel)/(1+exp($StandardDeviationDifficultyLevel)) * 10), 3)}} ({{$StandardDeviationDifficultyLevel}})</span>
    </div>
    @endforeach
</div>

<div class="ai-calibration-table mb-3">
    <h2>{{__('languages.involved_question_seeds')}}</h2>
    <table class="styled-table">
    <thead>
        <tr>
            <th>#{{__('languages.sr_no')}}</th>
            <th>{{__('languages.question_seed_code')}}</th>
            <th>{{__('languages.previews_difficulty')}}</th>
            <th>{{__('languages.calibration_difficulty')}}</th>
            <th>{{__('languages.change')}} (%)</th>
        </tr>
        <tbody>
            @foreach($CalibrationReport['calibration_questions'] as $question)
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
                @foreach($CalibrationReport['calibration_students'] as $student)
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

<div class="calibration-report-option mt-5">
    <div class="form-group m-0 btn-sec d-flex">
        <h4>{{__('languages.decide_whether_to_execute_calibration_adjustment_of_this_calibration')}}</h4>
        <button type="button" class="blue-btn btn btn-primary mx-2 px-4 next-button next_btn_step_2" data-stepid="2">{{__('languages.yes')}}</button>
        <button type="button" class="blue-btn btn btn-primary px-4">{{__('languages.no')}}</button>
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
</div>