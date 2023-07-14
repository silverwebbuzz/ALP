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
                             {{-- Start For The Self Learning Excercise Type List --}}
                             <div role="tabpanel" id="exercise">
                                 <table id="exercise-table">
                                     <thead>
                                         <tr>
                                             <th class="selec-opt">{{__('languages.publish_date_time')}}</th>
                                             <th>{{__('languages.reference_number')}}</th>
                                             <th>{{__('languages.report.accuracy')}}</th>
                                             <th>{{__('languages.study_status')}}</th>
                                             <th>{{__('languages.question_difficulties')}}</th>
                                             <th>{{__('languages.action')}}</th>
                                         </tr>
                                     </thead>
                                     <tbody class="scroll-pane">
                                         @if(isset($ExamsData['exercise_list']) && !empty($ExamsData['exercise_list']))
                                             @foreach($ExamsData['exercise_list'] as $selfLearningExcercise)
                                                 @php $examArray = $selfLearningExcercise->toArray(); @endphp
                                                 <tr>
                                                     <td>{{ date('d/m/Y H:i:s',strtotime($selfLearningExcercise->created_at)) }}</td>
                                                     <td>{{$selfLearningExcercise->reference_no}}</td>
                                                     @if(isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id')))
                                                         @php
                                                         $accuracy = App\Helpers\Helper::getAccuracy($selfLearningExcercise->id, $studentId);
                                                         $ability = $examArray['attempt_exams'][0]['student_ability'] ?? 0;
                                                         $accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
                                                         $abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
                                                         @endphp
                                                         <td>
                                                             @php
                                                             $total_correct_answers = $examArray['attempt_exams'][0]['total_correct_answers'];
                                                             $question_id_size = $examArray['question_ids'];
                                                             if($question_id_size != ""){
                                                                 $question_id_size = sizeof(explode(',',$question_id_size));
                                                             }
                                                             echo '<div class="progress"><div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% ('.$total_correct_answers.'/'.$question_id_size.')" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div></div>';
                                                             @endphp
                                                         </td>
                                                         <td align="center">
                                                             <span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%)" style="background-color: {{App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};border-radius: 50%;display: inline-block;"></span>
                                                         </td>
                                                     @else
                                                         <td align="center">-----</td>
                                                         <td align="center">-----</td>
                                                     @endif
                                                     <td>
                                                         @php
                                                         $progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($selfLearningExcercise->id,$studentId);
                                                         @endphp
                                                         <div class="progress" style="height: 1rem">
                                                             @php
                                                             if($progressQuestions['Level1'] !=0) {
                                                                 echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';
                                                             }
                                                             if($progressQuestions['Level2'] !=0) {
                                                                 echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
                                                             }
                                                             if($progressQuestions['Level3'] !=0) {
                                                                 echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
                                                             }
                                                             if($progressQuestions['Level4'] !=0) {
                                                                 echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
                                                             }
                                                             if($progressQuestions['Level5'] !=0) {
                                                                 echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
                                                             }
                                                             @endphp
                                                         </div>
                                                     </td>
                                                     <td class="btn-edit">
                                                         @if(in_array('attempt_exam_update', $permissions))
                                                             @if(!isset($examArray['attempt_exams']) || (isset($examArray['attempt_exams']) && !in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))) && $selfLearningExcercise->status == 'publish' && (App\Helpers\Helper::CheckExamStudentMapping($selfLearningExcercise->id) == false))
                                                             <a href="{{ route('studentAttemptExam', $selfLearningExcercise->id) }}" class="" title="{{__('languages.do')}}">
                                                                 <i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i>
                                                             </a>
                                                             @endif
                                                         @endif

                                                         {{-- @if(in_array('result_management_read', $permissions))	 --}}
                                                             @if((isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
                                                             <a href="{{route('adminexams.result',['examid' => $selfLearningExcercise->id, 'studentid' => $studentId])}}" class="view-result-btn" title="{{__('languages.result_text')}}">
                                                                 <i class="fa fa-eye fa-lg" aria-hidden="true"></i>
                                                             </a>
                                                             @endif
                                                         {{-- @endif --}}

                                                         @if((isset($examArray['attempt_exams']) && in_array($studentId,array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
                                                             {{-- Test Difficulty Analysis Link --}}
                                                             <a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$selfLearningExcercise->id}}">
                                                                 <i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i>
                                                             </a>
                                                         @endif
                                                         
                                                         {{-- <a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$selfLearningExcercise->id}}" title="{{__('languages.preview')}}"><i class="fa fa-book fa-lg" aria-hidden="true"></i></a> --}}

                                                         @if(isset($selfLearningExcercise->learning_objectives_configuration) && !empty($selfLearningExcercise->learning_objectives_configuration))
                                                         <a href="{{route('self_learning.preview',$selfLearningExcercise->id)}}" class="exam_info ml-2" title="{{__('languages.configurations')}}"><i class="fa fa-gear fa-lg" aria-hidden="true"></i></a>
                                                         @endif
                                                     </td>
                                                 </tr>
                                             @endforeach
                                         @endif
                                     </tbody>
                                 </table>
                             </div>
                             {{-- End For The Self Learning Excercise Type List --}}
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