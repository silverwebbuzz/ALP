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
							<h2 class="mb-4 main-title">{{ __('languages.learning_units') }} {{__('languages.report_text')}}</h2>
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				@include('backend.student.student_profile_menus')

				<!-- Include Study status color file -->
				@include('backend.reports.progress_report.learning_unit_report_color_code')
				<!-- End Include Study status color file -->
				
				<form class="mySubjects" id="mySubjects" method="get">
					<input type="hidden" name="isFilter" value="true">
					<div class="row">
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
								<button type="submit" name="filter" value="filter" class="btn-search" onclick="showCoverSpinLoader()">{{ __('languages.search') }}</button>
							</div>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-xl-12 col-md-12 mb-4">
						<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report teacher-progress-report-table">
							<div class="card-body ml-2">
								<div class="row">									
									<div class="table-responsive learning-progress-report-table-wrapper">
										<table class="table table-bordered learning-progress-report-table">
											<thead>
                                                @foreach($LearningUnitsList as $learninKey => $learningUnit)
                                                    <tr>
                                                        <th>{{$learningUnit['index']}}. {{$learningUnit['name_'.app()->getLocale()]}} ({{$learningUnit['id']}})</th> 
                                                        <td>
                                                            <div class="progress" style="height:1rem">
                                                                <div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$progressReportArray[$studentId]['report_data'][$learninKey]['accomplished_percentage']}}% ({{$progressReportArray[$studentId]['report_data'][$learninKey]['count_accomplished_learning_objectives']}}/{{$progressReportArray[$studentId]['report_data'][$learninKey]['no_of_learning_objectives']}})" style="width:{{$progressReportArray[$studentId]['report_data'][$learninKey]['accomplished_percentage']}}%;background-color:{{$ColorCodes['accomplished_color']}};">
                                                                    @if(!empty($progressReportArray[$studentId]['report_data'][$learninKey]['accomplished_percentage']))
                                                                    {{$progressReportArray[$studentId]['report_data'][$learninKey]['accomplished_percentage']}}%
                                                                    @else
                                                                    {{__('languages.not_available')}}
                                                                    @endif
                                                                </div>

                                                                <div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$progressReportArray[$studentId]['report_data'][$learninKey]['not_accomplished_percentage']}}% ({{$progressReportArray[$studentId]['report_data'][$learninKey]['count_not_accomplished_learning_objectives']}}/{{$progressReportArray[$studentId]['report_data'][$learninKey]['no_of_learning_objectives']}})" style="width:{{$progressReportArray[$studentId]['report_data'][$learninKey]['not_accomplished_percentage']}}%;background-color:{{$ColorCodes['not_accomplished_color']}};">
                                                                    @if(!empty($progressReportArray[$studentId]['report_data'][$learninKey]['not_accomplished_percentage']))
                                                                    {{$progressReportArray[$studentId]['report_data'][$learninKey]['not_accomplished_percentage']}}%
                                                                    @else
                                                                    {{__('languages.not_available')}}
                                                                    @endif
                                                                </div>
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
				</div>
			</div>
		</div>
	</div>
</div>
@include('backend.layouts.footer')
@endsection