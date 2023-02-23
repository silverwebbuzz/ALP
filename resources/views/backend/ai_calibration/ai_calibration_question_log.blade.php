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
								<h2 class="mb-4 main-title">{{__('languages.ai_calibration_question_log')}}</h2>
								@if(!empty($calibrationLogData[0]->AICalibrationReport))
                                	<h5>{{__('languages.calibration_number')}} : {{$calibrationLogData[0]->AICalibrationReport->calibration_number}} </h5>
								@endif
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
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
					<form class="calibrationFiltreation" id="calibrationFiltreation" method="get">	
						<div class="row">
							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<label>{{__('languages.question_log_type')}}</label>
									<select class="form-control" data-show-subtext="true" data-live-search="true" name="question_log_type" id="question_log_type">
										<option value=''>{{ __('languages.question_log_type') }}</option>
										@if(!empty($questionLogType))
											@foreach($questionLogType as $LogType)
											<option value="{{$LogType['id']}}" {{ request()->get('question_log_type') == $LogType['id'] ? 'selected' : '' }}>{{ $LogType['name']}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>

							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search mt-4">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form>
				 	 
					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec test-list-mains restrict-overflow">
                              @if(!empty($calibrationLogData[0]->AICalibrationReport))
								<table class="display table-responsive" style="width:100%">
                                    <thead>
                                        <th><input type="checkbox" name="" class="checkbox"></th>
                                        <th>{{__('languages.question_seed_code')}}</th>
                                        <th>{{__('languages.previous_ai_difficulty')}}</th>
                                        <th>{{__('languages.calibration_difficulty')}}</th>
                                        <th>{{__('languages.change_difference')}}</th>
                                        <th>{{__('languages.median_of_difficulty_level')}}</th>
                                        <th>{{__('languages.question_log_type')}}</th>
                                    </thead>
                                    <tbody class="scroll-pane">
                                        @foreach($calibrationLogData as $calibrationLog)
                                            <tr>
                                                <td><input type="checkbox" name="" class="checkbox"></td>
                                                <td>{{ (!empty($calibrationLog)) ? $calibrationLog->question->naming_structure_code : '' }}</td>
                                                <td>{{App\Helpers\Helper::DisplayingDifficulties($calibrationLog->previous_ai_difficulty) ?? ''}} ({{$calibrationLog->previous_ai_difficulty ?? ''}})</td>
                                                <td>{{App\Helpers\Helper::DisplayingDifficulties($calibrationLog->calibration_difficulty) ?? ''}} ({{$calibrationLog->calibration_difficulty}})</td>
                                                <td>{{$calibrationLog->change_difference}}%</td>
                                                <td>{{App\Helpers\Helper::DisplayingDifficulties($calibrationLog->median_of_difficulty_level) ?? ''}} ({{$calibrationLog->median_of_difficulty_level}})</td>
                                                <td>
                                                    @if($calibrationLog->question_log_type == "include")
                                                        <span class="badge badge-success">{{ucfirst($calibrationLog->question_log_type)}}</span> 
                                                    @else
                                                        <span class="badge badge-warning">{{ucfirst($calibrationLog->question_log_type)}}</span> 
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
							    </table>
                                @else
                                    <p align="center">{{__('languages.data_not_found')}}</p>
                                @endif
                                <div>{{__('languages.showing')}} {{!empty($calibrationLogData->firstItem()) ? $calibrationLogData->firstItem() : 0}} {{__('languages.to')}} {{!empty($calibrationLogData->lastItem()) ? $calibrationLogData->lastItem() : 0}}
                                    {{__('languages.of')}}  {{$calibrationLogData->total()}} {{__('languages.entries')}}
                                </div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$calibrationLogData->appends(request()->input())->links()}}
                                        @elseif((app('request')->input('items'))!= null)
                                            {{$calibrationLogData->appends(request()->input())->links()}}
										@else
											{{$calibrationLogData->appends(compact('items'))->links()}}
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
												<option value="{{$calibrationLogData->total()}}" @if(app('request')->input('items') == $calibrationLogData->total()) selected @endif >{{__('languages.all')}}</option>
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
				window.location = "{!! $calibrationLogData->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection