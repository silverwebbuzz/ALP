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
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.ai_calibration_list')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								{{-- @if (in_array('sub_admin_management_create', $permissions)) --}}
									<a href="{{ route('ai-calibration.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.add_new_calibration')}}</a>
								{{-- @endif --}}
								</div>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
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
				 	 <form class="addCalibrationFilterForm" id="addCalibrationFilterForm" method="get">	
						<div class="row">
							<div class="col-lg-4 col-md-4">
								<label for="id_end_time">{{__('languages.calibration_number')}}</label>
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="Search" value="{{request()->get('Search')}}" placeholder="{{__('languages.calibration_number')}}">
									@if($errors->has('Search'))
										<span class="validation_error">{{ $errors->first('Search') }}</span>
									@endif
								</div>
							</div>
							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<label for="id_end_time">{{ __('languages.test.from_date') }}</label>
									<div class="test-list-clandr">
										<input type="text" class="form-control from-date-picker" name="from_date" value="{{ (request()->get('from_date')) }}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<span id="from-date-error"></span>
								@if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
							</div>

							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<label for="id_end_time">{{ __('languages.test.to_date') }}</label>
									<div class="test-list-clandr">
										<input type="text" class="form-control to-date-picker" name="to_date" value="{{ (request()->get('to_date'))}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<span id="from-date-error"></span>
								@if($errors->has('to_date'))<span class="validation_error">{{ $errors->first('to_date') }}</span>@endif
							</div>
							
							<div class="col-lg-2 col-md-3">
								<label for="id_end_time"></label>
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form>

					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec test-list-mains restrict-overflow">
							@if(!empty($CalibrationData))
								<table class="display table-responsive" style="width:100%">
							    	<thead>
							        	<tr>
										  <th><input type="checkbox" name="" class="checkbox"></th>
										  <th><span>@sortablelink('calibration_number',__('languages.calibration_number'))</span></th>
										  <th>{{__('languages.reference_adjusted_calibration_number')}}</th>
										  <th class="first-head"><span>@sortablelink('start_date',__('languages.start_date'))</span></th>
										  <th><span>@sortablelink('end_date',__('languages.end_date'))</span></th>
                                          <th><span>@sortablelink('school_ids',__('languages.no_of_school'))</span></th>
                                          <th><span>@sortablelink('student_ids',__('languages.no_of_student'))</span></th>
                                          <th><span>@sortablelink('test_type',__('languages.test.test_type'))</span></th>
                                          <th><span>@sortablelink('included_question_ids',__('languages.count_included_questions'))</span></th>
                                          <th><span>@sortablelink('excluded_question_ids',__('languages.count_excluded_questions'))</span></th>
                                          <th><span>@sortablelink('included_student_ids',__('languages.count_included_students'))</span></th>
                                          <th><span>@sortablelink('median_calibration_difficulties',__('languages.median_calibration_difficulties'))</span></th>
                                          <th><span>@sortablelink('calibration_constant',__('languages.calibration_constant'))</span></th>
                                          <th><span>@sortablelink('median_calibration_ability',__('languages.median_calibration_ability'))</span></th>
										  <th>@sortablelink('created_at',__('languages.start_date_time'))</th>
										  <th>@sortablelink('updated_at',__('languages.complete_date_time'))</th>
										  <th>{{__('languages.status')}}</th>
										  <th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@foreach($CalibrationData as $calibrationData)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
                                            <td>{{$calibrationData->calibration_number}}</td>
											<td>{{$calibrationData->ReferenceAdjustedCalibration ?? '---'}}</td>
                                            <td>{{ App\Helpers\Helper::dateConvertDDMMYYY('-','/',$calibrationData->start_date)}}</td>
                                            <td>{{ App\Helpers\Helper::dateConvertDDMMYYY('-','/',$calibrationData->end_date)}}</td>
                                            <td>{{ ($calibrationData->school_ids!= '') ? count(explode(',',$calibrationData->school_ids)) : ''}}</td>
                                            <td>{{ ($calibrationData->student_ids !="") ? count(explode(',',$calibrationData->student_ids)) : ''}}</td>
                                            <td>
												@if($calibrationData->test_type == 1)
													{{__('languages.tests')}}
												@elseif($calibrationData->test_type == 2)
													{{__('languages.testing_zone')}}
												@else
													{{__('languages.tests')}} & {{__('languages.testing_zone')}}
												@endif
											</td>
                                            <td>{{ ($calibrationData->included_question_ids!="") ? count(explode(',',$calibrationData->included_question_ids)) : ''}}</td>
                                            <td>{{ ($calibrationData->excluded_question_ids!="") ? count(explode(',',$calibrationData->excluded_question_ids)) : ''}}</td>
                                            <td>{{ ($calibrationData->included_student_ids!="") ? count(explode(',',$calibrationData->included_student_ids)) : ''}}</td>
                                            <td>
												@if($calibrationData->median_calibration_difficulties!='')
													{{App\Helpers\Helper::DisplayingDifficulties($calibrationData->median_calibration_difficulties) ?? ''}} ({{$calibrationData->median_calibration_difficulties ?? ''}})
												@else
												---
												@endif
											</td>
                                            <td>{{$calibrationData->calibration_constant ?? '---'}}</td>
                                            <td>
												@if($calibrationData->median_calibration_ability!='')
													{{App\Helpers\Helper::DisplayingAbilities($calibrationData->median_calibration_ability) }} ({{ $calibrationData->median_calibration_ability ?? ''}})
												@else
												---
												@endif
											</td>
                                            <td>{{date('d/m/Y h:i:s', strtotime($calibrationData->created_at))}}</td>
											<td>{{date('d/m/Y h:i:s', strtotime($calibrationData->updated_at))}}</td>
											<td>
												@if($calibrationData->status == 'complete')
													<span class="badge badge-info">{{ucfirst($calibrationData->status)}}</span>
												@elseif($calibrationData->status == 'adjusted')
													<span class="badge badge-danger">{{ucfirst($calibrationData->status)}}</span>
												@else
													<span class="badge badge-warning">{{ucfirst($calibrationData->status)}}</span>
												@endif
											</td>
											<td>
												@if($calibrationData->status == 'complete' || $calibrationData->status == 'adjusted')
												<a href="{{ route('ai-calibration.report', $calibrationData->id) }}" class="ml-1" title="{{__('languages.ai_calibration_report')}}">
													<i class="fa fa-bar-chart" aria-hidden="true"></i>
												</a>
												<a href="{{ route('ai-calibration.question-log', $calibrationData->id) }}" class="ml-1" title="{{__('languages.calibration_log')}}">
													<i class="fa fa-bar-chart" aria-hidden="true"></i>
												</a>
												@endif
											</td>
										</tr>
										@endforeach
							        </tbody>
								</table>
							@else
								<p>{{__('languages.data_not_found')}}</p>
							@endif
							<div>{{__('languages.showing')}} {{!empty($CalibrationData->firstItem()) ? $CalibrationData->firstItem() : 0}} {{__('languages.to')}} {{!empty($CalibrationData->lastItem()) ? $CalibrationData->lastItem() : 0}}
								{{__('languages.of')}}  {{$CalibrationData->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$CalibrationData->appends(request()->input())->links()}}
                                        @elseif((app('request')->input('items'))!= null)
                                            {{$CalibrationData->appends(request()->input())->links()}}
										@else
											{{$CalibrationData->appends(compact('items'))->links()}}
										@endif 
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$CalibrationData->total()}}" @if(app('request')->input('items') == $CalibrationData->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>

		 
		<script>
			/*for pagination add this script added by mukesh mahanto*/ 
			document.getElementById('pagination').onchange = function() {
				window.location = "{!! $CalibrationData->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection