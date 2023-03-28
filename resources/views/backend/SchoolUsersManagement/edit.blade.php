@extends('backend.layouts.app')
    @section('content')
	<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.school_user_management.update_school_user')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
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
                            <form class="add-school-user-form" method="post" id="add-school-user-form"  action="{{route('school-users.update',$user->id)}}">
                                @csrf()
                                @method('patch')
                                <div class="form-row">
                                    @if(App\Helpers\Helper::isAdmin())
                                    <div class="form-group col-md-6">                                    
                                        <label for="users-list-role">{{ __('languages.user_management.school') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="school" id="school_users_school_id">
                                            <option value=''>{{ __('languages.select_school') }}</option>
                                            @if(!empty($Schools))
                                                @foreach($Schools as $school)
                                                <option value="{{$school->id}}" @if($user->school_id == $school->id) selected @endif>{{$school->DecryptSchoolNameEn}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_available_school') }}</option>
                                            @endif
                                            </select>
                                            <span id="error-school"></span>
                                            @if($errors->has('school'))<span class="validation_error">{{ $errors->first('school') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    @endif
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.user_management.role') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="role" id="role">
                                            <option value=''>{{ __('languages.select_role') }}</option>
                                            @if(!empty($Roles))
                                                @foreach($Roles as $role)
                                                @if ($role->id != '1')
                                                    <option value="{{$role->id}}" @if($user->role_id == $role->id) selected @endif>{{$role->role_name}}</option>
                                                @endif
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_available_roles') }}</option>
                                            @endif
                                            </select>
                                            <span id="error-role"></span>
                                            @if($errors->has('role'))<span class="validation_error">{{ $errors->first('role') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.school_user_management.school_admin_privilege')}}</label>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mt-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="is_school_admin_privilege_access" id="yes" value="yes" @if($user->is_school_admin_privilege_access == 'yes') checked @endif>
                                                        <label class="custom-control-label" for="yes">{{__('languages.yes')}}</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block my-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="is_school_admin_privilege_access" id="no" value="no" @if($user->is_school_admin_privilege_access == 'no') checked @endif>
                                                        <label class="custom-control-label" for="no">{{__('languages.no')}}</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                        </ul>                                        
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.region') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="region_id" id="region_id">
                                                @if(isset($Regions) && !empty($Regions))
                                                    @foreach($Regions as $region)
                                                    <option value="{{$region->id}}" @if(isset($user->region_id) && $user->region_id == $region->id) selected @endif><?php echo $region->{'region_'.app()->getLocale()}; ?></option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('region_id'))<span class="validation_error">{{ $errors->first('region_id') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">                                
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_en">{{ __('languages.name') }}</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{ __('languages.name') }}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                        @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_ch">{{ __('languages.name') }} ({{__('languages.chinese')}})</label>
                                        <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{ __('languages.name') }} ({{__('languages.chinese')}})" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                        @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                    </div>
                                
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.email_address') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('languages.email_address') }}" value="{{$user->email}}">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.phone_number') }}</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{ __('languages.phone_number') }}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxlength="8">
                                       @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="id_end_time">{{ __('languages.status') }}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="pending" {{$user->status === "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
                                            <option value="active" {{$user->status === "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
                                            <option value="inactive" {{$user->status === "inactive" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.user_management.submit') }}</button>
                                        </div>
                                    </div>
							    </div>
							</form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')  
        <script>
            var isUserPanal = 1 ;   
            @if(old('grade_id'))
                var isUserPanalEdit = 1;
                var oldUserData = JSON.parse('{!! json_encode(Session()->getOldInput()) !!}');                
            @endif      
        </script>
        <script>
             $(document).on("change", "#role", function () {
                if(this.value == 7){
                    $('#yes').prop('checked',true);
                }else{
                    $('#no').prop('checked',true);
                }
            }) 
        </script>
@endsection