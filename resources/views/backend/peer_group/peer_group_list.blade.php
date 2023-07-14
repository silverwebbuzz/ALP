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
								<h2 class="mb-4 main-title">{{__('languages.peer_group.peer_group')}}</h2>
								@if (in_array('peer_group_create', $permissions))
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									<a href="{{ route('peer-group.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.peer_group.create_peer_group')}}</a>
									<a href="{{ route('auto-peer-group') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.peer_group.auto_create_peer_group')}}</a>
								</div>
								@endif
								
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
					<form class="addPeerGroupFilterForm" id= "addPeerGroupFilterForm" method="GET">
						<div class="row">
							<div class="col-lg-2 col-md-2">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="searchName" id="searchName" value="{{request()->get('searchName')}}" placeholder="{{__('languages.search_by_name')}}">
									@if($errors->has('searchName'))
										<span class="validation_error">{{ $errors->first('searchName') }}</span>
									@endif
								</div>
							</div>
							{{-- <div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="subject" class="form-control" id="filterSubject">
										<option value="">{{__('languages.select')}}</option>
										@if(!empty($SubjectList))
										@foreach($SubjectList as $subject)
										<option value="{{$subject->id}}" {{($subject->id == request()->get('subject')) ? 'selected' : ''}}>{{$subject->name}}</option>
										@endforeach
										@endif
									</select>
								</div>
							</div> --}}

							@if(!empty($schoolUsers) && isset($schoolUsers) && Auth::user()->role_id != 2)
								<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="creator" class="form-control" id="creator">
										<option value="">{{__('languages.select')}} {{__('languages.owner')}}</option>
										@foreach($schoolUsers as $key => $creators)
										<option value={{$creators->id}} {{($creators->id == request()->get('creator')) ? 'selected' : ''}}>{{ __(\App\Helpers\Helper::decrypt($creators->{'name_'.app()->getLocale()}))}}</option>
										@endforeach
									</select>
								</div>
								</div>
							@endif
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="status" class="form-control" id="filterStatus">
										<option value="">{{__('languages.select')}}</option>
										<option value="1" {{(1 == request()->get('status')) ? 'selected' : ''}} @if(empty(request()->get('status'))) selected @endif>{{__('languages.active')}}</option>
										<option value="0" {{('0' == request()->get('status')) ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="group_type" class="form-control" id="group_type">
										<option value="">{{__('languages.select')}}</option>
										<option value="peer_group" {{("peer_group" == request()->get('group_type')) ? 'selected' : ''}}>{{__('languages.peer_group.peer_group')}}</option>
										<option value="group" {{("group" == request()->get('group_type')) ? 'selected' : ''}} @if(empty(request()->get('group_type'))) selected @endif>{{__('languages.group')}}</option>
									</select>
								</div>
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
							<div  class="question-bank-sec">
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox" id="selectAllPeerGroup">
											</th>
											<th>{{__('languages.id')}}</th>
											<th>{{__('languages.group_type')}}</th>
											<th>@sortablelink('group_name',__('languages.peer_group.group_name'))</th>
											<th>{{__('languages.peer_group.no_of_members')}}</th>
											<th>{{__('languages.owner')}}</th>
											<th>{{__('languages.owner')}} {{__('languages.role')}}</th>
                                            <th>@sortablelink('status',__('languages.peer_group.status'))</th>
											<th>{{__('languages.peer_group.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($PeerGroupList))
										@foreach($PeerGroupList as $peerGroup)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox selectSinglePeerGroup" data-id="{{$peerGroup->id}}"></td>
											<td>{{$loop->iteration}}</td>
											<td>
												@if($peerGroup->group_type == 'peer_group')
													{{__('languages.peer_group.peer_group')}}
												@else
													{{__('languages.group')}}
												@endif
											</td>
											<td>{{$peerGroup->group_name}}</td>
											<td>{{($peerGroup->members) ? count($peerGroup->Members) : 'N/A'}}</td>
											<td>{{App\Helpers\Helper::getUserName($peerGroup->created_by_user_id) ?? ''}}</td>
											<td>{{App\Helpers\Helper::FindRoleByUserId($peerGroup->created_by_user_id) ?? ''}}</td>
											
                                            <td>
												@if($peerGroup->status=="1")
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
												@if(in_array('peer_group_update', $permissions))
													@if($peerGroup->created_by_user_id == Auth::user()->id
													|| Auth::user()->role_id == 5
													|| Auth::user()->role_id == 7
													|| Auth::user()->role_id == 8
													|| Auth::user()->role_id == 9
													)
														<a href="{{ route('peer-group.edit', $peerGroup->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i></a>
													@endif
												@endif

												@if(in_array('peer_group_delete', $permissions))
													@if($peerGroup->created_by_user_id == Auth::user()->id || $peerGroup->status == 0)
														<a href="javascript:void(0);" class="pl-2 deletePeerGroup" data-id="{{$peerGroup->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>
													@endif
												@endif

												<a href="{{route('peer-group.view.members',$peerGroup->id)}}" id="view-peer-group-members" title="{{__('languages.view_members')}}">
													<i class="fa fa-users fa-lg"></i>
												</a>

												@if(count($peerGroup->members) >= 1)
												<a href="javascript:void(0);" class="pl-2 alp_chat_icon" data-AlpChatGroupId="{{$peerGroup->dreamschat_group_id}}" title="{{__('languages.peer_group.alp_chat')}}">
													{{-- <img src="{{asset('images/alp_chat.png')}}"/> --}}
													<i class="fa fa-comments fa-lg" aria-hidden="true"></i>
												</a>
												@endif
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($PeerGroupList->firstItem()) ? $PeerGroupList->firstItem() : 0}} {{__('languages.to')}} {{ !empty($PeerGroupList->lastItem()) ? $PeerGroupList->lastItem() : 0}}
									{{__('languages.of')}}  {{$PeerGroupList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$PeerGroupList->appends(request()->input())->links()}}
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
												<option value="{{$PeerGroupList->total()}}" @if(app('request')->input('items') == $PeerGroupList->total()) selected @endif >{{__('languages.all')}}</option>
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
				window.location = "{!! $PeerGroupList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		<script>
			$(document).on('click', '#selectAllPeerGroup', function() {      
				$(".selectSinglePeerGroup").prop("checked", this.checked);
			});	
			$(document).on('click', '.selectSinglePeerGroup', function() {		
				if ($('.selectSinglePeerGroup:checked').length == $('.selectSinglePeerGroup').length) {
					$('#selectAllPeerGroup').prop('checked', true);
				} else {
					$('#selectAllPeerGroup').prop('checked', false);
				}
			}); 
			/*Mass Record Delete */
			$(document).on('click','#massDelete',function(){
				var records = [];  
				$(".selectSinglePeerGroup:checked").each(function() {  
					records.push($(this).data('id'));
				});	
				if(records.length <=0){  
					// alert("Please select records."); 
					toastr.error(PLEASE_SELECT_RECORD); 
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
										url: BASE_URL + "/mass-delete-peer-peer-group",
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
												var ch = $(".selectSinglePeerGroup:checked").each(function() { 
													var $this = $(this);
													sel = true;
													$this.closest('tr').fadeOut(function(){
														$this.remove();
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