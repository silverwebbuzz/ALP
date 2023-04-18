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
								<h2 class="mb-4 main-title">{{__('View Peer Group Members')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								</div>
							</div>
							<hr class="blue-line">
							<div class="row peer-group-detail-sec">
								<div class="col-md-3">
									<span><b>{{__('languages.group_type')}} </b>: </span>
									<span>
									@if($GroupData->group_type == 'peer_group')
										{{__('languages.peer_group.peer_group')}}
									@else
										{{__('languages.group')}}
									@endif
									</span>
								</div>
								<div class="col-md-3">
									<span><b>{{__('languages.group_management.group_name')}} </b>: </span>
									<span>{{$GroupData->group_name ?? 'N/A'}}</span>
								</div>
								<!-- <div class="col-md-3">
									<span><b>{{__('languages.peer_group.peer_group_type')}} </b>: </span>
									<span>
										@if($GroupData->created_type == 'auto')
											{{__("languages.auto")}}
											@if($GroupData->auto_group_by == 0)
											({{__("languages.round_robin")}})
											@else
											({{__("languages.sequence")}})
											@endif
										@else
											{{__("languages.manual")}}
										@endif
									</span>
								</div> -->
								<div class="col-md-3">
									<span><b>{{__('languages.creator')}} </b>: </span>
									<span>
										@if(app()->getLocale() == 'en')
										{{$GroupData->CreatedGroupUser->{'DecryptNameEn'} ?? 'N/A'}}
										@else
										{{$GroupData->CreatedGroupUser->{'DecryptNameCh'} ?? 'N/A'}}
										@endif
										({{$GroupData->GetGroupCreatorRole($GroupData->CreatedGroupUser->role_id)}})
									</span>
								</div>
								<div class="col-md-3">
									<span><b>{{__('languages.status')}} </b>: </span>
									<span>
										@if($GroupData->status==1)
										{{__('languages.active')}}
										@else
										{{__('languages.inactive')}}
										@endif
									</span>
								</div>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="view-peer-group-members">						
								<table id="view-peer-group-member-table" class="display" style="width:100%">
									<thead>
										<tr>
											<th>
												<input type="checkbox" name="" class="all-select-member-checkbox" class="checkbox">
											</th>
											<th class="first-head"><span>{{__('languages.name')}}</span></th>
											<th class="first-head"><span>{{__('languages.name')}} ({{__('languages.chinese')}})</span></th>
											<th class="sec-head selec-opt"><span>{{__('languages.email_address')}}</span></th>
											<th class="selec-head">{{__('languages.form')}}</th>
											<th class="selec-head">{{__('languages.class')}}</th>
											<th class="selec-head">{{__('languages.student_code')}}</th>
										</tr>
									</thead>
									<tbody class="scroll-pane">
										@if(!empty($MembersList))
										@foreach($MembersList as $Member)
										<tr>
											<td>
												<input type="checkbox" name="memberIds[]" class="checkbox select-member-checkbox" value="{{$Member->member_id}}">
											</td>
											<td>{{($Member->member->DecryptNameEn) ? $Member->member->DecryptNameEn : $Member->member->name }}</td>
											<td>{{ ($Member->member->DecryptNameCh) ? $Member->member->DecryptNameCh : 'N/A' }}</td>
											<td>{{ $Member->member->email }}</td>
											<td class="classname_{{$Member->member_id}}">{{$Member->member->grade_id ?? 'N/A'}}</td>
											<td>{{$Member->member->CurriculumYearData->class ?? 'N/A'}}</td>
											<td>{{$Member->member->CurriculumYearData->class_student_number ?? 'N/A'}}</td>
										</tr>
										@endforeach
										@endif
								</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection