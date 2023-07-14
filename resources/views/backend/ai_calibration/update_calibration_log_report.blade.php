<input type="hidden" name="CalibrationReportId" value="{{$CalibrationReportId}}">
<div class="ai-calibration-head-label">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <label>{{__('languages.calibration_constant')}} :</label>
            <span>{{$CalibrationConstant}}</span>
        </div>
    </div>
</div>
<div class="ai-calibration-table mb-3">
    <h2>{{__('languages.included_question_calibration_log')}}</h2>
    <table class="styled-table">
        <thead>
            <tr>
                <th>#{{__('languages.sr_no')}}</th>
                <th>{{__('languages.calibration_number')}}</th>
                <th>{{__('languages.test.from_date')}}</th>
                <th>{{__('languages.to_date')}}</th>
                <th>{{__('languages.question_seed_code')}}</th>
                <th>{{__('languages.previous_difficulty')}}</th>
                <th>{{__('languages.calibration_difficulty')}}</th>
                <th>{{__('languages.change')}} (%)</th>                
                <th>{{__('languages.median_of_difficulty_level')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($IncludedQuestionCalibrationLogs as $includedQuestionLog)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$includedQuestionLog->AICalibrationReport->calibration_number}}</td>
                <td>{{$includedQuestionLog->AICalibrationReport->start_date}}</td>
                <td>{{$includedQuestionLog->AICalibrationReport->end_date}}</td>
                <td>{{$includedQuestionLog->question->naming_structure_code}}</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($includedQuestionLog->previous_ai_difficulty)}} ({{$includedQuestionLog->previous_ai_difficulty}})</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($includedQuestionLog->calibration_difficulty)}} ({{$includedQuestionLog->calibration_difficulty}})</td>
                <td>{{$includedQuestionLog->change_difference}}%</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($includedQuestionLog->median_of_difficulty_level)}} ({{$includedQuestionLog->median_of_difficulty_level}})</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($isUpdateNonIncludedQuestions == 'yes')
<div class="ai-calibration-table mb-3">
    <h2>{{__('languages.excluded_question_calibration_log')}}</h2>
    <table class="styled-table">
        <thead>
            <tr>
                <th>#{{__('languages.excluded_question_calibration_log')}}</th>
                <th>{{__('languages.calibration_number')}}</th>
                <th>{{__('languages.test.from_date')}}</th>
                <th>{{__('languages.test.to_date')}}</th>
                <th>{{__('languages.question_seed_code')}}</th>
                <th>{{__('languages.previous_difficulty')}}</th>
                <th>{{__('languages.calibration_difficulty')}}</th>
                <th>{{__('languages.change')}} (%)</th>
                <th>{{__('languages.median_of_difficulty_level')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ExcludedQuestionCalibrationLogs as $excludedQuestionLog)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$excludedQuestionLog->AICalibrationReport->calibration_number}}</td>
                <td>{{$excludedQuestionLog->AICalibrationReport->start_date}}</td>
                <td>{{$excludedQuestionLog->AICalibrationReport->end_date}}</td>
                <td>{{$excludedQuestionLog->question->naming_structure_code}}</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($excludedQuestionLog->previous_ai_difficulty)}} ({{$excludedQuestionLog->previous_ai_difficulty}})</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($excludedQuestionLog->calibration_difficulty)}} ({{$excludedQuestionLog->calibration_difficulty}})</td>
                <td>{{$excludedQuestionLog->change_difference}}%</td>
                <td>{{App\Helpers\Helper::DisplayingDifficulties($excludedQuestionLog->median_of_difficulty_level)}} ({{$excludedQuestionLog->median_of_difficulty_level}})</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif