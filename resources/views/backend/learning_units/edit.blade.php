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
								<h2 class="mb-4 main-title">{{ __('languages.learning_units_management.update_learning_units') }}</h2>
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
                        <form class="learning-units-form" method="post" id="editLearningUnitsForm"  action="{{ route('learning_units.update',$learning_units->id) }}">
							@csrf()
                            @method('patch')
                            <div class="form-row select-data">
                                    {{-- <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.learning_units_management.name') }}</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="{{__('languages.learning_units_management.name')}}" value="{{$learning_units->name}}">
                                        @if($errors->has('name'))<span class="validation_error">{{ $errors->first('name') }}</span>@endif
                                    </div> --}}
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="stage_id">{{ __('languages.stage') }}</label>
                                        <select name="stage_id" class="form-control select-option" id="stage_id">
                                            <option value="3" {{ (3 == $learning_units->stage_id ? 'selected="selected"' : '') }}>3</option>
                                            <option value="4" {{ (4 == $learning_units->stage_id ? 'selected="selected"' : '') }}>4</option>
                                        </select>
                                         @if($errors->has('stage_id'))<span class="validation_error">{{ $errors->first('stage_id') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername_en1">{{ __('languages.learning_units_management.name_en') }}</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.learning_units_management.name_en')}}" value="{{$learning_units->name_en}}">
                                        @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername_ch1">{{ __('languages.learning_units_management.name_ch') }}</label>
                                        <input type="text" class="form-control" name="name_ch" id="name_ch" placeholder="{{__('languages.learning_units_management.name_ch')}}" value="{{$learning_units->name_ch}}">
                                        @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.learning_units_management.code') }}</label>
                                        <input type="text" class="form-control" name="code" id="code" placeholder="{{__('languages.learning_units_management.code')}}" value="{{$learning_units->code}}">
                                        @if($errors->has('code'))<span class="validation_error">{{ $errors->first('code') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="strand_id">{{ __('languages.learning_units_management.strand_id') }}</label>
                                        <select name="strand_id" class="form-control select-option" id="strand-id">
                                            <option value="">{{ __('languages.strands') }}</option>
                                            @if(!empty($strands))
                                                @foreach($strands as $strandKey => $strand)
                                                    <option value="{{$strand->id}}" {{ ($strand->id==$learning_units->strand_id ? 'selected="selected"' : '') }}>{{$strand->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_strands_available') }}</option>
                                            @endif
                                        </select>
                                         @if($errors->has('strand_id'))<span class="validation_error">{{ $errors->first('strand_id') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="1" {{($learning_units->status === "1") ? 'selected' : ''}}>{{__('languages.active')}}</option>
                                                <option value="0" {{($learning_units->status === "0") ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
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