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
                @if(session()->has('success_msg'))
                <div class="alert alert-success">{{ session()->get('success_msg') }}</div>
                @endif
                @if(session()->has('error_msg'))
                <div class="alert alert-danger">{{ session()->get('error_msg') }}</div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{__('languages.my_studies.questions')}}</h2>
                        </div>
                        <hr class="blue-line">
                    </div>
                </div>
                <div class="alert alert-warning mb-5">{{\App\Helpers\Helper::getGlobalConfiguration('attempt_exam_restrict_notification_'.app()->getLocale())}}</div>
                @if($examType == 'single')
                <form class="changeLanguageExamForm" id="changeLanguageExamForm" method="post">
                    @csrf()
                    <input type="hidden" name="SelectedAnswersArray" value="" id="SelectedAnswersArray">
                    <input type="hidden" name="exam_taking_timing" value="" class="taking_exam_timing">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 attmp-main-timer">
                            <div class="select-lng w-25">
                                <select name="language" class="form-control select-option" id="student-select-attempt-exam-language" data-examid="{{$examDetail->id}}" @if(request()->get("language") === "ch") disabled @endif>
                                    <option value="">{{ __('languages.my_studies.select_exam_language') }}</option>
                                    <option value="en" @if(request()->get("language") === "en") selected @endif @if(!isset($_GET['language'])) selected @endif>{{ __('languages.english') }}</option>
                                    <option value="ch" @if(request()->get("language") === "ch") selected @endif>{{ __('languages.chinese') }}</option>
                                </select>
                                @if($errors->has('language'))
                                    <span class="validation_error">{{ $errors->first('language') }}</span>
                                @endif
                            </div>
                            <div class="exam_timer_section">
                                <div class="attmp-timer-out-inr">
                                    @if($totalSeconds != 'unlimited_time')
                                        <h5 class="exam_time_limit_label">{{__('languages.time_limit')}} : </h5>
                                        <span id="ExamTimerOut">@if(isset($totalSeconds)) {{sprintf('%02d:%02d:%02d', ($totalSeconds/ 3600),($totalSeconds/ 60 % 60), $totalSeconds% 60)}} @else 00:00:00 @endif</span>
                                    @endif
                                </div>
                                <div class="attmp-timer-inr">
                                    <h5>{{__('languages.my_studies.exam_time')}}: </h5>
                                    <p><span id="ExamTimer">@if(isset($taking_exam_timing)) {{$taking_exam_timing}} @else 00:00:00 @endif</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <form class="attempt-exams" method="post" id="attempt-exams" action="{{route('student.answer.save')}}" onsubmit="return @if($testType==1) attempt_personal_exams(); @else attempt_exams(); @endif">
                    <input type="hidden" name="exam_taking_timing" value="" class="taking_exam_timing">
                    <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                    <input type="hidden" name="language" value="{{ $examLanguage }}"/>
                    <input type="hidden" name="questions_ans" id="questions_ans">
                    <input type="hidden" name="wrong_ans" id="wrong_ans">
                    <input type="hidden" name="attempt_ans" id="attempt_ans">
                    <input type="hidden" name="wrong_ans_id" id="wrong_ans_id">
                    <input type="hidden" name="attempt_first_trial_data_new" id="attempt_first_trial_data_new">
                    <input type="hidden" name="before_exam_survey" id="before_exam_survey"/>
                    <input type="hidden" name="after_exam_survey" id="after_exam_survey"/>
                    @csrf()
                    <div id="nextquestionarea"  class="@if($testType==1) test-personal @else test-all @endif">
                    @if(!empty($Questions))
                    @foreach($Questions as $question)
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
                                <div class="attmp-main-que">
                                    <h4>Q-1</h4>
                                    <div class="attmp-que">
                                       @php echo $question->{'question_'.$examLanguage}; @endphp
                                    </div>
                                </div>
                                <div class="attmp-main-answer">
                                    @php
                                        $random_number_array = range(1,4);
                                        shuffle($random_number_array );
                                        $random_number_array = array_slice($random_number_array ,0,4);
                                    @endphp
                                    @if(isset($question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[0]}}" class="radio mr-2 checkanswer" @if((!empty($SelectedAnswersArray) && isset($SelectedAnswersArray->{'ans_que_'.$question->id}) && $SelectedAnswersArray->{'ans_que_'.$question->id} == $random_number_array[0]) || $selectedOldAnswer == $random_number_array[0]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="1" >
                                            <div class="answer-title mr-2">A</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[1]}}" class="radio mr-2 checkanswer" @if((!empty($SelectedAnswersArray) && isset($SelectedAnswersArray->{'ans_que_'.$question->id}) && $SelectedAnswersArray->{'ans_que_'.$question->id} == $random_number_array[1]) || $selectedOldAnswer == $random_number_array[1]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="2" >
                                            <div class="answer-title mr-2">B</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[2]}}" class="radio mr-2 checkanswer" @if((!empty($SelectedAnswersArray) && isset($SelectedAnswersArray->{'ans_que_'.$question->id}) && $SelectedAnswersArray->{'ans_que_'.$question->id} == $random_number_array[2]) || $selectedOldAnswer == $random_number_array[2]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="3" >
                                            <div class="answer-title mr-2">C</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[3]}}" class="radio mr-2 checkanswer" @if((!empty($SelectedAnswersArray) && isset($SelectedAnswersArray->{'ans_que_'.$question->id}) && $SelectedAnswersArray->{'ans_que_'.$question->id} == $random_number_array[3]) || $selectedOldAnswer == $random_number_array[3]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="4" >
                                            <div class="answer-title mr-2">D</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if (isset($examDetail->exam_type) && $examDetail->exam_type == 2)
                                <div class="attmp-main-explain">
                                    <div class="attmp-expln-inner questionhint" style="display: none">
                                        <h5>{{__('languages.my_studies.explain')}}</h5>
                                        <p id="questionhint"></p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-all-button">
                                {{-- <div class="attmp-prev-btn attmp-butns">
                                    <button type="button" class="btn btn-info mr-2" id="prevquestion" question-id-prev="{{$Questions[0]['id']}}" disabled >{{__('languages.my_studies.previous')}}</button>
                                </div> --}}
                                 @if(isset($questionSize) && $questionSize>1)
                                <div class="attmp-next-btn attmp-butns">
                                    <button type="button" class="btn btn-warning mr-2" id="nextquestion" question-id-next="{{$Questions[0]['id']}}" @if($selectedOldAnswer == '') disabled @endif data-text="Next" >{{__('languages.my_studies.next')}}</button>
                                </div>
                                @endif
                                <div class="attmp-submit-btn attmp-butns">
                                    <button type="submit" class="btn btn-success mr-2"   style="display:none;" id="submitquestion" @if($selectedOldAnswer=='') disabled @endif>{{__('languages.submit')}}</button>
                                </div>
                                @if (isset($examDetail->exam_type) && ($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)))
                                <div class="">
                                    @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                                        <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                    @else
                                        <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div> 
                        @endif
                                                
                        @if (isset($examDetail->exam_type) && ($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)))
                            <!-- Want a hint Modal -->
                            <div class="modal fade" id="WantAHintModal" tabindex="-1" aria-labelledby="WantAHintModal" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        @if(isset($UploadDocumentsData) && empty($UploadDocumentsData))
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif
                                        
                                        @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                                        <div class="modal-body  embed-responsive embed-responsive-16by9">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <iframe class="embed-responsive-item " id="videoDis" frameborder="0" allowtransparency="true" allowfullscreen width="100%" height="400" ></iframe>
                                        </div>
                                        @else
                                        <div class="modal-body">
                                            @if($examLanguage=='ch')
                                                @if(trim($question->general_hints_ch)!="")
                                                {!! $question->general_hints_ch !!}
                                                @else
                                                {{__('languages.hint_not_available')}}
                                                @endif
                                            @else
                                                @if(trim($question->general_hints_en)!="")
                                                {!! $question->general_hints_en !!}
                                                @else
                                                {{__('languages.hint_not_available')}}
                                                @endif
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="AttemptQuestionFeedback" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-xl" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{__('Exam Survey')}}</h5>
            </div>
            <div class="modal-body " id="AttemptQuestionFeedbackBody">
                <form class="smileys">
                    <input type="hidden" id="isAfterSubmit" value="" /> 
                    <input type="radio" name="smiley" value="1" class="sad emojisButton" onChange="Emoji();">
                    <input type="radio" name="smiley" value="2" class="happy emojisButton" onChange="Emoji();">
                </form>
            </div>
        </div>
    </div>
