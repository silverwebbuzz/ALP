@extends('backend.layouts.app')
@section('content')
<style type="text/css">
.progress-sm {
  height: .5rem;
}
.position-center {
  left: 50%;
  top: 50%;
  -webkit-transform: translate(-50%,-50%);
  transform: translate(-50%,-50%);
  position: absolute !important;
  display: block;
  font-size: 20px;
}
.cm-progress-bar.progress-bar {
  text-align: right;
  color: #FFF;
  font-weight: bold;
}
.text-geay{
  color: gray;
}
</style>
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec student-learning-report">
  @include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-title">
							{{-- <h2 class="mb-4 main-title">{{ __('languages.learning_objectives_progress_short')}}</h2> --}}
							<h2 class="mb-4 main-title">{{__('languages.user_management.student')}} {{__('languages.details')}} 
								@if(Auth::user()->role_id != 3)
									({{$studentData->class_student_number}})
								@endif
							</h2>
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
                        <hr class="blue-line">
                      </div>
                    </div>
					@include('backend.student.student_profile_menus')
					
					<!-- Include Study status color file -->
					@include('backend.reports.progress_report.study_status_color')
					<!-- End Include Study status color file -->

                    <form class="mySubjects" id="mySubjects" method="get">	
						<input type="hidden" name="isFilter" value="true">
                      	<div class="row">
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pb-2">
									<label for="users-list-role">{{ __('languages.strand') }}</label>
									<select name="learningReportStrand[$learningObjectivesKey]m-control select-option" id="strand_id">
									@if(!empty($strandData))
										@foreach($strandData as $strand)
										<option value="{{$strand->id}}" @if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) selected @elseif($loop->index == 0) selected @endif > {{ $strand->{'name_'.app()->getLocale()} }}</option>
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
												>{{$learningUnit['index']}}. {{ $learningUnit['name_'.app()->getLocale()] }} ({{$learningUnit['id']}})</option>
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
									<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }} @if(request()->get('reportLearningType')=="") selected @endif> {{__("languages.tests")}}</option>
								</select>
							</div>
							<div class="col-lg-2 col-md-3">
								<label for="users-list-role"></label>
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search" onclick="showCoverSpinLoader()">{{ __('languages.search') }}</button>
								</div>
							</div>
                      	</div>
                    </form>

                    @php
                        $data='';
                    @endphp

                	@foreach($progressReportArray as $strandTitle => $reportData)
					<div class="row">
						@foreach($reportData as $reportTitle => $reportInfo)
						<div class="col-xl-12 col-md-12 mb-4">
							<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report">
								<div class="card-body ml-2">
									<div class="row">
										<div class="col-md-12">
											<h5 class="font-weight-bold">
												@if(isset($strandDataLbl[$strandTitle]) && !empty($strandDataLbl[$strandTitle]))
			                                        {{ $strandDataLbl[$strandTitle] }}
			                                    @endif
											</h5>
										</div>
										<div class="table-responsive learning-progress-report-table-wrapper">
											<table class="table table-bordered learning-progress-report-table">
												<thead>
												@foreach($learningObjectivesList as $learningObjectives)
												<tr>
													<th style="width: 10% !important;min-width: 10% !important;">{{ $learningObjectives['index'] }} {{ $learningObjectives['title_'.app()->getLocale()] }} ({{ $learningObjectives['foci_number'] }})</th>
													<?php
														$normalizedAbility = 0;
														$studyStatusColor = 'background:'.App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';color:#FFF;';
														if(isset($reportInfo['report_data']) && !empty($reportInfo['report_data']) && !empty($reportInfo['report_data'][$learningObjectives['id']]['normalizedAbility'])){
															$studyStatusColor = 'background:'.$reportInfo['report_data'][$learningObjectives['id']]['studyStatusColor'].';color:#FFF;';
														}
													?>
													<td style="width: 10% !important;min-width: 10% !important; text-align:center;">
														<div class="study_status_colors-sec">
															<span class="dot-color" style="{{$studyStatusColor}}border-radius: 50%;display: inline-block;"></span>
														</div>
													</td>
												</tr>
												@endforeach
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				@endforeach
    			</div>
			</div>
        </div>
    </div>
	@include('backend.layouts.footer')
	@include('backend.reports.progress_report.progress_report_js')
@endsection