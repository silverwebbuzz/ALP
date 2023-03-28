<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec w-100">
    <div id="content" > {{-- class="pl-2 pb-5"--}}
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="sm-right-detail-sec">
        <div class="remove-teacher-question-list-padding">
            <!-- Start Student List -->

            <div class="sm-add-user-sec card">
                <div class="select-option-sec pb-2 card-body">
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        @if(!empty($difficultyLevels))
                            @php $i=1; $difficultyColor= []; @endphp
                            @foreach($difficultyLevels as $difficultyLevel)
                                @php $difficultyColor['Level'.$i] = $difficultyLevel->difficulty_level_color;  $i+=1;@endphp
                            @endforeach
                        @endif
                    </div>
                    
                    @if(!empty($Questions))
                    @php
                        $UserSelectedAnswers = [];
                        if(isset($AttemptExamData->question_answers) && !empty($AttemptExamData->question_answers)){
                            $UserSelectedAnswers = json_decode($AttemptExamData->question_answers);
                        }
                        $WeaknessList = array();
                        $WeaknessListWithId = array();
                    @endphp
                    @php
                        $bg_correct_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
                        $bg_incorrect_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
                    @endphp
                        @foreach($Questions as $key => $question)
                        <div class="row">
                            <input type="hidden" name="qIndex[]" value="{{ $question->id }}" />
                            @php
                                $QuestionIDs = explode(',',$ExamData['question_ids']);
                                $AnswerNumber = array_filter($UserSelectedAnswers, function ($var) use($question){
                                    if($var->question_id == $question['id']){
                                        return $var;
                                    }
                                });
                            @endphp
                            <div class="sm-que-option pl-3">
                                <p class="sm-title bold">{{__('languages.result.q_no')}}: {{ (array_search($question->id,$QuestionIDs) + 1) }} @if(Auth::user()->role_id == 1) {{__('languages.question_code')}} : {{ $question->naming_structure_code }} @endif
                                    {{-- Display Question types and with color code --}}
                                    <?php 
                                        $LevelName = \App\Helpers\Helper::getLevelNameBasedOnLanguage($question->dificulaty_level);
                                    ?>
                                    @if($question->dificulaty_level == 1)
                                    @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level1']))
                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level1']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                    @endif
                                    @elseif($question->dificulaty_level == 2)
                                        @if(isset($difficultyColor['Level2']) && !empty($difficultyColor['Level2']))
                                            <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                        @endif
                                    @elseif($question->dificulaty_level == 3)
                                        @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                                            <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                        @endif
                                    @elseif($question->dificulaty_level == 4)
                                        @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                                            <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                        @endif
                                    @elseif($question->dificulaty_level == 5)
                                        @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                                            <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                        @endif
                                    @endif
                                    @php
                                        $normalized_difficulty=\App\Helpers\Helper::getNormalizedAbility($question->PreConfigurationDifficultyLevel->title);
                                    @endphp
                                    <span class="ml-5">{{__('languages.difficulty')}}:
                                        @if(isset($question->PreConfigurationDifficultyLevel->title) && $question->PreConfigurationDifficultyLevel->title!="")
                                        {{App\Helpers\Helper::GetShortPercentage($normalized_difficulty)}}
                                        @endif
                                    </span>
                                </p>
                                <div class="sm-que pl-2">
                                    <p><?php echo $question->{'question_'.app()->getLocale()}; ?></p>
                                </div>
                                @php
                                    if(!empty($question->answers)){
                                        $answer = [
                                                    $question->answers->{'answer1_'.app()->getLocale()},
                                                    $question->answers->{'answer2_'.app()->getLocale()},
                                                    $question->answers->{'answer3_'.app()->getLocale()},
                                                    $question->answers->{'answer4_'.app()->getLocale()},
                                                ];
                                        //shuffle($answer); 
                                    }
                                @endphp
                                @if(isset($answer[0]))
                                <div class="sm-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                    <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >1</div>
                                        <div class="progress">
                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][1]}}%">
                                                <div class="anser-detail pl-2">
                                                    <?php
                                                    echo $question->answers->{'answer1_'.app()->getLocale()};
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($answer[1]))
                                <div class="sm-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_{{$question->id}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">2</div>
                                    <div class="progress">
                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][2]}}%">
                                            <div class="anser-detail pl-2">
                                                <?php
                                                echo $question->answers->{'answer2_'.app()->getLocale()};
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(isset($answer[2]))
                                <div class="sm-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_{{$question->id}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">3</div>
                                    <div class="progress">
                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][3]}}%">
                                            <div class="anser-detail pl-2">
                                                <?php
                                                echo $question->answers->{'answer3_'.app()->getLocale()};
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(isset($answer[3]))
                                <div class="sm-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_{{$question->id}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">4</div>
                                    <div class="progress">
                                        {{-- <div >
                                            <div class="anser-detail pl-2"><?php echo $answer[3];?></div>
                                        </div> --}}
                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][4]}}%">
                                            <div class="anser-detail pl-2">
                                                <?php
                                                //echo $question->answers->{'answer4_'.$AttemptExamData->language};
                                                echo $question->answers->{'answer4_'.app()->getLocale()};
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div> 
            </div>
        </div>
    </div>
</div>
</div> 

