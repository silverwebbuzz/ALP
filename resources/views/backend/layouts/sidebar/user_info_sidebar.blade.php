<div class="sidebar_profile_user_data">
    @php
    $UserName = \App\Helpers\Helper::getUserName(Auth::user()->id);
    @endphp
    <p>{{ !empty($UserName) ? $UserName : Auth::user()->name }}</p>
    @if(Auth::user()->role_id != 1)
        <a href="javascript:void(0);" data-toggle="modal" data-target="#school-profile-popup">
            <p title="{{ \App\Helpers\Helper::getSchoolName(Auth::user()->school_id) }}">{{ \App\Helpers\Helper::getSchoolName(Auth::user()->school_id) }}</p>
        </a>
    @endif
    <p>
        @if(auth()->user()->role_id == 3)
            @php
                // Get Curriculum Year list by user
                $CurriculumYearList = App\Helpers\Helper::getCurriculumYearList(auth()->user()->id);
            @endphp
            <!-- Selection for school year -->
                <select class="years-selection-dropdown selectpicker select-option" id="curriculum_year">
                    @if(isset($CurriculumYearList) && !empty($CurriculumYearList))
                    @foreach($CurriculumYearList as $CurriculumYearListKey => $curriculumYear)
                    <option value="{{$curriculumYear->id}}" @if(Auth::user()->curriculum_year_id == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                    @endforeach
                    @endif
                </select>
            <!-- End Selection for school year -->
        @endif
        @if(in_array(auth()->user()->role_id,[2,5,7,1,9,8]))
            <?php $CurriculumYearList = \App\Traits\Common::GetCurriculumCurrentYear(); ?>
            <!-- Selection for school year -->
            <select class="years-selection-dropdown selectpicker select-option" id="curriculum_year">
                @if(isset($CurriculumYearList) && !empty($CurriculumYearList))
                @foreach($CurriculumYearList as $CurriculumYearListKey => $curriculumYear)
                <option value="{{$curriculumYear->id}}" @if(Auth::user()->curriculum_year_id == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                @endforeach
                @endif
            </select>
        @endif
    </p>
</div>