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
                <div class="test-navigation-main">
                    <div id="test-navigation" style="display: block;">
                        <div id="test_question_review">
                            <ol>
                                @if($question_ids)
                                    @foreach($question_ids as $QuestionIndex => $QuestionId)
                                    <!-- <li class="test-navigation-item selected_question_item" data-index="1">1</li> -->
                                    <li class="test-navigation-item test-navigation-item-{{$QuestionId}} 
                                    @if(in_array($QuestionId,$answered_flag_question_ids)) answered-item @endif
                                    @if(in_array($QuestionId,$not_attempted_flag_question_ids)) flagged-item @endif 
                                    @if($Question->id == $QuestionId) selected_question_item @endif"
                                    data-index="{{$QuestionId}}" 
                                    question-id-next="{{$QuestionId}}" 
                                    data-text="QuestionNavigation">{{($QuestionIndex+1)}}</li>
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                        <div id="test-navigation-legend">
                            <ol>
                                <li>
                                    <span class="test-navigation-color" style="background-color: #6CA54C;"></span>
                                    <span class="test-navigation-text">{{__('languages.Answered')}}</span>
                                </li>
                                <li>
                                    <span class="test-navigation-color" style="background-color: #E30B5C;"></span>
                                    <span class="test-navigation-text">{{__('languages.Flagged')}}</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="row" style="float:right;">
                    <div class="attmp-submit-btn attmp-butns">
                        <button type="submit" class="btn btn-success mr-2" id="submitquestion" submit-id="1">
                        @if($examLanguage == 'en')
                        {{__('languages.submit')}}
                        @else
                            {{__('提交')}}
                        @endif
                        </button>
                    </div>
                </div>
                    <form class="changeLanguageExamForm" id="changeLanguageExamForm" method="post">
                        @csrf()
                        <input type="hidden" name="SelectedAnswersArray" value="" id="SelectedAnswersArray">
                        <input type="hidden" name="exam_taking_timing" value="" class="taking_exam_timing">
                        <div class="row w-100">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-main-timer">
                                <div class="select-lng w-25">
                                    <select name="language" class="form-control select-option" id="student-select-attempt-exam-language" data-examid="{{$examDetail->id}}" data-questionId="{{$Question->id}}" @if(request()->get("language") === "ch") disabled @endif>
                                        <option value="">{{ __('languages.my_studies.select_exam_language') }}</option>
                                        <option value="en" @if(request()->get("language") === "en" || (isset($examLanguage) && $examLanguage == 'en')) selected @endif @if(!isset($_GET['language'])) selected @endif>{{ __('languages.english') }}</option>
                                        <option value="ch" @if(request()->get("language") === "ch" || (isset($examLanguage) && $examLanguage == 'ch')) selected @endif>{{ __('languages.chinese') }}</option>
                                    </select>
                                    @if($errors->has('language'))
                                        <span class="validation_error">{{ $errors->first('language') }}</span>
                                    @endif
                                </div>
                                <div class="exam_timer_section">
                                    <div class="attmp-timer-out-inr">
                                        @if($ExamMaximumSeconds != 'unlimited_time')
                                            <h5 class="exam_time_limit_label">{{__('languages.time_limit')}} : </h5>
                                            <span id="ExamTimerOut">@if(isset($ExamMaximumSeconds)) {{sprintf('%02d:%02d:%02d', ($ExamMaximumSeconds/ 3600),($ExamMaximumSeconds/ 60 % 60), $ExamMaximumSeconds% 60)}} @else 00:00:00 @endif</span>
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

                    <form class="attempt-exams" method="post" id="attempt-exams" action="{{route('student.submit.test-exercise')}}">
                        <input type="hidden" name="no_of_trial_exam" value="{{$IsAttemptTrialNo}}" class="no_of_trial_exam" id="no_of_trial_exam">
                        <input type="hidden" name="exam_taking_timing" value="" class="taking_exam_timing">
                        <input type="hidden" name="exam_id" value= "{{request()->route('exam_id')}}"/>
                        @csrf()
                        <div id="nextquestionarea" class="test-all">
                        @if(!empty($Question))                        
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
                                <div class="attmp-main-que">
                                    <h4>Q-{{$QuestionNumber}}</h4>
                                    <div class="attmp-que">
                                    @php echo $Question->{'question_'.$examLanguage}; @endphp
                                    </div>
                                </div>
                                <div class="attmp-main-answer">
                                    @php
                                        $random_number_array = range(1,4);
                                        shuffle($random_number_array );
                                        $random_number_array = array_slice($random_number_array ,0,4);
                                    @endphp
                                    @if(isset($Question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[0]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[0])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="1" >
                                            <div class="answer-title mr-2">A</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $Question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($Question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[1]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[1])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="2" >
                                            <div class="answer-title mr-2">B</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $Question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($Question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[2]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[2])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="3" >
                                            <div class="answer-title mr-2">C</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $Question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($Question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[3]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[3])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="4" >
                                            <div class="answer-title mr-2">D</div>
                                            <div class="progress">
                                                <div  role="progressbar">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $Question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}; @endphp</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="attmp-ans pl-2 pb-2 not-answer-row">
                                        <input type="radio" name="ans_que_{{$Question->id}}" value="5" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == 5)) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif  data-q-index="5" >
                                        <div class="answer-title mr-2">E</div>
                                        <div class="progress">
                                            <div  role="progressbar">
                                                <div class="anser-detail pl-2">
                                                    <p>
                                                        @if($examLanguage == 'en') 
                                                        {{__('languages.no_answer_en')}}
                                                        @else
                                                        {{__('languages.no_answer_ch')}}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-all-button">
                                <div class="flag-btn-main">
                                    <button type="button" class="btn btn-danger mr-2" id="attempt-test-flag-button" flag-question-id="{{$Question->id}}">
                                    @if($examLanguage == 'en') 
                                    {{__('languages.flag_en')}}
                                    @else
                                    {{__('languages.flag_ch')}}
                                    @endif
                                    </button>
                                </div>
                                @if($QuestionsFirstId != $Question->id)     
                                <div class="attmp-prev-btn attmp-butns">
                                    <button type="button" class="btn btn-info mr-2" id="prevquestion" question-id-prev="{{$Question->id}}" data-text="Previous">
                                    @if($examLanguage == 'en')
                                    {{__('languages.my_studies.previous')}}
                                    @else
                                    {{__('以前的')}}
                                    @endif
                                    </button>
                                </div>
                                @endif

                                @if($QuestionsLastId != $Question->id)
                                    <div class="attmp-next-btn attmp-butns">
                                        <button type="button" class="btn btn-warning mr-2" id="nextquestion" question-id-next="{{$Question->id}}" data-text="Next">
                                        @if($examLanguage == 'en')
                                        {{__('languages.my_studies.next')}}
                                        @else
                                        {{__('下一個')}}
                                        @endif
                                        </button>
                                    </div>
                                @endif
                                
                                @if($QuestionsLastId == $Question->id)
                                <div class="attmp-submit-btn attmp-butns">
                                    <button type="button" class="btn btn-success mr-2"  id="submitquestion" @if($QuestionsLastId == $Question->id) submit-id="1" @endif>
                                    @if($examLanguage == 'en')
                                    {{__('languages.submit')}}
                                    @else
                                    {{__('提交')}}
                                    @endif
                                    </button>
                                </div>
                                @endif
                                
                                @if(isset($examDetail->exam_type) && ($examDetail->exam_type == 2))
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
                                                    
                        @if(isset($examDetail->exam_type) && ($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)))
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
                                                @if(trim($Question->general_hints_ch)!="")
                                                {!! $Question->general_hints_ch !!}
                                                @else
                                                {{__('languages.hint_not_available')}}
                                                @endif
                                            @else
                                                @if(trim($Question->general_hints_en)!="")
                                                {!! $Question->general_hints_en !!}
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
                </div>
            </div>
        </div>
    </div>
</div>

@include('backend.layouts.footer')

{{-- Modal --}}
<div class="modal fade" id="AttemptQuestionFeedback" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-xl" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{__('Exam Survey')}}</h5>
            </div>
            <div class="modal-body " id="AttemptQuestionFeedbackBody">
                <form class="smileys">
                    <input type="hidden" name="feedbackType" id="feedbackType" value=""/>
                    <input type="radio" name="smiley" value="1" class="sad emojisButton">
                    <input type="radio" name="smiley" value="2" class="happy emojisButton">
                </form>
            </div>
        </div>
    </div>
</div>
{{-- End Modal --}}

<script>
    var questionNo = 1;
    var attempt_ans = 0;
    var question_position = new Array();
    var FlaggedQuestionIds = new Array();
    var answered_flag_question_ids = new Array();
    var second = 0;
    var QuestionSecond = 0;
    var QuestionTimer = 0;
    //var totalSeconds = '@php echo $ExamMaximumSeconds; @endphp';
    var totalSeconds = '@php echo $RemainingSeconds; @endphp';
</script>

<script>
<?php if($IsAttemptTrialNo == 1){?>
    var isReAttempt = true;
    <?php }else{ ?>
        var isReAttempt = false;
<?php } ?>

// Set status value for Before_test_feedback & after test feedback
<?php if(empty($HistoryStudentExamsData) || empty($HistoryStudentExamsData->before_emoji_id)){?>
    var BeforeTestFeedbackPopUp = true;
<?php }else{ ?>
    var BeforeTestFeedbackPopUp = false;
<?php } ?>
</script>

