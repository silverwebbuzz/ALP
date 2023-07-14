@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
    @include('backend.layouts.sidebar')
    <div id="content" class="pl-2 pb-5">
        @include('backend.layouts.header')
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="sm-right-detail-sec pl-5 pr-5">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{__('languages.ai_calibration')}}</h2>
                                <div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								</div>
                        </div>
                        <hr class="blue-line">
                    </div>
                </div>
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
                <form name="ai-calibration-form" id="ai-calibration" action="{{ route('ai-calibration') }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 calibration-preview-tab admin-tab tab_active" data-tabid="1">1. {{__('languages.configurations')}}</li>
                                            <li class="step-headings section-step2 calibration-preview-tab admin-tab" data-tabid="2">2.{{__('languages.ai_calibration_report')}}</li>
                                            <li class="step-headings section-step3 calibration-preview-tab admin-tab" data-tabid="3">3.{{__('languages.calibration_log')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.start_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control" id="ai-calibration-start-date" name="start_date" value="" placeholder="{{__('languages.select_start_date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="start-date-error"></span>
                                            @if($errors->has('start_date'))<span class="validation_error">{{ $errors->first('start_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.end_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control" id="ai-calibration-end-date" name="end_date" value="" placeholder="{{ __('languages.select_end_date') }}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('end_date'))<span class="validation_error">{{ $errors->first('end_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.select_school') }}</label>
                                            <select name="schoolIds[]" class="form-control select-option" id="select-ai-calibration-schools" multiple>
                                                @if(isset($SchoolList) && !empty($SchoolList))
                                                    <label>{{__('languages.select_school')}}</label>
                                                    @foreach($SchoolList as $school)
                                                    <option value="{{$school->id}}">
                                                        @if(app()->getLocale() == 'en')
                                                        {{$school->DecryptSchoolNameEn}}
                                                        @else
                                                        {{$school->DecryptSchoolNameCh}}
                                                        @endif
                                                    </option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{ __('languages.no_any_school_available') }}</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50" id="student-selection">
                                            <label>{{ __('languages.select_student') }}</label>
                                            <select name="studentIds[]" class="form-control select-option" id="select-ai-calibration-students" multiple>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.select_test_type') }}</label>
                                            <select name="test_type" class="form-control select-option" id="select-ai-calibration-choose-result">
                                                <!-- <option value="">{{ __('languages.select_test_type') }}</option> -->
                                                <option value="1">{{__('languages.tests')}}</option>
                                                <option value="2">{{__('languages.testing_zone')}}</option>
                                                <option value="3">{{__('languages.tests')}} & {{__('languages.testing_zone')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_1" data-stepid="1">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step2 ai-calibration-report" style="display:none;">
                                    <div class="form-group col-md-12 mb-50" id="calibration-report-preview">
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.previous')}}</button>
                                                <a href="{{route('ai-calibration.list')}}" class="btn-back dark-blue-btn btn btn-primary" id="backButton">{{__('languages.close')}}</a>
                                                <!-- <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.next')}}</button> -->
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step3 ai-calibration-report" style="display:none;">
                                    <div class="form-group col-md-12 mb-50" id="calibration-log-report-preview">
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.previous')}}</button>
                                                <a href="{{route('ai-calibration.list')}}" class="btn-back dark-blue-btn btn btn-primary" id="backButton">{{__('languages.close')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('backend.ai_calibration.ai_calibration_js')
@include('backend.layouts.footer')
@endsection
