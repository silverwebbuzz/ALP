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
								<h2 class="mb-4 main-title">{{__('languages.teacher_management.teacher_details')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('teacher_management_create', $permissions))
									<a href="{{ route('teacher.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.teacher_management.add_new_teacher')}}</a>
								@endif
								</div>
							</div>
							<hr class="blue-line">
							{{-- <a href="javascript:void(0);" class="btn btn-warning mb-4" id="massDelete">{{__('Mass Delete')}}</a> --}}
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
					<form class="addTeacherFilterForm" id="addTeacherFilterForm" method="get">	
						<div class="row">
							
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="teachername" value="{{request()->get('teachername')}}" placeholder="{{__('languages.search_by_teacher_name')}}">
									@if($errors->has('teachername'))
										<span class="validation_error">{{ $errors->first('teachername') }}</span>
									@endif
								</div>
							</div>

							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="email" value="{{request()->get('email')}}" placeholder="{{__('languages.search_by_email')}}">
									@if($errors->has('email'))
										<span class="validation_error">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="status" class="form-control select-option" id="status">
									<option value="">{{__('languages.select_status')}}</option>
									<option value="active" {{ request()->get('status') == "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
									<option value="pending" {{ request()->get('status') == "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
									<option value="inactive" {{ request()->get('status') == "inactive" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
								</select>
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox" id="selectAllTeacher">
											</th>
											
											<th class="first-head"><span>@sortablelink('name_en','Name English')</span></th>
											<th class="first-head"><span>@sortablelink('name_ch','Name Chinese')</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('email','Email')</span></th>
											<th class="selec-head">@sortablelink('status','Status')</th>
											<th class="selec-head">{{__('Action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($UsersList))
										@foreach($UsersList as $User)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox selectSingleTeacher" data-id="{{$User->id}}"></td>
											<td>{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name }}</td>
											<td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
											<td>{{ $User->email }}</td>
											<td>
												@if($User->status == "inactive")
												<span class="badge badge-warning">{{__('languages.inactive')}}</span>
												@elseif($User->status == "active")
												<span class="badge badge-success">{{__('languages.active')}}</span> 
												@else
												<span class="badge badge-primary">{{__('languages.inactive')}}</span> 
												@endif
											</td>
											<td class="btn-edit">
												@if (in_array('teacher_management_update', $permissions))
													<a href="{{ route('teacher.edit', $User->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
												@endif
												@if (in_array('teacher_management_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deleteTeacher" data-id="{{$User->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
												@endif
												@if(Auth::user()->role_id == 5)
													<a href="javascript:void(0);" class="pl-2 changeUserPassword" data-id="{{$User->id}}" title="{{__('languages.change_password')}}"><i class="fa fa-unlock" aria-hidden="true"></i></a>
												@endif
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($UsersList->firstItem()) ? $UsersList->firstItem() : 0}} {{__('languages.to')}} {{!empty($UsersList->lastItem()) ? $UsersList->lastItem() : 0}}
								{{__('languages.of')}}  {{$UsersList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$UsersList->appends(request()->input())->links()}}
										@else
											{{$UsersList->appends(compact('items'))->links()}}
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
												<option value="{{$UsersList->total()}}" @if(app('request')->input('items') == $UsersList->total()) selected @endif >{{__('languages.all')}}</option>
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
				//for per Page on filteration hidden 
				var TotalFilterData = "{!! $TotalFilterData !!}";
				if( (TotalFilterData > 0 && TotalFilterData < 11)){
						document.getElementById("pagination").style.visibility = "hidden";
						document.getElementById("per_page").style.visibility = "hidden";
				}
				/*for pagination add this script added by mukesh mahanto*/ 
				document.getElementById('pagination').onchange = function() {
						window.location = "{!! $UsersList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
		{{-- Mass Record Delete --}}
		<script>
			$(document).on('click', '#selectAllTeacher', function() {      
				$(".selectSingleTeacher").prop("checked", this.checked);
			});	
			$(document).on('click', '.selectSingleTeacher', function() {		
				if ($('.selectSingleTeacher:checked').length == $('.selectSingleTeacher').length) {
					$('#selectAllTeacher').prop('checked', true);
				} else {
					$('#selectAllTeacher').prop('checked', false);
				}
			}); 
			/*Mass Record Delete */
			$(document).on('click','#massDelete',function(){
				var records = [];  
				$(".selectSingleTeacher:checked").each(function() {  
					records.push($(this).data('id'));
				});	
				if(records.length <=0){  
					alert("Please select records.");  
				}else { 
					var selected_values = records.join(",");
					$.confirm({
						title: ARE_YOU_SURE_TO_REMOVE_THESE_RECORDS + "?",
						content: CONFIRMATION,
						autoClose: "Cancellation|8000",
						buttons: {
							deleteRecords: {
								text: ARE_YOU_SURE_TO_REMOVE_THESE_RECORDS,
								action: function () {
									$("#cover-spin").show();
									$.ajax({
										url: BASE_URL + "/mass-delete-teacher",
										type: "POST",
										data: {
											_token: $('meta[name="csrf-token"]').attr("content"),
											record_ids:selected_values
										}, 
										success: function (response) {
											$("#cover-spin").hide();
											var data = JSON.parse(JSON.stringify(response));
											if (data.status === "success") {
												var sel = false;
												var ch = $(".selectSingleTeacher:checked").each(function() { 
													var $this = $(this);
													sel = true;
													$this.closest('tr').fadeOut(function(){
														$this.remove(); //remove row when animation is finished
													});
												});
												toastr.success(data.message);
											}else {
												toastr.error(data.message);
											}
										},	
										error: function (response) {
											ErrorHandlingMessage(response);
										},
									});
								},
							},
							Cancellation: function () {},
						},
					});
				} 	  
			})
		</script>
		@include('backend.layouts.footer')

@endsection