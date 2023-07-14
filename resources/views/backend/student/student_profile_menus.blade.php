@if(\App\Helpers\Helper::isStudentLogin())
<div class="row pb-4">
    <div class="col-sm-12 col-md-12 col-lg-12">
        {{-- <a href="{{ route('student.student-profiles',auth()->user()->id) }}" class="btn-search @if(request()->is('student/students-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a> --}}
        <a href="{{ route('student-profiles',auth()->user()->id) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a>
        <a href="{{ route('credit-point-history',auth()->user()->id) }}" class="btn-search mb-3 d-inline-block @if(request()->is('credit-point-history/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.credit_point_history') }}</a>
        <a href="{{ route('student.progress-report.learning-units',auth()->user()->id) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/progress-report/learning-units/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_unit_progress_short')}}</a>
        <a href="{{ route('student.progress-report.learning-objective',auth()->user()->id) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/progress-report/learning-objective/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_objectives_progress_short')}}</a>
        <a href="{{ route('student.self-learning-exercise',auth()->user()->id) }}" class="btn-search mb-3  d-inline-block @if(request()->is('student/self-learning/exercise/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.self_learning')}}</a>
        <a href="{{ route('getStudentExerciseExamList',auth()->user()->id) }}" class="btn-search mb-3  d-inline-block @if(request()->is('student/exercise/exam/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.exercises')}}</a>
        <a href="{{ route('getStudentTestExamList',auth()->user()->id) }}" class="btn-search mb-3  d-inline-block @if(request()->is('student/test/exam/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.tests')}}</a>
        <a href="{{ route('student.testing-zone',auth()->user()->id) }}" class="btn-search mb-3  d-inline-block @if(request()->is('student/testing-zone/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.common_sidebar.ai_based_assessment')}}</a>
        <a href="{{ route('start-game') }}" class="btn-search mb-3  d-inline-block @if(request()->is('student/testing-zone/*')) active-btn @else white-font inactive-btn @endif">{{ __('Games')}}</a>
    </div>
</div>
@else
<div class="row pb-4">
    <div class="col-sm-12 col-md-12 col-lg-12">
        {{-- <a href="{{ route('teacher.student-profiles',$studentId) }}" class="btn-search @if(request()->is('teacher/students-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a> --}}
        <a href="{{ route('student-profiles',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a>
        <a href="{{ route('credit-point-history',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('credit-point-history/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.credit_point_history') }}</a>
        <a href="{{ route('student.progress-report.learning-units',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/progress-report/learning-units/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_unit_progress_short')}}</a>
        <a href="{{ route('student.progress-report.learning-objective',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/progress-report/learning-objective/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_objectives_progress_short')}}</a>
        <a href="{{ route('student.self-learning-exercise',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/self-learning/exercise/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.self_learning')}}</a>
        <a href="{{ route('getStudentExerciseExamList',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/exercise/exam/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.exercises')}}</a>
        <a href="{{ route('getStudentTestExamList',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/test/exam/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.tests')}}</a>
        <a href="{{ route('student.testing-zone',$studentId) }}" class="btn-search mb-3 d-inline-block @if(request()->is('student/testing-zone/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.common_sidebar.ai_based_assessment')}}</a>
    </div>
</div>
@endif