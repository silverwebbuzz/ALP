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
								<h2 class="mb-4 main-title">{{__('languages.add_new_calibration')}}</h2>
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
						<form method="post" id="create-calibration" action="{{ route('ai-calibration.create') }}">
							@csrf()
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{__('languages.reference_adjusted_calibration')}}</label>
                                    <select name="reference_adjusted_calibration" class="form-control select-option" id="reference_adjusted_calibration">
                                        <option value="">{{__('languages.reference_adjusted_calibration')}}</option>
                                        <option value="initial_conditions">{{__('languages.initial_condition')}}</option>
                                        @if($AdjustedCalibrationList)
                                        @foreach($AdjustedCalibrationList as $CalibrationList)
                                        <option value="{{$CalibrationList->id}}">{{$CalibrationList->calibration_number}} ({{date('d-m-Y',strtotime($CalibrationList->start_date))}} To {{date('d-m-Y',strtotime($CalibrationList->end_date))}})</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label>{{ __('languages.start_date') }}</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" id="ai-calibration-start-date" name="start_date" value="" placeholder="{{__('languages.start_date')}}" autocomplete="off" readonly>
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
                                        <input type="text" class="form-control" id="ai-calibration-end-date" name="end_date" value="" placeholder="{{ __('languages.end_date') }}" autocomplete="off">
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
                                    <label>{{ __('languages.schools') }}</label>
                                    <select name="schoolIds[]" class="form-control select-option" id="select-ai-calibration-schools" multiple>
                                        @if(isset($SchoolList) && !empty($SchoolList))
                                            <label>{{__('languages.schools')}}</label>
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
                                    <label>{{ __('languages.students') }}</label>
                                    <select name="studentIds[]" class="form-control select-option" id="select-ai-calibration-students" multiple>
                                    </select>
                                </div>

                                <div class="form-group col-md-6 mb-50">
                                    <label>{{ __('languages.type') }}</label>
                                    <select name="test_type" class="form-control select-option" id="select-ai-calibration-choose-result">
                                        <!-- <option value="">{{ __('languages.select_test_type') }}</option> -->
                                        <option value="1">{{__('languages.tests')}}</option>
                                        <option value="2">{{__('languages.ai_based_assessments')}}</option>
                                        <option value="3">{{__('languages.tests')}} & {{__('languages.ai_based_assessments')}}</option>
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
        <script>
        $( document ).ready(function() {
            /**
             * USE : Set multiselect dropdown for select schools
             */
            $("#select-ai-calibration-schools").multiselect({
                enableHTML: true,
                templates: {
                    filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                    filterClearBtn:'<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                },
                columns: 1,
                placeholder: SELECT_SCHOOL,        
                includeSelectAllOption: true,
                enableFiltering: true,
                nonSelectedText: NONE_SELECTED,
                nSelectedText: N_SELECTED_TEXT,
                allSelectedText: ALL_SELECTED,
            });

            /**
             * USE : Set multiselect dropdown for select students
             */
            $("#select-ai-calibration-students").multiselect({
                enableHTML: true,
                templates: {
                    filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                    filterClearBtn:'<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                },
                columns: 1,
                placeholder: SELECT_STUDENT,
                includeSelectAllOption: true,
                enableFiltering: true,
                nonSelectedText: NONE_SELECTED,
                nSelectedText: N_SELECTED_TEXT,
                allSelectedText: ALL_SELECTED,
            });

            /**
             * USE Set Date Formate ai-calibration start date and end date picker
             */
            $("#ai-calibration-start-date").datepicker({
                dateFormat: "dd/mm/yy",
                maxDate:0,
                changeMonth: true,
                changeYear: true,
                yearRange: "1950:" + new Date().getFullYear(),
            });

            $("#ai-calibration-start-date, #ai-calibration-end-date").datepicker({
                dateFormat: "dd/mm/yy",
                maxDate:0,
                changeMonth: true,
                changeYear: true,
                yearRange: "1950:" + new Date().getFullYear(),
            });

            /**
             * Use : Get Student list based on selected schools
             */
            $(document).on('change', '#select-ai-calibration-schools', function() {
                // Get Selected school ids
                var SchoolIds = $("select[name='schoolIds[]']").val();
                if(SchoolIds!=""){
                    $("#cover-spin").show();
                    $.ajax({
                        url: BASE_URL + "/ai-calibration/student-list",
                        method: "GET",
                        data: {
                            school_id: SchoolIds
                        },
                        success: function (response) {
                            if(response.data){
                                $('#select-ai-calibration-students').html(response.data);
                                //$("#select-ai-calibration-students").find('option').attr('selected','selected');
                                $("#select-ai-calibration-students").multiselect("rebuild");
                            }
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            $("#cover-spin").hide();
                            ErrorHandlingMessage(response);
                        }
                    });
                }
            });
        });
        </script>
        @include('backend.layouts.footer')
@endsection