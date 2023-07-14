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
								<h2 class="mb-4 main-title">{{ __('languages.student_management.update_student') }}</h2>
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
                        <form class="user-form" method="post" id="editStudentForm"  action="{{ route('Student.update',$user->id) }}">
							@csrf()
                            @method('patch')
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.form') }}</label>
                                    <select name="grade_id" class="form-control select-option" id="School-studentgrade-id">
                                        @if(!empty($grades))
                                            <option value="" disabled>{{ __('languages.select') }} {{ __('languages.form') }}</option>
                                            @if(!empty($grades))
                                                @foreach($grades as $grade)
                                                    <option value="{{$grade->id}}" @if($grade->id == $user->CurriculumYearGradeId) selected @else disabled @endif>{{ $grade->name}}</option>
                                                @endforeach
                                            @endif
                                        @else
                                            <option value="">{{ __('languages.no_grade_available') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.class') }}</label>
                                    <select name="class_id" class="form-control select-option" id="School-studentclassType-option">
                                        <option value="" disabled>{{ __('languages.select_class') }}</option>
                                        @if(!empty($user))
                                        <option value="{{$user->CurriculumYearClassId}}" selected>{{$user->class}}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.student_number') }}</label>
                                    <input type="text" class="form-control" name="student_number" id="student_number" placeholder="{{ __('languages.student_number') }}" value="{{$user->CurriculumYearData['student_number_within_class']}}">
                                    @if($errors->has('student_number'))<span class="validation_error">{{ $errors->first('student_number') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="student_number_with_class">{{ __('languages.student_code') }}</label>
                                    <input type="text" class="form-control" name="student_number_with_class" id="student_number_with_class" placeholder="{{__('languages.student_code')}}" value="{{$user->CurriculumYearData['class_student_number']}}" readonly>
                                    @if($errors->has('student_number_with_class'))<span class="validation_error">{{ $errors->first('student_number_with_class') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.std_number') }} </label>
                                    <input type="text" class="form-control" id="permanent_refrence_number" name="permanent_refrence_number" placeholder="{{ __('languages.std_number') }}" value="{{$user->permanent_reference_number}}">
                                    @if($errors->has('permanent_refrence_number'))<span class="validation_error">{{ $errors->first('permanent_refrence_number') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_en">{{ __('languages.name') }}</label>
                                    <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.name_english')}}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                    @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_ch">{{ __('languages.name') }} ({{__('languages.chinese')}})</label>
                                    <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{__('languages.name_chinese')}}" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                    @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_Ch') }}</span>@endif
                                </div>
                            </div>
                            
                                
                            <div class="form-row select-data">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.email_address') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="{{__('languages.email_address')}}" value="{{$user->email}}">
                                    @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.phone_number') }}</label>
                                    <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.phone_number')}}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxLength="8">
                                    @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.user_management.date_of_birth') }}</label>
                                    <div class="input-group date" id="id_4">
                                    <input type="text" class="form-control birthdate-date-picker" name="date_of_birth" placeholder="{{__('languages.select_date')}}" value="{{ date('d/m/Y', strtotime($user->dob)) }}" >
                                        @if($errors->has('date_of_birth'))<span class="validation_error">{{ $errors->first('date_of_birth') }}</span>@endif
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="error-dateof-birth"></span>
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.gender') }}</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mt-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="male" value="male" @if($user->gender == 'male') checked @endif >
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="male">{{ __('languages.user_management.male') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="female" value="female" @if($user->gender == 'female') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="female">{{ __('languages.user_management.female') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="other" value="other" @if($user->gender == 'other') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="other">{{ __('languages.user_management.other') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                    <span class="gender-select-err"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.address') }}</label>
                                    <textarea class="form-control" name="address" id="address" placeholder="{{__('languages.user_management.enter_the_address')}}" value="" rows=5>{{App\Helpers\Helper::decrypt($user->address)}}</textarea>
                                    @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
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
                                <label for="id_end_time">{{ __('languages.status') }}</label>
                                    <select name="status" class="form-control select-option" id="status">
                                        <option value="pending" {{ $user->status === "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
                                        <option value="active" {{ $user->status === "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
                                        <option value="inactive" {{ $user->status === "inactive" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
                                    </select>
                                </div>
                            </div>
                                
                            <div class="form-row select-data">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group col-md-6 mb-50 btn-sec">
                                        <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
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
@endsection