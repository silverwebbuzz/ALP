
@if(!empty($Question))
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
        <div class="attmp-main-que">
            <h4>Q-{{$QuestionNumber}}</h4>
            <div class="attmp-que">
                @php echo $Question->{'question_'.$examLanguage}; @endphp
            </div>
            {{--@if($examLanguage == 'en')
                <span style="color:red">@php if($attempAnswer) echo 'Incorrect Answer'; @endphp</span>
            @else
                <span style="color:red">@php if($attempAnswer) echo '不正確的答案'; @endphp</span>
            @endif--}}
        </div>

        <div class="attmp-main-answer">
            @if(isset($question_position) && !empty($question_position) && isset($question_position[$Question->id]))
                @php
                if($question_position[$Question->id]!=""){
                    $random_number_array = json_decode($question_position[$Question->id]);
                }
                @endphp
            @endif
            @if(isset($Question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}))
                <div class="attmp-ans pl-2 pb-2">
                    <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[0]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[0])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
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
                    <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[1]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[1])) checked @endif question-id="{{$Question->id}}"  @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
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
                    <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[2]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[2])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
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
                    <input type="radio" name="ans_que_{{$Question->id}}" value="{{$random_number_array[3]}}" class="radio mr-2 checkanswer" @if(isset($HistoryStudentQuestionAnswer) && $HistoryStudentQuestionAnswer->selected_answer_id == $random_number_array[3])) checked @endif question-id="{{$Question->id}}" @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
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
        @if(isset($examDetail->exam_type) && $examDetail->exam_type == 2)
            <div class="attmp-main-explain">
                <div class="attmp-expln-inner questionhint" style="display: none">
                    <h5>{{__('languages.my_studies.explain')}}</h5>
                    <p id="questionhint"></p>
                </div>
            </div>
        @endif
        @if(isset($examDetail->exam_type) && $examDetail->exam_type == 2)
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
        @if($Questionsfirstid != $Question->id)     
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
        @if($Questionslastid != $Question->id)
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
        @if($Questionslastid == $Question->id)
        <div class="attmp-submit-btn attmp-butns">
            <button type="button" class="btn btn-success mr-2" id="submitquestion" @if($Questionslastid == $Question->id) submit-id="1" @endif>
            @if($examLanguage == 'en')
            {{__('languages.submit')}}
            @else
                {{__('提交')}}
            @endif
            </button>
        </div>
        @endif
        
        @if (isset($examDetail->exam_type) && $examDetail->exam_type == 2)
            <div class="">
                @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                    <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal">
                    @if($examLanguage == 'en')
                    {{__('languages.my_studies.want_a_hint')}}
                    @else
                    {{__('想要提示？')}}
                    @endif
                    </button>
                @else
                    <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal">
                    @if($examLanguage == 'en')
                        {{__('languages.my_studies.want_a_hint')}}
                    @else
                        {{__('想要提示？')}}
                    @endif
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
@endif

@if(isset($examDetail->exam_type) && $examDetail->exam_type == 2)
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

@if(isset($examDetail[0]->exam_type) && ($examDetail[0]->exam_type == 2 || ($examDetail[0]->exam_type == 1 && $examDetail[0]->self_learning_test_type == 1)))
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