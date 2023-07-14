@extends('backend.layouts.app')
@section('content')
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
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec student-learning-report">
	@include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-title">
							<h2 class="mb-4 main-title">{{__('languages.user_management.student')}} {{__('languages.details')}} ({{$studentData->class_student_number}})</h2>
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				@include('backend.student.student_profile_menus')
                <div class="row main-my-study">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                       <div id="mystudytable" class="my-study-table">
                          <div class="tab-content">
                             <table id="test-table">
                                 <thead>
                                     <tr>
                                         <th>{{__('languages.report.start_date')}}</th>
                                         <th>{{__('languages.report.end_date')}}</th>
                                         <th>{{__('languages.report.result_release_date')}}</th>
                                         <th>{{__('languages.reference_number')}}</th>
                                         <th>{{__('languages.title')}}</th>
                                         <th>{{__('languages.report.accuracy')}}</th>
                                         <th align="center">{{__('languages.study_status')}}</th>
                                         <th>{{__('languages.status')}}</th>
                                         <th>{{__('languages.question_difficulties')}}</th>
                                         <th>{{__('languages.action')}}</th>
                                     </tr>
                                 </thead>
                                 @if(isset($data['testExam']) && !empty($data['testExam']))
                                 <tbody class="scroll-pane">
                                     @foreach($data['testExam'] as $exerciseExam)
                                     @php $examArray = $exerciseExam->toArray(); 
                                     @endphp
                                     <tr @if($data['testExam']) class='test-exam' @endif>
                                         <td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['start_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['start_time']) ? $examArray['exam_school_grade_class'][0]['start_time'] : '00:00:00' }}</td>
                                         <td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['end_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['end_time']) ?  $examArray['exam_school_grade_class'][0]['end_time'] : '00:00:00' }}</td>
                                         <td>{{date('d/m/Y',strtotime($exerciseExam->result_date))}}</td>
                                         <td>{{$exerciseExam->reference_no}}</td>
                                         <td>{{$exerciseExam->title}}</td>
                                         @if(isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id')))
                                             @php
                                             $accuracy = App\Helpers\Helper::getAccuracy($exerciseExam->id, $studentId);
                                             $ability  = $examArray['attempt_exams'][0]['student_ability'] ?? 0;
                                             $accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
                                             $abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
                                             @endphp
                                             <td>
                                                 @php
                                                 $total_correct_answers = $examArray['attempt_exams'][0]['total_correct_answers'];
                                                 $question_id_size = $examArray['question_ids'];
                                                 if($question_id_size != ""){
                                                     $question_id_size=sizeof(explode(',',$question_id_size));
                                                 }
                                                 echo '<div class="progress"><div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% ('.$total_correct_answers.'/'.$question_id_size.')" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div></div>';
                                                 @endphp
                                             </td>
                                             <td align="center">
                                                 <span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%) "  style="border-radius: 50%;display: inline-block;position: relative;background-color: {{ App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};"></span>
                                             </td>
                                         @else
                                             <td align="center">-----</td>
                                             <td align="center">-----</td>
                                         @endif
                                         <td>
                                             @if((isset($examArray['attempt_exams']) && !in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))))
                                             <span class="badge badge-warning">{{__('languages.test.pending')}}</span>
                                             @else
                                             <span class="badge badge-success">{{__('languages.test.complete')}}</span>
                                             @endif
                                         </td>
                                         <td>
                                             @php
                                                 $progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($exerciseExam->id,$studentId);
                                             @endphp
                                             <div class="progress">
                                                 @php
                                                 
                                                 if($progressQuestions['Level1'] !=0) {
                                                     echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';																
                                                 }

                                                 if($progressQuestions['Level2'] !=0) {
                                                     echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
                                                 }

                                                 if($progressQuestions['Level3'] !=0) {
                                                     echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
                                                 }
                                                 
                                                 if($progressQuestions['Level4'] !=0) {
                                                     echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
                                                 }
                                                 
                                                 if($progressQuestions['Level5'] !=0) {
                                                     echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
                                                 }
                                                 @endphp
                                             </div>
                                         </td>
                                         <td class="btn-edit">
                                             @if(in_array('attempt_exam_update', $permissions))
                                                 @if(
                                                     (isset($examArray['attempt_exams'])
                                                     && !in_array($studentId,array_column($examArray['attempt_exams'],'student_id')))
                                                     && $exerciseExam->status == 'publish'
                                                     && 
                                                     (isset($exerciseExam->ExamGradeClassConfigurations->start_date)
                                                         && isset($exerciseExam->ExamGradeClassConfigurations->start_time)
                                                         && isset($exerciseExam->ExamGradeClassConfigurations->end_date)
                                                         && isset($exerciseExam->ExamGradeClassConfigurations->end_time)
                                                         && date('Y-m-d H:i:s',strtotime($exerciseExam->ExamGradeClassConfigurations->start_date.''.$exerciseExam->ExamGradeClassConfigurations->start_time)) <= date('Y-m-d H:i:s')
                                                         && date('Y-m-d H:i:s',strtotime($exerciseExam->ExamGradeClassConfigurations->end_date.''.$exerciseExam->ExamGradeClassConfigurations->end_time)) >= date('Y-m-d H:i:s')
                                                         
                                                         || 

                                                         isset($exerciseExam->ExamGradeClassConfigurations->start_date)
                                                         && isset($exerciseExam->ExamGradeClassConfigurations->end_date)
                                                         && date('Y-m-d',strtotime($exerciseExam->ExamGradeClassConfigurations->start_date)) <= date('Y-m-d')
                                                         && date('Y-m-d',strtotime($exerciseExam->ExamGradeClassConfigurations->end_date)) >= date('Y-m-d')
                                                     )
                                                     && (App\Helpers\Helper::CheckExamStudentMapping($exerciseExam->id) == false)
                                                 )
                                                 <!-- <a href="{{ route('studentAttemptExam', $exerciseExam->id) }}" class="" title="{{__('languages.test_text')}}">
                                                     <i class="fa fa-book" aria-hidden="true"></i>
                                                 </a> -->
                                                 <a href="{{route('StudentAttemptTestExercise', $exerciseExam->id)}}" class="" title="{{__('languages.do')}}">
                                                     <i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i>
                                                 </a>
                                                 @endif
                                             @endif
                                             {{-- @if (in_array('result_management_read', $permissions)) --}}
                                                 @if((isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
                                                 <a href="{{route('adminexams.result',['examid' => $exerciseExam->id, 'studentid' =>$studentId])}}" class="view-result-btn" title="{{__('languages.result_text')}}">
                                                     <i class="fa fa-eye fa-lg" aria-hidden="true" ></i>
                                                 </a>
                                                 @endif 
                                             {{-- @endif --}}

                                             {{-- <a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$exerciseExam->id}}" title="{{__('languages.preview')}}"><i class="fa fa-book fa-lg" aria-hidden="true"></i></a> --}}

                                             @if((isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
                                                 {{-- Test Difficulty Analysis Link --}}
                                                 <a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$exerciseExam->id}}">
                                                     <i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i>
                                                 </a>
                                             @endif
                                         </td>
                                     </tr>
                                     @endforeach
                                     @endif
                                 </tbody>
                             </table>
                         </div>
                       </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
@include('backend.layouts.footer')
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
@endsection