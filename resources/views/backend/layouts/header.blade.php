
@php
    // if(Auth::user()->role_id == 1){
    //     $color = '#A5A6F6';
    // }else if(Auth::user()->role_id==2){
    //     $color = '#f7bfbf';
    // }else if(Auth::user()->role_id==3){
    //     $color = '#d8dc41';
    // }else if(Auth::user()->role_id == 7){
    //     $color = '#BDE5E1';
    // }else if(Auth::user()->role_id == 8){
    //     $color = '#fed08d';
    // }else if(Auth::user()->role_id == 9){
    //     $color = '#eab676';
    // }else{
    //     $color = '#a8e4b0';
    // }
    $RoleBasedColor = \App\Helpers\Helper::getRoleBasedColor();
    $languageList = \App\Models\Languages::all();
    $SchoolDashboardController = new \App\Http\Controllers\SchoolDashboardController;
    $CurrentSchoolData = $SchoolDashboardController->GetSchoolDetailsById(auth::user()->school_id);  
@endphp
<nav class="navbar navbar-expand-lg" style="background-color:{{$RoleBasedColor['headerColor']}};">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-primary tonggel-btn">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle Menu</span>
        </button>
        <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>
    
        {{-- @if(Auth::user()->role_id != 1)
        <div class="school-profile-title">
            @if(isset($CurrentSchoolData->SchoolLogo) && !empty($CurrentSchoolData->SchoolLogo))
            <a href="javascript:void(0);" class="school-profile-button" data-toggle="modal" data-target="#school-profile-popup">
                <img src="{{$CurrentSchoolData->SchoolLogo}}" alt="logo" class="logo-icon">
            </a>
            @else
                <button type="button" class="btn btn-primary school-profile-button" data-toggle="modal" data-target="#school-profile-popup">
                    @if(app()->getLocale() == 'en')
                        {{ mb_strimwidth($CurrentSchoolData->DecryptSchoolNameEn, 0, 28, "...")}}
                    @else
                        {{ mb_strimwidth($CurrentSchoolData->DecryptSchoolNameCh, 0, 28, "...")}}
                    @endif
                </button>
            @endif
        </div>
        @endif --}}

        {{-- @if(auth()->user()->role_id == 3)
            @php
            // Get Curriculum Year list by user
            $CurriculumYearList = App\Helpers\Helper::getCurriculumYearList(auth()->user()->id);
            @endphp
            <!-- Selection for school year -->
            <div class="years-selection-dropdown-main ml-auto">
                <label>{{__('languages.school_year')}}</label>
                <select class="years-selection-dropdown selectpicker select-option" id="curriculum_year">
                    @if(isset($CurriculumYearList) && !empty($CurriculumYearList))
                    @foreach($CurriculumYearList as $CurriculumYearListKey => $curriculumYear)
                    <option value="{{$curriculumYear->id}}" @if(Auth::user()->curriculum_year_id == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <!-- End Selection for school year -->
        @endif --}}

        {{-- @if(in_array(auth()->user()->role_id,[2,5,7,1,9,8]))
            <?php $CurriculumYearList = \App\Traits\Common::GetCurriculumCurrentYear(); ?>
            <!-- Selection for school year -->
            <div class="years-selection-dropdown-main ml-auto">
                <label>{{__('languages.school_year')}}</label>
                <select class="years-selection-dropdown selectpicker select-option" id="curriculum_year">
                    @if(isset($CurriculumYearList) && !empty($CurriculumYearList))
                    @foreach($CurriculumYearList as $CurriculumYearListKey => $curriculumYear)
                    <option value="{{$curriculumYear->id}}" @if(Auth::user()->curriculum_year_id == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
        @endif --}}

        <div class="langague-dropdown">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    {{ Config::get('languages')[App::getLocale()] }}
                </a>
                <ul class="dropdown-menu">
                    @foreach($languageList as $language)
                        @if ($language['code'] != App::getLocale())
                        <li>
                            <a href="{{ route('lang.switch', $language['code']) }}">{{$language['name']}}</a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        </div>
        {{-- <div class="super-admin-title ml-3">
            <h4>{{ auth()->user()->id }} : {{ (auth()->user()->name_en) ? App\Helpers\Helper::decrypt(auth()->user()->name_en) : auth()->user()->name }}</h4>
        </div> --}}
        <!-- <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Knowledge tree</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Text book</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#"><img src="{{ asset('images/frame.png') }}" alt="icon"></a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#"><img src="{{ asset('images/men-icon.png') }}" alt="icon"></a>
            </ul>
        </div> -->
    </div>
</nav>