@php if(isset($taking_exam_timing)){ @endphp
<script>
    // var hms = '@php echo $taking_exam_timing; @endphp';   // your input string
    // var a = hms.split(':'); // split it at the colons
    // // minutes are worth 60 seconds. Hours are worth 60 minutes.
    // var second = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
    var second = '@php echo $second; @endphp';
</script>
@php } @endphp

<script>
    $(document).ready(function() {
        // Start exam timer
        examTimer();
        examTimerReverse();

        // Open popup for before starting the exams
        if(BeforeTestFeedbackPopUp){
            $('#feedbackType').val('before_test_exercise');
            $('#AttemptQuestionFeedback').modal('show');
        }

        // Trigger on click event after selecting emoji
        $(document).on("click",".emojisButton",function () {
            var FeedbackEmojiId = $('input[name="smiley"]:checked').val();
            if(FeedbackEmojiId!=""){
                //$("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/update/test-exercise/survey-feedback",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        exam_id: $("input[name=exam_id]").val(),
                        FeedbackEmojiId:FeedbackEmojiId,
                        FeedbackType : $('#feedbackType').val(),
                    },
                    success: function (response) {
                        var Response = JSON.parse(JSON.stringify(response));
                        if(Response.data.status){
                            $("#AttemptQuestionFeedback").modal("hide");
                            closePopupModal("AttemptQuestionFeedback");
                            $("#cover-spin").hide();
                        }
                        if(Response.data.isFormSubmit){
                            $("#cover-spin").show();
                            $("#attempt-exams").submit();
                        }else{
                            $("#cover-spin").hide();
                        }
                    },
                });
            }
        });

        // Set on click trigger flagged questions
        $(document).on("click","#attempt-test-flag-button",function () {
            var FlagQuestionId = $(this).attr('flag-question-id');
            if(FlaggedQuestionIds.indexOf(FlagQuestionId) !== -1)  {
                // We will add flagged question id into array
                removeFlaggedQuestionIds(FlaggedQuestionIds, FlagQuestionId);
                $('.test-navigation-item-'+FlagQuestionId).removeClass('flagged-item');
                $("#nextquestionarea .checkanswer[value=5]" ).prop("checked", false);
            }else{
                // If value is already exists the we remove flag
                FlaggedQuestionIds.push(FlagQuestionId);
                $('.test-navigation-item-'+FlagQuestionId).removeClass('selected_question_item');
                $('.test-navigation-item-'+FlagQuestionId).addClass('flagged-item');
                $("#nextquestionarea .checkanswer[value=5]" ).prop("checked", true);
                $(".test-all .checkanswer:checked").trigger('change');
            }
        });
    });

    /**
     * USE : Student can select exam language based on changes list of question answer language
     * **/
    $(document).on("change","#student-select-attempt-exam-language",function () {
        if (this.value == "") {
            toastr.error(PLEASE_SELECT_LANGUAGE);
            return false;
        }
        var examid = $(this).attr("data-examid");        
        var CurrentQuestionId = $(this).attr("data-questionId");
        var language = $(this).val();
        $("#cover-spin").show();
        // Get new question
        $.ajax({
            url: BASE_URL + "/test-exercise/next-question",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                CurrentQuestionId: CurrentQuestionId,
                examid: examid,
                examaction: 'current',
                language: language,
                no_of_trial_exam : $('#no_of_trial_exam').val(),
                second:second
            },
            success: function (response) {
                var Response = JSON.parse(JSON.stringify(response));
                if(Response.data.html){
                    $("#nextquestionarea").html(Response.data.html);
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }
                $("#cover-spin").hide();
            },
        });
    });

    /**
     * USE : Use To Check Answer Is Right Or Not
     */
    var wrong = 0;
    var questionIdArr = [];
    $(document).on("change", ".checkanswer", function () {
        $(".questionhint").hide();
        $("#nextquestion").prop("disabled", false);
        var exam_id = $("input[name=exam_id]").val();
        var no_of_trial_exam = $("input[name=no_of_trial_exam]").val();
        var current_question_id = $(this).attr("question-id");
        var language = $("#student-select-attempt-exam-language").val();
        var no_of_second = 10;
        var selected_answer_id = $(this).val();
        // Set Question navigation color
        if(selected_answer_id=='5'){
            $('.test-navigation-item-'+current_question_id).removeClass('answered-item');
            $('.test-navigation-item-'+current_question_id).addClass('flagged-item');
            var is_answered_flag = false;
        }else{
            $('.test-navigation-item-'+current_question_id).removeClass('flagged-item').removeClass('selected_question_item');
            $('.test-navigation-item-'+current_question_id).addClass('answered-item');
            var is_answered_flag = true;
        }
        $("#cover-spin").show();
        // Update in the database selected question and answers
        $.ajax({
            url: BASE_URL + "/test-exercise/update-question-answer",
            type: "POST",
            data:{
                _token: $('meta[name="csrf-token"]').attr("content"),
                exam_id : exam_id,
                no_of_trial_exam : no_of_trial_exam,
                current_question_id : current_question_id,
                selected_answer_id : selected_answer_id,
                is_answered_flag : is_answered_flag,
                no_of_second : no_of_second,
                language : language,
                second:second
            },
            success: function (response) {
                // No any success event in this ajax call
                var Response = JSON.parse(JSON.stringify(response));
                $("#cover-spin").hide();
            }
        });
        $("#cover-spin").hide();
    });

    /**
     * USE : Get the next question, previous question, or selected index questions
     */
    $(document).on("click","#nextquestion,#prevquestion,.test-navigation-item",function (e) {
        $(".test-all .checkanswer:checked").trigger('change');
        $('.test-navigation-item').removeClass('selected_question_item');
        var examaction = $(this).attr("data-text");
        var language = $("#student-select-attempt-exam-language").val();
        var examid = $("input[name=exam_id]").val();
        if (examaction == "Next" || examaction == 'QuestionNavigation'){
            var CurrentQuestionId = $(this).attr("question-id-next");
            var submitid = $("#submitquestion").attr("submit-id");
            QuestionSecond = 0;
        }
        if(examaction == "Previous"){
            var CurrentQuestionId = $(this).attr("question-id-prev");
        }
        $('#student-select-attempt-exam-language').attr('data-questionId',CurrentQuestionId);
        $("#cover-spin").show();
        // Get new question
        $.ajax({
            url: BASE_URL + "/test-exercise/next-question",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                CurrentQuestionId: CurrentQuestionId,
                examid: examid,
                examaction: examaction,
                questionNo: questionNo,
                language: language,
                no_of_trial_exam : $('#no_of_trial_exam').val(),
                second:second
            },
            success: function (response) {
                var Response = JSON.parse(JSON.stringify(response));
                if(Response.data.html){
                    $("#nextquestionarea").html(Response.data.html);
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }
                $('.test-navigation-item-'+Response.data.question_id).addClass('selected_question_item');
                $("#cover-spin").hide();
            },
        });
    });

    function removeFlaggedQuestionIds(arr) {
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax= arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    // Create function Exam Timer
    var examTimerInterval = '';
    function examTimer() {
        //var myTimer = setInterval(myClock, 1000);
        examTimerInterval = setInterval(myClock, 1000);
        function myClock(){
            var timer = secondsTimeSpanToHMS(++second);
            $('.taking_exam_timing').val(timer);
            $('#ExamTimer').text(timer);
        }
    }

    function secondsTimeSpanToHMS(second) {
        var h = Math.floor(second / 3600); //Get whole hoursecond
        second -= h * 3600;
        var m = Math.floor(second / 60); //Get remaining minutesecond
        second -= m * 60;
        return h + ":" + (m < 10 ? '0' + m : m) + ":" + (second < 10 ? '0' + second : second); //zero padding on minutes and seconds
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
                                AfterCompleteTestFeedback();
                                //$("#attempt-exams").submit();
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

    // Trigger on click form submit event
    $(document).on("click","#submitquestion",function (e) {
        $(".test-all .checkanswer:checked").trigger('change');
        if(APP_LANGUAGE == 'en'){
            var confirm_button_yes_text = BUTTONYESTEXTEN;
        }else{
            var confirm_button_yes_text = BUTTONYESTEXTCH;
        }
        $.confirm({
            title: CONFIRMATION_BUTTON_TEXT,
            content: SUBMIT_TEST_EXERCISE_CONFIRMATION_MESSAGE,
            autoClose: 'Cancellation|5000',
            buttons: {
                TryAgain: {
                    text: confirm_button_yes_text,
                    action: function () {
                        var questionspos = "";
                        var ExamType = $("#nextquestionarea .checkanswer").attr("question-type");
                        var language = $("#student-select-attempt-exam-language").val();
                        var no_of_trial_exam = $("input[name=no_of_trial_exam]").val();
                        var exam_id = $("input[name=exam_id]").val();
                        if(ExamType == 2){
                            // Exam type => 2 = Excercise  & 1 = self learning
                            if(isReAttempt === false){
                                AfterCompleteTestFeedback();
                                //$("#cover-spin").show();
                                //$("#attempt-exams").submit();
                                //return true;
                            }else{
                                $("#cover-spin").show();
                                // The logic is second trial attempt test
                                $.ajax({
                                    url: BASE_URL + "/verify/question-answer/test-exercise",
                                    type: "POST",
                                    data:{
                                        _token: $('meta[name="csrf-token"]').attr("content"),
                                        exam_id: exam_id,
                                        no_of_trial_exam: no_of_trial_exam,
                                    },
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var Response = JSON.parse(JSON.stringify(response));
                                        if(Response.data.questionNo !=""){
                                            $.each(Response.data.questionNo, function (key, value) {
                                                questionspos += "Q-" + value;
                                                if(Response.data.questionNo - 1 != key){
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
                                                            isReAttempt = false;
                                                            var exam_id = $("input[name=exam_id]").val();
                                                            $("input[name=no_of_trial_exam]").val(2);
                                                            var no_of_trial_exam = $("input[name=no_of_trial_exam]").val();
                                                            var WrongQuestionIds = Response.data.WrongQuestionIds;
                                                            $("#cover-spin").show();
                                                            $.ajax({
                                                                url:BASE_URL + "/student/attempt/exercise/second-trial",
                                                                type: "POST",
                                                                data: {
                                                                    _token: $('meta[name="csrf-token"]').attr("content"),
                                                                    exam_id: exam_id,
                                                                    WrongQuestionIds:WrongQuestionIds,
                                                                    examaction: "Next",
                                                                    language: language,
                                                                    no_of_trial_exam:no_of_trial_exam,
                                                                    second:second
                                                                },
                                                                success: function (Second_Trial_Response) {
                                                                    var SecondTrialResponse = JSON.parse(JSON.stringify(Second_Trial_Response));
                                                                    $('#test_question_review').html(SecondTrialResponse.data.IndexingHtml);
                                                                    $("#nextquestionarea").html(SecondTrialResponse.data.html);
                                                                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                                                                    var FlaggedQuestionIds = new Array();
                                                                    var answered_flag_question_ids = new Array();
                                                                    clearInterval(examTimerInterval);
                                                                    second = SecondTrialResponse.data.second;
                                                                    examTimer();
                                                                }
                                                            });
                                                            $("#cover-spin").hide();
                                                        },
                                                    },
                                                    No: function () {
                                                        
                                                        $("#cover-spin").hide();
                                                        isReAttempt = false;
                                                        AfterCompleteTestFeedback();
                                                        // $("#attempt-exams").submit();
                                                        // return true;
                                                    }
                                                },
                                            });
                                        }
                                    }
                                });
                            }
                        }else{
                            $("#cover-spin").hide();
                            AfterCompleteTestFeedback();
                        }
                    },
                },
                No: function () {
                    $("#cover-spin").hide();
                }
            },
        });
        //return false;
    });

    function AfterCompleteTestFeedback(){
        $('#feedbackType').val('after_test_exercise');
        $('#AttemptQuestionFeedback').modal('show');
    }
</script>
@endsection