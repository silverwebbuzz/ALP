    
@if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="sm-right-detail-sec">
    <div class="container-fluid">
    <div class="sm-add-user-sec statistics-result-main card">
    <div class="select-option-sec pb-2 card-body">
        <div class="row result-statistics">
            <div class="sm-que-option pl-3">
                @php
                    $bg_correct_color='background-color:'.\App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
                    $bg_incorrect_color='background-color:'.\App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
                @endphp
                @if(!empty($difficultyLevels))
                    @php $i=1; $difficultyColor= []; @endphp
                    @foreach($difficultyLevels as $difficultyLevel)
                        @if(!empty($questionDifficultyGraph['Level'.$i]))
                            <h6>{{__($difficultyLevel->{'difficulty_level_name_'.app()->getLocale()})}}</h6>
                            <div class="progress question-difficulty-progressbar">
                                @if(!empty($questionDifficultyGraph['Level'.$i.'_correct_percentage']))
                                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['Level'.$i.'_correct_percentage']}}%;{{$bg_correct_color}};">
                                        {{$questionDifficultyGraph['correct_Level'.$i]}}
                                    </div>
                                @endif
                                @if(!empty($questionDifficultyGraph['Level'.$i.'_wrong_percentage']))
                                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['Level'.$i.'_wrong_percentage']}}%;{{$bg_incorrect_color}};">
                                        {{ $questionDifficultyGraph['wrong_Level'.$i] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                        @php $difficultyColor['Level'.$i] = $difficultyLevel->difficulty_level_color;  $i+=1;@endphp
                    @endforeach
                @endif
                <p class="sm-title bold">
                    {{__('languages.result.q_no')}} 1
                    @if(auth()->user()->role_id == 1):{{__('languages.question_code')}} : {{ $QuestionData->naming_structure_code }}@endif
                    <?php $LevelName = \App\Helpers\Helper::getLevelNameBasedOnLanguage($QuestionData->dificulaty_level); ?>
                    {{-- Display Question types and with color code --}}
                    @if($QuestionData->dificulaty_level == 1)
                        @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level1']))
                            <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level1']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                        @endif
                    @elseif($QuestionData->dificulaty_level == 2)
                        @if(isset($difficultyColor['Level2']) && !empty($difficultyColor['Level2']))
                            <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                        @endif
                    @elseif($QuestionData->dificulaty_level == 3)
                        @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                            <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                        @endif
                    @elseif($QuestionData->dificulaty_level == 4)
                        @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                            <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                        @endif
                    @elseif($QuestionData->dificulaty_level == 5)
                        @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                            <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                        @endif
                    @endif
                    
                </p>
                <div class="sm-que pl-2">
                    <p><?php echo $QuestionData->{'question_'.app()->getLocale()}; ?></p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{-- <h6>{{__('languages.all')}}</h6> --}}
                        <div class="sm-ans pl-2 pb-2">
                            <div class="answer-title mr-2 <?php if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">1</div>
                            <div class="progress">
                                <div class="progress-bar @if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{(!empty($percentage)) ? $percentage[$QuestionData->id][1] : 0}}" aria-valuemin="0" aria-valuemax="100" style=";width:{{!empty($percentage) ? $percentage[$QuestionData->id][1] : 0}}%">
                                    <div class="anser-detail pl-2">
                                        <?php echo $QuestionData->answers->{'answer1_'.app()->getLocale()}; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="answer-progress">
                                <p class="progress-percentage">{{(!empty($percentage)) ? $percentage[$QuestionData->id][1] : 0}}%</p>
                            </div>
                        </div>
                        
                        <div class="sm-ans pl-2 pb-2">
                            <div class="answer-title mr-2 <?php if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 2){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">2</div>
                            <div class="progress">
                                <div class="progress-bar @if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{(!empty($percentage)) ? $percentage[$QuestionData->id][2] : 0}}" aria-valuemin="0" aria-valuemax="100" style=";width:{{!empty($percentage) ? $percentage[$QuestionData->id][2] : 0}}%">
                                    <div class="anser-detail pl-2">
                                        <?php echo $QuestionData->answers->{'answer2_'.app()->getLocale()}; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="answer-progress">
                                <p class="progress-percentage">{{(!empty($percentage)) ? $percentage[$QuestionData->id][2] : 0}}%</p>
                            </div>
                        </div>
                        
                        <div class="sm-ans pl-2 pb-2">
                            <div class="answer-title mr-2 <?php if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 3){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">3</div>
                            <div class="progress">
                                <div class="progress-bar @if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{(!empty($percentage)) ? $percentage[$QuestionData->id][3] : 0}}" aria-valuemin="0" aria-valuemax="100" style=";width:{{!empty($percentage) ? $percentage[$QuestionData->id][3] : 0}}%">
                                    <div class="anser-detail pl-2">
                                        <?php echo $QuestionData->answers->{'answer3_'.app()->getLocale()}; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="answer-progress">
                                <p class="progress-percentage">{{(!empty($percentage)) ? $percentage[$QuestionData->id][3] : 0}}%</p>
                            </div>
                        </div>

                        <div class="sm-ans pl-2 pb-2">
                            <div class="answer-title mr-2 <?php if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 4){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">4</div>
                            <div class="progress">
                                <div class="progress-bar @if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{(!empty($percentage)) ? $percentage[$QuestionData->id][4] : 0}}" aria-valuemin="0" aria-valuemax="100" style=";width:{{!empty($percentage) ? $percentage[$QuestionData->id][4] : 0}}%">
                                    <div class="anser-detail pl-2">
                                        <?php echo $QuestionData->answers->{'answer4_'.app()->getLocale()}; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="answer-progress">
                                <p class="progress-percentage">{{(!empty($percentage)) ? $percentage[$QuestionData->id][4] : 0}}%</p>
                            </div>
                        </div>

                        <div class="sm-ans pl-2 pb-2">
                            <div class="answer-title mr-2 <?php if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 5){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($QuestionData->answers->{'correct_answer_'.app()->getLocale()} == 5){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">N</div>
                            <div class="progress">
                                <div class="progress-bar  @if($QuestionData->answer == 5) ans-incorrect @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width:{{!empty($percentage) ? $percentage[$QuestionData->id][5] : 0}}%">
                                    <div class="anser-detail no-answer-fontsize pl-2">
                                        {{__('languages.no_answer')}}
                                    </div>
                                </div>
                            </div>
                            <div class="answer-progress">
                                <p class="progress-percentage">{{!empty($percentage) ? $percentage[$QuestionData->id][5] : 0}}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    </div>
    </div>
</div>
