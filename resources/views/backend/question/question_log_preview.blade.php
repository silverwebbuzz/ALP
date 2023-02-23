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
								<h2 class="mb-4 main-title">{{__('languages.question_calibration_adjustment_log')}}</h2>
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
					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec">
                            @if($QuestionLog->isNotEmpty())
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th><input type="checkbox" name="" class="checkbox"></th>
											<th>{{__('languages.calibration_adjustment_date')}}</th>
											<th>{{__('languages.calibration_number')}}</th>
											<th>{{__('languages.question_code')}}</th>
											<th>@sortablelink('previous_ai_difficulty',__('languages.previous_ai_difficulty'))</th>
							          		<th>@sortablelink('calibration_difficulty',__('languages.calibration_difficulty'))</th>
											<th>@sortablelink('change_difference',__('languages.change_difference'))</th>
											<th>@sortablelink('change_difference',__('languages.median_of_difficulty_level'))</th>
											<th><span>{{__('languages.question_log_type')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @foreach($QuestionLog as $questionPreview)
                                            <tr>
                                                <td><input type="checkbox" name="" class="checkbox"></td>
												<td>{{\App\Helpers\Helper::dateConvertDDMMYYY('-','/',$questionPreview->created_at)}}</td>
												<td>{{$questionPreview->AICalibrationReport->calibration_number}}</td>
												<td>{{$questionPreview->question->naming_structure_code}}</td>
                                                <td>{{$questionPreview->previous_ai_difficulty}}</td>
                                                <td>{{$questionPreview->calibration_difficulty}}</td>
                                                <td>{{$questionPreview->change_difference}}</td>
                                                <td>{{$questionPreview->median_of_difficulty_level}}</td>
                                                <td>
                                                    @if($questionPreview->question_log_type == 'include')
                                                        <span class="badge badge-success">{{ucfirst($questionPreview->question_log_type)}}</span>
                                                    @else
                                                        <span class="badge badge-warning">{{ucfirst($questionPreview->question_log_type)}}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
							        </tbody>
							    </table>
                            @else
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p align="center">{{__('languages.data_not_found')}}</p>
                                    </div>
                                </div>
                            @endif
								<div>{{__('languages.showing')}} {{!empty($QuestionLog->firstItem()) ? $QuestionLog->firstItem() : 0}} {{__('languages.to')}} {{!empty($QuestionLog->lastItem()) ? $QuestionLog->lastItem() : 0}}
									{{__('languages.of')}}  {{$QuestionLog->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$QuestionLog->appends(request()->input())->links()}}
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
												<option value="{{$QuestionLog->total()}}" @if(app('request')->input('items') == $QuestionLog->total()) selected @endif >{{__('languages.all')}}</option>
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
					window.location = "{!! $QuestionLog->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection