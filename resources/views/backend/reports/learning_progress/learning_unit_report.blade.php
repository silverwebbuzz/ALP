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
							<h2 class="mb-4 main-title">{{__('languages.learning_unit_progress_short')}}</h2>
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				<!-- Include Study status color file -->
				@include('backend.reports.learning_progress.learning_unit_report_color_code')
				<!-- End Include Study status color file -->
				<form class="" id="" method="get">
					<input type="hidden" name="isFilter" value="true">
					<div class="row">
						<div class="col-lg-2 col-md-2">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.form') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" required >
									@if(!empty($GradesList))
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
									@if(!empty($ClassList))
									@foreach($ClassList as $class)
									<option value="{{$class['class_id']}}" @if(null !== request()->get('class_type_id') && in_array($class['class_id'],request()->get('class_type_id'))) selected @elseif($classid == $class['class_id']) selected @endif>{{$class['class_name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="select-lng pb-2 col-lg-2 col-md-4">
							<label for="users-list-role">{{ __('languages.report_type') }}</label>
							<select name="reportLearningType" class="form-control select-option" id="reportLearningType">
								<option value="">{{__("languages.all")}}</option>
								<option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.testing_zone")}}</option>
								<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }} @if(request()->get('reportLearningType')=="") selected @endif>{{__("languages.tests")}}</option>
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

				<div class="row">
					<div class="col-xl-12 col-md-12 mb-4">
						<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report learning-progress-report">
							<div class="card-body ml-2">
								<div class="row">									
									<div class="table-responsive learning-progress-report-table-wrapper">
										<table class="table table-bordered learning-progress-report-table">
											<thead>
												<tr>
													<th rowspan="2">{{__('languages.student_name')}}</th>
													@foreach($StrandList as $Strand)
														<th colspan={{count(App\Helpers\Helper::getLearningUnits($Strand['id']))}}>{{$Strand['name_'.app()->getLocale()]}}</th>
													@endforeach
												</tr>
                                                <tr>
													@foreach($LearningUnitsList as $learningUnit)
														<th>{{$learningUnit['index']}}.{{$learningUnit['name_'.app()->getLocale()]}} ({{$learningUnit['id']}})</th>
													@endforeach
												</tr>
											</thead>
                                            <tbody>
                                            @foreach($progressReportArray as $StudentId => $Student)
                                                <tr>
                                                    <td><?php echo  App\Helpers\Helper::decrypt($Student['student_data'][0]['name_'.app()->getLocale()]); ?></td>
													@foreach($Student['report_data'] as $LearningUnit)
													<td>
														<div class="progress-bar-main">
															@if(isset($LearningUnit) && !empty($LearningUnit) && !empty($LearningUnit['achieved_percentage']))
															<div class="progress" style="height:1rem;background-color:{{$ColorCodes['not_accomplished_color']}};" title="{{round($LearningUnit['achieved_percentage'],1)}}%">
																<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{round($LearningUnit['achieved_percentage'],1)}}%" style="width:{{$LearningUnit['achieved_percentage']}}%;background-color:{{$ColorCodes['accomplished_color']}};"></div>
															</div>
															<span class="progress-count">{{round($LearningUnit['achieved_percentage'],1)}}%</span>
															@else
															<div class="progress" style="height:1rem;background-color:{{$ColorCodes['not_accomplished_color']}};" title="0%">
																<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="0%" style="width:0%;background-color:{{$ColorCodes['not_accomplished_color']}};"></div>
															</div>
															<span class="progress-count">0%</span>
															@endif
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
				</div>
			</div>
		</div>
	</div>
</div>
@include('backend.layouts.footer')
@endsection