@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec student-learning-report">
	@include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-title">
							<h2 class="mb-4 main-title">{{__('languages.learning_objectives_progress_short')}}</h2>
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				<!-- Include Study status color file -->
				@include('backend.reports.progress_report.study_status_color')
				<!-- End Include Study status color file -->
				
				<form class="mySubjects" id="mySubjects" method="get">
					<input type="hidden" name="isFilter" value="true">
					<div class="row">
						<div class="col-lg-2 col-md-2">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.form') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" required >
									@if(isset($GradesList) && !empty($GradesList))
									@foreach($GradesList as $grade)
									<option value="{{$grade['id']}}" @if(null !== request()->get('grade_id') && in_array($grade['id'],request()->get('grade_id'))) selected @elseif($loop->index==0) selected @endif>{{$grade['name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.class') }}</label>
								<select name="class_type_id[]" class="form-control" id="classType-select-option" >
									@if(isset($teachersClassList) && !empty($teachersClassList))
									@foreach($teachersClassList as $class)
									<option value="{{$class['class_id']}}" @if(null !== request()->get('class_type_id') && in_array($class['class_id'],request()->get('class_type_id'))) selected @elseif($classid == $class['class_id']) selected @endif>{{$class['class_name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.strand') }}</label>
								<select name="learningReportStrand[]" class="form-control select-option" id="strand_id">
									@if(isset($strandData) && !empty($strandData))
									@foreach($strandData as $strand)
									<option value="{{$strand->id}}" 
									@if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) 
										selected
									@elseif($loop->index==0)
									selected 
									@endif
									>{{ $strand->{'name_'.app()->getLocale()} }}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label>{{__('languages.learning_unit')}}</label>
                                <select name="learning_unit_id" class="form-control select-option" id="learning_unit" >
                                    @if(isset($LearningUnits) && !empty($LearningUnits))
                                        @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                            <option value="{{ $learningUnit['id'] }}" 
                                            	@if(null !== request()->get('learning_unit_id') && $learningUnit['id']==request()->get('learning_unit_id')) 
													selected
												@elseif($loop->index==0)
												selected 
												@endif
											>{{$learningUnit['index']}}. {{$learningUnit['name_'.app()->getLocale()]}} ({{$learningUnit['id']}})</option>
                                        @endforeach
                                    @else
                                        <option value="">{{__('languages.no_learning_units_available')}}</option>
                                    @endif
                                </select>
							</div>
						</div>
						<div class="select-lng pb-2 col-lg-2 col-md-4">
							<label for="users-list-role">{{ __('languages.report_type') }}</label>
							<select name="reportLearningType" class="form-control select-option" id="reportLearningType">
								<option value="">{{__("languages.all")}}</option>
								<option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.testing_zone")}}</option>
								<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }}>{{__("languages.tests")}}</option>
							</select>
						</div>
						<div class="col-lg-2 col-md-3">
							<label for="users-list-role"></label>
							<div class="select-lng pt-2 pb-2">
								<!-- <button type="submit" name="filter" value="filter" class="btn-search" onclick="showCoverSpinLoader()">{{ __('languages.search') }}</button> -->
								<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
							</div>
						</div>
					</div>
				</form>
				@if(isset($progressReportArray) && !empty($progressReportArray))
				@foreach($progressReportArray as $strandTitle => $strands)
				<div class="row">
					<div class="col-md-12">
						<h3>@if(isset($strandDataLbl[$strandTitle]) && !empty($strandDataLbl[$strandTitle]))
							{{$strandDataLbl[$strandTitle]}}
							@endif
						</h3>
					</div>
					@foreach($strands as $reportTitle => $learningUnits)
					<div class="col-xl-12 col-md-12 mb-4">
						<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report">
							<div class="card-body ml-2">
								<div class="row">
									<div class="col-md-12">
										<h5 class="font-weight-bold">
											@if(isset($LearningsUnitsLbl[$reportTitle]) && !empty($LearningsUnitsLbl[$reportTitle]))
											{{$LearningsUnitsLbl[$reportTitle]}}
											@endif
										</h5>
									</div>
									<div class="table-responsive learning-progress-report-table-wrapper">
										<table class="table table-bordered learning-progress-report-table">
											<thead>
												<tr>
													<th style="text-align:center;">{{__('languages.student_name')}}</th>
													@foreach($learningObjectivesList as $learningObjectives)
													<th style="text-align:center;" title="{{$learningObjectives['title_'.app()->getLocale()]}}">{{$learningObjectives['index']}} ({{$learningObjectives['foci_number']}})</th>
													@endforeach
												</tr>
											</thead>
											<tbody>
												@foreach($learningUnits as $students)
													<tr>
														<td style="text-align:center;">
															<?php echo  App\Helpers\Helper::decrypt($students['student_data'][0]['name_'.app()->getLocale()]); ?>
														</td>
														@foreach($students['report_data'] as $report_data)
														<?php
														$normalizedAbility = 0;
														$studyStatusColor = 'background:'.App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';color:#FFF;';
														if(isset($report_data) && !empty($report_data) && !empty($report_data['normalizedAbility'])){
															$studyStatusColor = 'background:'.$report_data['studyStatusColor'].';color:#FFF;';
														}
														?>
														<td style="text-align:center;">
															<div class="study_status_colors-sec">
																<span class="dot-color" style="{{$studyStatusColor}}border-radius: 50%;display: inline-block;"></span>
															</div>
														</td>
														@endforeach		
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
				@endforeach
				@endif
			</div>
		</div>
	</div>
</div>
@include('backend.layouts.footer')
@include('backend.reports.learning_progress.progress_report_js')
@endsection