</div>
{{-- End Modal --}}

<script>
    $(document).ready(function() {
        if($.cookie('SetfeedbackEmoji')!=1){
            $("#BeforeExamSurvayOrAfterExamSurvay").val("BeforeSurvey");
            $('#AttemptQuestionFeedback').modal('show');
        }
    });
    function EmojiPopupHtml() {
    var htmlEmojiPopup =
        "<form class='smileys'>" +
        "<input type='hidden' id='isAfterSubmit' value='' />" +
        "<input type='radio' name='smiley' value='1' class='sad emojisButton' onChange='Emoji();'>" +
        "<input type='radio' name='smiley' value='2' class='happy emojisButton' onChange='Emoji();'>" ;//+
        "</form>";
    return htmlEmojiPopup;
}

function Emoji() {
    $selectedEmoji = $("input[type=radio][name=smiley]:checked").val();
    // Set Cookie for emoji after refresh can not open
    $.cookie('SetfeedbackEmoji',1);
    if ($("#isAfterSubmit").val() != 1 && $("#isAfterSubmit").val() != 2) {
        $("#before_exam_survey").val($selectedEmoji);
        $("#AttemptQuestionFeedback").modal("hide");
        closePopupModal("AttemptQuestionFeedback");
    }
    if ($("#isAfterSubmit").val() == 1) {
        $("#after_exam_survey").val($selectedEmoji);
        $("#AttemptQuestionFeedback").modal("hide");
        closePopupModal("AttemptQuestionFeedback");
        $("#attempt-exams").attr('onsubmit', true);
        $("#attempt-exams").submit();
        $("#cover-spin").show();
    }
}
</script>
<script type="text/javascript">
    // set variable in self learning test type
    var student_test_type='{{$testType}}';
    var self_learning_test_type=0;
    var time_duration=0;
    @if(isset($examDetail->self_learning_test_type))
        self_learning_test_type='{{$examDetail->self_learning_test_type}}';
        time_duration='{{$examDetail->time_duration}}';
    @endif
