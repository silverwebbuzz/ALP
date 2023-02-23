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
								<h2 class="mb-4 main-title">{{__('languages.principal_management.add_new_principal')}}</h2>
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
                        
							<form class="principal-form" method="post" id="addPrincipalForm"  action="{{ route('principal.store') }}">
							@csrf()
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_en">{{ __('languages.user_management.name_english') }}</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.name_english')}}" value="{{old('name_en')}}">
                                        @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_ch">{{ __('languages.user_management.name_chinese') }}</label>
                                        <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{__('languages.name_chinese')}}" value="{{old('name_ch')}}">
                                        @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">                                    
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="{{__('languages.email')}}" value="{{old('email')}}" maxlength="191">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.mobile_number') }}</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.user_management.enter_the_number')}}" value="{{old('mobile_no')}}" maxlength="8">
                                       @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                   </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.password') }}</label>
                                       <input type="Password" class="form-control" name="password" id="password" placeholder="****" value="{{old('password')}}">
                                       @if($errors->has('password'))<span class="validation_error">{{ $errors->first('password') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.confirm_password') }}</label>
                                        <input type="Password" class="form-control" name="confirm_password" id="confirm_password" placeholder="****" value="{{old('password')}}">
                                        @if($errors->has('confirm_password'))<span class="validation_error">{{ $errors->first('confirm_password') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="id_end_time">{{ __('languages.status') }}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="active">{{__("languages.active")}}</option>
                                            <option value="pending">{{__("languages.pending")}}</option>
                                            <option value="inactive">{{__("languages.inactive")}}</option>
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