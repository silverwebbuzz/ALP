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
								<h2 class="mb-4 main-title">{{__('Game Bundle')}}</h2>
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
                        
							<form  method="post" id="game_bundle"  action="{{route('game-bundle')}}">
							@csrf()
                            @php 
                                $disabled = "";
                                if(!empty($BundleDetail)){
                                    $credit_points = json_decode($BundleDetail->bundle_values,true);
                                }
                                if(!empty($BundleDetail) && Auth::user()->role_id == 1 && $BundleDetail->is_admin_updated){
                                    $disabled = "disabled";
                                }
                            @endphp
                            <div class="form-row">
                                <div class="form-group col-md-2 mb-50">
                                    <label class="text-bold-600" for="credit_point_1">{{ __('10 Credit Points') }}</label>
                                    <input type="text" class="form-control" name="credit_point_1" id="credit_point_1" placeholder="{{ __('Steps') }}" value="{{!empty($credit_points ) ? $credit_points['credit_point_1'] : '' }}" {{$disabled}}>
                                    @if($errors->has('credit_point_1'))<span class="validation_error">{{ $errors->first('credit_point_1') }}</span>@endif
                                </div>
                                <div class="form-group col-md-2 mb-50">
                                    <label class="text-bold-600" for="credit_point_2">{{ __('20 Credit Points') }}</label>
                                    <input type="text" class="form-control" name="credit_point_2" id="credit_point_2" placeholder="{{ __('Steps') }}" value="{{!empty($credit_points ) ? $credit_points['credit_point_2'] : '' }}" {{$disabled}}>
                                    @if($errors->has('credit_point_2'))<span class="validation_error">{{ $errors->first('credit_point_2') }}</span>@endif
                                </div>
                                <div class="form-group col-md-2 mb-50">
                                    <label class="text-bold-600" for="credit_point_3">{{ __('30 Credit Points') }}</label>
                                    <input type="text" class="form-control" name="credit_point_3" id="credit_point_3" placeholder="{{ __('Steps') }}" value="{{!empty($credit_points ) ? $credit_points['credit_point_3'] : '' }}" {{$disabled}}>
                                    @if($errors->has('credit_point_3'))<span class="validation_error">{{ $errors->first('credit_point_3') }}</span>@endif
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