</script>
<script>var second = 0;var QuestionSecond=0;$QuestionTimer=0;</script>
<script>var totalSeconds = '@php echo $totalSeconds; @endphp';</script>
@php if(isset($taking_exam_timing)){ @endphp
<script>
    var hms = '@php echo $taking_exam_timing; @endphp';   // your input string
    var a = hms.split(':'); // split it at the colons
    // minutes are worth 60 seconds. Hours are worth 60 minutes.
    var second = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
</script>
@php } @endphp
@include('backend.layouts.footer')
<script>
    var i=0;
    $( document ).ready(function() {
        
        // $(document).on('keydown', function(e) {
        //     // F5 is pressed
        //     if((e.which || e.keyCode) == 116) {
        //         disableKeyPressing(e);
        //         console.log('F5 is disabled now');
        //     }
        
        //     // Ctrl+R
        //     if (e.ctrlKey && (e.which === 82) ) {
        //         disableKeyPressing(e);
        //         console.log('Ctrl+R is pressed and refresh is disabled now');
        //     }
        // });

        // $(document).bind("contextmenu",function(e){
        //     return false;
        // });

        // This is check is personal exam and exam type is test
        //if(student_test_type ==1 && self_learning_test_type == 2){
            examTimerReverse();
        //}
        examTimer();
        examQuestionTimer();

        //$(".attmp-exam-main .checkanswer").attr('checked',false);
    });

    // Create function Exam Timer
    function examTimer() {
        var myTimer = setInterval(myClock, 1000);
        function myClock(){
            $timer = secondsTimeSpanToHMS(++second);
            $('.taking_exam_timing').val($timer);
            $('#ExamTimer').text($timer);
        }
    }

    // This function Exam Question Timer
    function examQuestionTimer() {
        var myTimer = setInterval(myQuestionClock, 1000);
        function myQuestionClock(){
            $QuestionTimer = secondsTimeSpanToHMS(++QuestionSecond);
        }
    }

    // Create function Exam Timer Reverse
    function examTimerReverse() {
        var myTimer = setInterval(myTimerOut, 1000);
        function myTimerOut() {
            if(totalSeconds !== 'unlimited_time'){
                totalSeconds--;
                if(totalSeconds == 0){
                    $.confirm({
                        title: YOUR_TIME_IS_OVER,
                        content: CONFIRMATION,
                        autoClose: 'Cancellation|3000',
                        buttons: {
                            Cancellation: function() {
                                $("#attempt-exams").submit();
                            },
                        },
                    });
                }else{
                    if(totalSeconds == (60 * 5)){
                        $.confirm({
                            title: TIME_OUT_IN_5_MINUTES,
                            content: CONFIRMATION,
                            autoClose: 'Cancellation|3000',
                            buttons: {
                                Cancellation: function() {},
                            },
                        });
                    }
                    if(totalSeconds == 60){
                        $.confirm({
                            title: TIME_OUT_IN_1_MINUTES,
                            content: CONFIRMATION,
                            autoClose: 'Cancellation|3000',
                            buttons: {
                                Cancellation: function() {},
                            },
                        });
                    }
                }
            }
        }
    }

    function secondsTimeSpanToHMS(second) {
        var h = Math.floor(second / 3600); //Get whole hoursecond
        second -= h * 3600;
        var m = Math.floor(second / 60); //Get remaining minutesecond
        second -= m * 60;
        return h + ":" + (m < 10 ? '0' + m : m) + ":" + (second < 10 ? '0' + second : second); //zero padding on minutes and seconds
    }

    function getYoutubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    $(document).on('click', '.video-img-sec', function() {
        var videoSrc = $(this).data( "src" );
        var domain = videoSrc.replace('http://','').replace('https://','').split(/[/?#]/)[0];
        if (videoSrc.indexOf("youtube") != -1) {
            const videoId = getYoutubeId(videoSrc);
            $("#videoDis").attr('src','//www.youtube.com/embed/'+videoId);
        }else if (videoSrc.indexOf("vimeo") != -1) {
            const videoId = getYoutubeId(videoSrc);
            var matches = videoSrc.match(/vimeo.com\/(\d+)/);
            $("#videoDis").attr('src','https://player.vimeo.com/video/'+matches[1]);
        }else if (videoSrc.indexOf("dailymotion") != -1) {
            var m = videoSrc.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
            if (m !== null) {
                if(m[4] !== undefined) {
                    $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
                }
                $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
            }
        }else{
            $("#videoDis").attr('src','/'+videoSrc);
        }
    });

    // Disable key press
    // function disableKeyPressing(e) {
    //     // keycodes table https://css-tricks.com/snippets/javascript/javascript-keycodes/
    //     var conditions = [
    //         // Diable F5
    //         (e.which || e.keyCode) == 116,
    //         // Diable Ctrl+R
    //         e.ctrlKey && (e.which === 82)
    //     ]

    //     if ( $.each(conditions, function(key, val) { val + ' || ' }) ) {
    //         e.preventDefault();
    //     }
    // }

    // F5 is pressed
    // $(document).on('keydown', function(e) {
    //     // F5 is pressed
    //     if((e.which || e.keyCode) == 116) {
    //         disableKeyPressing(e);
    //         console.log('F5 is diabled now');
    //     }

    //     // Ctrl+R
    //     if (e.ctrlKey && (e.which === 82) ) {
    //         disableKeyPressing(e);
    //         console.log('Ctrl+R is pressed and refresh is diabled now');
    //     }
    // });

    /**
     * Change Attempt Exam flow 
     */
    var questionNo = 1;
    var attempt_ans = 0;
    var question_old_data_new = new Array();
    var question_position = new Array();
    var wrong_question_old_data_new = new Array();
    var attempt_first_trial_data_new = new Array();
    var wrong_old_data_new = 0;
    var questiontype = 0;
    var isReAttempt = true;
    $( document ).ready(function() {
        /**
         * USE : Student can select exam language based on changes list of question answer language
         * **/
        $(document).on("change","#student-select-attempt-exam-language",function () {
            if (this.value == "") {
                toastr.error(PLEASE_SELECT_LANGUAGE);
                return false;
            }
            var AnswerNameList = [];
            $("input:radio").each(function () {
                var rname = $(this).attr("name");
                if ($.inArray(rname, AnswerNameList) === -1)
                    AnswerNameList.push(rname);
            });
            $("#attempt-exams input[name=language]").val($(this).val());
            var question_id = $("#nextquestionarea .checkanswer").attr("question-id");
            var SelectedAnswersArray = {};
            $.each(AnswerNameList, function (key, questionKey) {
                SelectedAnswersArray[questionKey] = $("input[type='radio'][name='" +questionKey +"']:checked").val();
            });
            $("#SelectedAnswersArray").val(JSON.stringify(SelectedAnswersArray));
            if ($("#nextquestionarea .checkanswer:checked").length != 0) {
                var answer = $("#nextquestionarea .checkanswer").val();
                var questionid = $("#nextquestionarea .checkanswer").attr("question-id");
                var submitid = $("#submitquestion").attr("submit-id");
                questiontype = $("#nextquestionarea .checkanswer").attr("question-type");
                var language = $("#attempt-exams input[name=language]").val();
                var checkarns = 0;
                question_arr = {
                    question_id: questionid,
                    answer: answer,
                    language: language,
                };
                if (question_old_data_new.length != 0) {
                    for (var ic = 0;ic < question_old_data_new.length;ic++) {
                        if (question_old_data_new[ic]["question_id"] ==questionid) {
                            checkarns++;
                            question_old_data_new[ic] = question_arr;
                        }
                    }
                    if (checkarns == 0) {
                        question_old_data_new.push(question_arr);
                    }
                } else {
                    question_old_data_new.push(question_arr);
                }
            }
            $examId = $(this).attr("data-examid");
            if ($examId) {
                $("#cover-spin").show();
                $.ajax({
                    url:
                        BASE_URL + "/student/exam/change-language/" + $examId,
                    type: "GET",
                    data: {
                        language: this.value,
                        question_id: question_id,
                        SelectedAnswersArray: SelectedAnswersArray,
                        questionNo: questionNo,
                        question_position: question_position,
                    },
                    success: function (response) {
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            $("#nextquestionarea").html(data.data.html);
                            //updateMathHtml();
                            //updateMathHtmlById("nextquestionarea");
                            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                            if (
                                $("#nextquestionarea .checkanswer:checked")
                                    .length != 0
                            ) {
                                $("#nextquestion").prop("disabled", false);
                                if ($("#nextquestion").length == 0) {
                                    $("#submitquestion").prop("disabled",false);
                                    $("#submitquestion").show();
                                }
                            }
                        } else {
                            toastr.error(data.message);
                        }
                        $("#cover-spin").hide();
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
            return false;
        });

        // Check all attempt students exams validation
        $(document).on("click", "#submit-exams", function (event) {
            //Make groups
            var names = [];
            $("input:radio").each(function () {
                var rname = $(this).attr("name");
                if ($.inArray(rname, names) === -1) names.push(rname);
            });

            //do validation for each group
            $status = true;
            $.each(names, function (i, name) {
                $("#" + name).text("").removeClass("error-option");
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    $status = false;
                    $("#" + name).text("Please select answer").addClass("error-option");
                } else {
                    if ($status) {
                        $status = true;
                    }
                }
            });
            if (!$status) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $(".error-option").offset().top,
                },1000);
                return false;
            }
            $("#attempt-exams").submit();
            $("#submit-exams").prop("disabled", true);
        });

        /**
         * USE : Use To Check Answer Is Right Or Not
         */
        var wrong = 0;
        var questionIdArr = [];
        $(document).on("change", ".test-all .checkanswer", function () {
            $(".questionhint").hide();
            $("#nextquestion").prop("disabled", false);
            $("#submitquestion").hide();
            if($("#nextquestion").length == 0) {
                $("#submitquestion").prop("disabled", false);
                $("#submitquestion").show();
            }
            var examid = $("input[name=exam_id]").val();            
            var answer = $(this).val();
            var questionid = $(this).attr("question-id");
            var language = $("#attempt-exams input[name=language]").val();
             // Store Student Exam History Detail
            StoreStudentExamDetail(examid,questionid,answer,language);
            var submitid = $("#submitquestion").attr("submit-id");
            questiontype = $(this).attr("question-type");
            
            var checkarns = 0;
            question_arr = {
                question_id: questionid,
                answer: answer,
                language: language,
                duration_second: 0,
            };

            // Set Question Position Start
            var question_position_number = [];
            $(this).closest(".attmp-main-answer").find(".checkanswer").each(function () {
                question_position_number.push($(this).val());
            });
            var questionPositionNumberJson = JSON.stringify(question_position_number);
            if (question_position.length != 0) {
                checkarnsPo = 0;
                for (var ip = 0; ip < question_position.length; ip++) {
                    if (question_position[ip]["question_id"] == questionid) {
                        checkarnsPo++;
                        question_position_arr = {
                            question_id: questionid,
                            position: questionPositionNumberJson,
                        };
                        question_position[ip] = question_position_arr;
                    }
                }
                if (checkarns == 0) {
                    var question_position_arr = {
                        question_id: questionid,
                        position: questionPositionNumberJson,
                    };
                    question_position.push(question_position_arr);
                }
            } else {
                var question_position_arr = {
                    question_id: questionid,
                    position: questionPositionNumberJson,
                };
                question_position.push(question_position_arr);
            }
            // Set Question Position End

            if (question_old_data_new.length != 0) {
                for (var ic = 0; ic < question_old_data_new.length; ic++) {
                    if ( question_old_data_new[ic]["question_id"] == questionid) {
                        checkarns++;
                        question_arr = {
                            question_id: questionid,
                            answer: answer,
                            language: language,
                            duration_second:
                                question_old_data_new[ic]["duration_second"],
                        };
                        question_old_data_new[ic] = question_arr;
                    }
                }
                if (checkarns == 0) {
                    question_old_data_new.push(question_arr);
                }
            } else {
                question_old_data_new.push(question_arr);
            }
        });

        /**
         * USE : Use Next or Previous Question
         */
        $(document).on("click",".test-all #nextquestion,.test-all #prevquestion",function (e) {
            // If user click on the next button then system will trigger on click selected radio button
            $(".test-all .checkanswer:checked").trigger('change');
            $("#cover-spin").show();
            var is_err = 0;
            var examaction = $(this).attr("data-text");
            if(examaction == "Next"){
                questiontype = $(".checkanswer:checked").attr("question-type");
                if (questiontype == 2 || questiontype == 1) {
                    var is_err = $(this).attr("iserr");
                }
                var currentid = $(this).attr("question-id-next");
                questionNo = questionNo + 1;
            }else{
                var currentid = $(this).attr("question-id-prev");
                questionNo = questionNo - 1;
            }
            var wrong_old_data_new = $("#wrong_ans_id").val();
            var examid = $("input[name=exam_id]").val();
            var language = $("input[name=language]").val();

            // set question duration in second start
            if (examaction == "Next") {
                var answer = $(".checkanswer:checked").val();
                var questionid = $(".checkanswer:checked").attr("question-id");
                var submitid = $("#submitquestion").attr("submit-id");
                var language = $("#attempt-exams input[name=language]").val();
                if(question_old_data_new.length != 0){
                    for ( var ics = 0; ics < question_old_data_new.length; ics++) {
                        if ( question_old_data_new[ics]["question_id"] == questionid && question_old_data_new[ics]["duration_second"] == 0) {
                            question_arr = {
                                question_id: questionid,
                                answer: answer,
                                language: language,
                                duration_second: $QuestionTimer,
                            };
                            question_old_data_new[ics] = question_arr;
                        }
                    }
                }
                QuestionSecond = 0;
            }
            if(examaction == "Previous"){
                var answer = $(".checkanswer:checked").val();
            }
            // send ajax to get data
            $.ajax({
                url: BASE_URL + "/next-question",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    currentid: currentid,
                    examid: examid,
                    examaction: examaction,
                    questionNo: questionNo,
                    language: language,
                    wrong_ans: wrong_question_old_data_new,
                    attempt_ans: attempt_ans,
                    // Get Question Position
                    question_position: question_position,
                },
                success: function (response) {
                    //$("#cover-spin").hide();
                    $("#nextquestionarea").html(response);
                    if (question_old_data_new.length != 0) {
                        for (var icn = 0; icn < question_old_data_new.length;icn++) {
                            var question_id_check = $("#nextquestionarea .checkanswer").attr("question-id");
                            if (question_old_data_new[icn]["question_id"] == question_id_check) {
                                var answer_ren = question_old_data_new[icn]["answer"];
                                $("#nextquestionarea .checkanswer[value=" + answer_ren + "]" ).prop("checked", true);
                                $("#nextquestion").prop("disabled", false);
                                if ($("#nextquestion").length == 0) {
                                    $("#submitquestion").prop( "disabled", false);
                                    $("#submitquestion").show();
                                }
                            }
                        }
                    }
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    $("#cover-spin").hide();
                },
            });
        });

        /**
         * USE : Use Check Answer Question in Personal Exam
         */
        $(document).on("change", ".test-personal .checkanswer", function () {
            $(".questionhint").hide();
            $("#nextquestion").prop("disabled", false);
            $("#submitquestion").hide();
            // $('#submitquestion').prop('disabled', true);
            if ($("#nextquestion").length == 0) {
                $("#submitquestion").prop("disabled", false);
                $("#submitquestion").show();
            }
            var examid = $("input[name=exam_id]").val();
            var answer = $(this).val();
            var questionid = $(this).attr("question-id");
            var submitid = $("#submitquestion").attr("submit-id");
            questiontype = $(this).attr("question-type");
            var language = $("#attempt-exams input[name=language]").val();
            // Store Student Exam History Detail
            StoreStudentExamDetail(examid,questionid,answer,language);
            var checkarns = 0;
            question_arr = {
                question_id: questionid,
                answer: answer,
                language: language,
                duration_second: 0,
            };

            // Set Question Position Start
            var question_position_number = [];
            $(this).closest(".attmp-main-answer").find(".checkanswer").each(function () {
                question_position_number.push($(this).val());
            });

            var questionPositionNumberJson = JSON.stringify(question_position_number);
            if(question_position.length != 0){
                checkarnsPo = 0;
                for(var ip = 0; ip < question_position.length; ip++){
                    if(question_position[ip]["question_id"] == questionid){
                        checkarnsPo++;
                        question_position_arr = {
                            question_id: questionid,
                            position: questionPositionNumberJson,
                        };
                        question_position[ip] = question_position_arr;
                    }
                }
                if(checkarns == 0){
                    var question_position_arr = {
                        question_id: questionid,
                        position: questionPositionNumberJson,
                    };
                    question_position.push(question_position_arr);
                }
            }else{
                var question_position_arr = {
                    question_id: questionid,
                    position: questionPositionNumberJson,
                };
                question_position.push(question_position_arr);
            }
            // Set Question Position End
            if(question_old_data_new.length != 0){
                for(var ic = 0; ic < question_old_data_new.length; ic++){
                    if(question_old_data_new[ic]["question_id"] == questionid){
                        checkarns++;
                        question_arr = {
                            question_id: questionid,
                            answer: answer,
                            language: language,
                            duration_second:
                                question_old_data_new[ic]["duration_second"],
                        };
                        question_old_data_new[ic] = question_arr;
                    }
                }
                if(checkarns == 0){
                    question_old_data_new.push(question_arr);
                }
            }else{
                question_old_data_new.push(question_arr);
            }
        });

        /**
         * USE : Use Next or Previous Question in Personal Exam
         */
        $(document).on("click",".test-personal #nextquestion,.test-personal #prevquestion",function (e) {
            $("#cover-spin").show();
            $(".test-personal .checkanswer:checked").trigger('change');
            var is_err = 0;
            var examaction = $(this).attr("data-text");
            if(examaction == "Next"){
                questiontype = $(".checkanswer:checked").attr("question-type");
                if(questiontype == 2 || questiontype == 1){
                    var is_err = $(this).attr("iserr");
                }
                var currentid = $(this).attr("question-id-next");
                questionNo = questionNo + 1;
            }else{
                var currentid = $(this).attr("question-id-prev");
                questionNo = questionNo - 1;
            }
            var wrong_old_data_new = $("#wrong_ans_id").val();
            var examid = $("input[name=exam_id]").val();
            var language = $("input[name=language]").val();
            // set question duration in second start
            if(examaction == "Next"){
                var answer = $(".checkanswer:checked").val();
                var questionid = $(".checkanswer:checked").attr("question-id");
                var submitid = $("#submitquestion").attr("submit-id");
                var language = $("#attempt-exams input[name=language]").val();
                if(question_old_data_new.length != 0){
                    for(var ics = 0;ics < question_old_data_new.length;ics++){
                        if(question_old_data_new[ics]["question_id"] == questionid && question_old_data_new[ics]["duration_second"] == 0){
                            question_arr = {
                                question_id: questionid,
                                answer: answer,
                                language: language,
                                duration_second: $QuestionTimer,
                            };
                            question_old_data_new[ics] = question_arr;
                        }
                    }
                }
                QuestionSecond = 0;
            }
            // set question duration in second end
            // send ajax to get data
            $.ajax({
                url: BASE_URL + "/next-question",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    currentid: currentid,
                    examid: examid,
                    examaction: examaction,
                    questionNo: questionNo,
                    language: language,
                    wrong_ans: wrong_question_old_data_new,
                    attempt_ans: attempt_ans,
                    // Get Question Position
                    question_position: question_position,
                },
                success: function (response) {
                    $("#nextquestionarea").html(response);
                    if(question_old_data_new.length != 0){
                        for(var icn = 0; icn < question_old_data_new.length; icn++){
                            var question_id_check = $("#nextquestionarea .checkanswer").attr("question-id");
                            if(question_old_data_new[icn]["question_id"] == question_id_check){
                                var answer_ren = question_old_data_new[icn]["answer"];
                                $("#nextquestionarea .checkanswer[value=" + answer_ren + "]").prop("checked", true);
                                $("#nextquestion").prop("disabled", false);
                                if($("#nextquestion").length == 0){
                                    $("#submitquestion").prop("disabled",false);
                                    $("#submitquestion").show();
                                }
                            }
                        }
                    }
                    //updateMathHtml();
                    //updateMathHtmlById("nextquestionarea");
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    $("#cover-spin").hide();
                },
            });
            }
        );

        /** USE : After that student all attempt questions and click on submit button */
        $(document).on("click", "#submitquestion", function (e) {
            $("#cover-spin").show();
        });
    });

    // Store Student Exam History
function StoreStudentExamDetail(examid,currentid,answer,language){
    // Store StudentExam History Data
    $.ajax({
        url: BASE_URL + "/store-student-answer-history",
        type: "GET",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            questionid: currentid,
            examid: examid,
            language: language,
            answer:answer,                   
        },
        success: function (response) {
            $("#cover-spin").hide();
        },
    });
}

// Attempt Exams submit
function attempt_exams() {
    // If user click on the next button then system will trigger on click selected radio button
    $(".test-all .checkanswer:checked").trigger('change');
    var questionspos = "";
    setNoButtonText = "";
    $setYesButtonText = "";
    $setPoptitle = "";
    var language = $("input[name=language]").val();
    $("#cover-spin").show();
    if(questiontype == 2 || questiontype == 1){
        // Exam type => 2 = Excercise  & 1 = self learning
        if(attempt_ans == 0){
            if(isReAttempt === false){
                $("#questions_ans").val(JSON.stringify(question_old_data_new));
                $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                return true;
            }else{
                $("#attempt_first_trial_data_new").val(JSON.stringify(question_old_data_new));
                $.ajax({
                    url: BASE_URL + "/check-answer",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        questionid: question_old_data_new,
                    },
                    success: function (response) {                        
                        if(response.data.length != 0){
                            $.each(response.data, function (k, v) {
                                questionspos += "Q-" + v.questionNo;
                                if(response.data.length - 1 != k){
                                    questionspos += ", ";
                                }
                            });
                            if(language == "ch"){
                                questionspos += "<br />" + POPMESSAGE_CH1;
                                $setPoptitle = POPMESSAGETITLE_CH;
                                $setYesButtonText = BUTTONYESTEXTCH;
                                setNoButtonText = BUTTONNOTEXTCH;
                            }else{
                                questionspos += "<br />" + POPMESSAGE_EN1;
                                $setPoptitle = POPMESSAGETITLE_EN;
                                $setYesButtonText = BUTTONYESTEXTEN;
                                setNoButtonText = BUTTONNOTEXTEN;
                            }
                            $.confirm({
                                title: $setPoptitle,
                                content: questionspos,
                                //autoClose: 'Cancellation|8000',
                                buttons: {
                                    TryAgain: {
                                        text: $setYesButtonText,
                                        action: function () {
                                            var examid = $("input[name=exam_id]").val();
                                            var language = $("input[name=language]").val();
                                            wrong_question_old_data_new = response.data;
                                            var currentid = response.data[0]["question_id"];
                                            attempt_ans = 1;
                                            questionNo = 1;
                                            $("#cover-spin").hide();
                                            $.ajax({
                                                url:BASE_URL + "/next-question",
                                                type: "POST",
                                                data: {
                                                    _token: $('meta[name="csrf-token"]').attr("content"),
                                                    currentid: currentid,
                                                    examid: examid,
                                                    examaction: "Next",
                                                    questionNo: questionNo,
                                                    language: language,
                                                    wrong_ans:
                                                    wrong_question_old_data_new,
                                                    attempt_ans: attempt_ans,
                                                    // Get Question Position
                                                    question_position:
                                                    question_position,
                                                    examactionset: "current",
                                                },
                                                success: function (response) {
                                                    $("#nextquestionarea").html(response);
                                                    if (question_old_data_new.length != 0) {
                                                        for (var icn = 0;icn <question_old_data_new.length;icn++) {
                                                            var question_id_check =$("#nextquestionarea .checkanswer").attr("question-id");
                                                            if (question_old_data_new[icn]["question_id"] == question_id_check) {
                                                                var answer_ren = question_old_data_new[icn]["answer"];
                                                                $("#nextquestionarea .checkanswer[value=" +answer_ren +"]").prop("checked",true);
                                                                $("#nextquestion").prop("disabled",false);
                                                                if ($("#nextquestion").length ==0) {
                                                                    $("#submitquestion").prop("disabled",false);
                                                                    $("#submitquestion").show();
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //updateMathHtml();
                                                    // updateMathHtmlById(
                                                    //     "nextquestionarea"
                                                    // );
                                                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                                                    $("#cover-spin").hide();
                                                },
                                            });
                                        },
                                    },
                                    No: function () {
                                        isReAttempt = false;
                                        $("#questions_ans").val(JSON.stringify(question_old_data_new));
                                        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                                        $("#cover-spin").hide();
                                        $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
                                        $("#isAfterSubmit").val(1);
                                        if($("#isAfterSubmit").val() == 1){
                                            $("#AttemptQuestionFeedback").modal("show");
                                        }
                                        // $("#attempt-exams").submit();
                                        // return true;
                                    },
                                },
                            });
                        }else{
                            isReAttempt = false;
                            $("#questions_ans").val(JSON.stringify(question_old_data_new));
                            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                            $("#cover-spin").hide();
                            $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
                            $("#isAfterSubmit").val(1);
                            if ($("#isAfterSubmit").val() == 1) {
                                $("#AttemptQuestionFeedback").modal("show");
                            }
                             // for emoji cookie set blank after exam submit
                            $.cookie('SetfeedbackEmoji','');
                            // $("#attempt-exams").submit();
                            // return true;
                        }
                    },
                });
            }
        }else{
            $("#questions_ans").val(JSON.stringify(question_old_data_new));
            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
            $("#cover-spin").hide();
            $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
            $("#isAfterSubmit").val(1);
            if ($("#isAfterSubmit").val() == 1) {
                $("#AttemptQuestionFeedback").modal("show");
                //return true;
            }
        }
    }else{
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        $("#cover-spin").hide();
        $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
        $("#isAfterSubmit").val(1);
        if($("#isAfterSubmit").val() == 1){
            $("#AttemptQuestionFeedback").modal("show");
        }
        // return true;
    }
    return false;
}

// Attempt Personal Exams  submit
function attempt_personal_exams() {
    // If user click on the next button then system will trigger on click selected radio button
    $(".test-all .checkanswer:checked").trigger('change');
    var questionspos = "";
    setNoButtonText = "";
    $setYesButtonText = "";
    $setPoptitle = "";
    var language = $("input[name=language]").val();
    if(student_test_type == 1 && self_learning_test_type == 2){
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        return true;
    }

    $("#cover-spin").show();
    if(questiontype == 2 || questiontype == 1){
        // Exam type => 2 = Excercise  & 1 = self learning
        if(attempt_ans == 0){
            if(isReAttempt === false){
                $("#questions_ans").val(JSON.stringify(question_old_data_new));
                $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                return true;
            }else{
                $("#attempt_first_trial_data_new").val(JSON.stringify(question_old_data_new));
                $.ajax({
                    url: BASE_URL + "/check-answer",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        questionid: question_old_data_new,
                    },
                    success: function (response) {
                        if(response.data.length != 0){
                            $.each(response.data, function (k, v) {
                                questionspos += "Q-" + v.questionNo;
                                if(response.data.length - 1 != k){
                                    questionspos += ", ";
                                }
                            });
                            if(language == "ch"){
                                questionspos += "<br />" + POPMESSAGE_CH1;
                                $setPoptitle = POPMESSAGETITLE_CH;
                                $setYesButtonText = BUTTONYESTEXTCH;
                                setNoButtonText = BUTTONNOTEXTCH;
                            }else{
                                questionspos += "<br />" + POPMESSAGE_EN1;
                                $setPoptitle = POPMESSAGETITLE_EN;
                                $setYesButtonText = BUTTONYESTEXTEN;
                                setNoButtonText = BUTTONNOTEXTEN;
                            }
                            $.confirm({
                                title: $setPoptitle,
                                content: questionspos,
                                //autoClose: 'Cancellation|8000',
                                buttons: {
                                    TryAgain: {
                                        text: $setYesButtonText,
                                        action: function () {
                                            var examid = $("input[name=exam_id]").val();
                                            var language = $("input[name=language]").val();
                                            wrong_question_old_data_new = response.data;
                                            var currentid = response.data[0]["question_id"];
                                            attempt_ans = 1;
                                            questionNo = 1;
                                            $("#cover-spin").hide();
                                            $.ajax({
                                                url:BASE_URL + "/next-question",
                                                type: "POST",
                                                data: {
                                                    _token: $('meta[name="csrf-token"]').attr("content"),
                                                    currentid: currentid,
                                                    examid: examid,
                                                    examaction: "Next",
                                                    questionNo: questionNo,
                                                    language: language,
                                                    wrong_ans: wrong_question_old_data_new,
                                                    attempt_ans: attempt_ans,
                                                    // Get Question Position
                                                    question_position:
                                                    question_position,
                                                    examactionset: "current",
                                                },
                                                success: function (response) {
                                                    $("#nextquestionarea").html(response);
                                                    if(question_old_data_new.length != 0){
                                                        for(var icn = 0; icn < question_old_data_new.length;icn++){
                                                            var question_id_check = $("#nextquestionarea .checkanswer").attr("question-id");
                                                            if(question_old_data_new[icn]["question_id"] == question_id_check){
                                                                var answer_ren = question_old_data_new[icn]["answer"];
                                                                $("#nextquestionarea .checkanswer[value=" +answer_ren +"]").prop("checked",true);
                                                                $("#nextquestion").prop("disabled",false);
                                                                if($("#nextquestion").length ==0){
                                                                    $("#submitquestion").prop("disabled",false);
                                                                    $("#submitquestion").show();
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //updateMathHtml();
                                                    // updateMathHtmlById(
                                                    //     "nextquestionarea"
                                                    // );
                                                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                                                    $("#cover-spin").hide();
                                                },
                                            });
                                        },
                                    },
                                    No: function () {
                                        // for emoji cookie set blank after exam submit
                                        isReAttempt = false;
                                        $("#questions_ans").val(JSON.stringify(question_old_data_new));
                                        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                                        $("#cover-spin").hide();
                                        $("#attempt-exams").submit();
                                        $.cookie('SetfeedbackEmoji','');
                                        return true;
                                    },
                                },
                            });
                        }else{
                            isReAttempt = false;
                            $("#questions_ans").val(JSON.stringify(question_old_data_new));
                            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
                            $("#cover-spin").hide();
                            $("#attempt-exams").submit();
                            return true;
                        }
                    },
                });
            }
        }else{
            $("#questions_ans").val(JSON.stringify(question_old_data_new));
            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
            return true;
        }
    }else{
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        return true;
    }
    return false;
}
</script>
@endsection