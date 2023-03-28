<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Models\Exam;
use App\Models\Question;
use App\Models\AttemptExams;
use App\Models\Answer;
use App\Models\HistoryStudentExams;
use App\Models\HistoryStudentQuestionAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Http\Services\AIApiService;
use App\Http\Controllers\CronJobController;

class AttemptExamTestExerciseController extends Controller
{
    use Common, ResponseFormat;
    protected $AIApiService,$CronJobController;

    public function __construct(){
        ini_set('max_execution_time', -1);
        $this->AIApiService = new AIApiService();
        $this->CronJobController = new CronJobController;
    }

    /**
     * USE : Get Random array position for question answer
     */
    public function GetRandomAnswerOrdering(){
        $random_number_array = range(1,4);
        shuffle($random_number_array );
        $random_number_array = array_slice($random_number_array ,0,4);
        return $random_number_array;
    }

    /** USE : Student will attempt test and exercise */
    public function StudentAttemptTestExercise($exam_id, Request $request){
        if(isset($exam_id) && !empty($exam_id)){
            $HistoryStudentExamsData = array();
            $IsAttemptTrialNo = 1;
            $answered_flag_question_ids = array();
            $not_attempted_flag_question_ids = array();
            $taking_exam_timing = '00:00:00';
            $examLanguage = app()->getLocale() ?? 'en';
            $testType = 0;
            $second = 0;
            $RemainingSeconds = 0;
            
            // Get the exam details
            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$exam_id)->first();

            $ExamMaximumSeconds = Exam::where(cn::EXAM_TABLE_ID_COLS,$exam_id)->where(cn::EXAM_TABLE_IS_UNLIMITED,1)->sum(cn::EXAM_TABLE_TIME_DURATIONS_COLS);
            if(empty($ExamMaximumSeconds)){
                $ExamMaximumSeconds = 'unlimited_time';
                $RemainingSeconds = 'unlimited_time';
            }else{
                $RemainingSeconds = $ExamMaximumSeconds;
            }
            
            // Get the question size and count
            if(isset($examDetail) && !empty($examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                // Check before attempted questions
                $HistoryStudentExamsData =  HistoryStudentExams::where([
                                                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                                                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL    => $exam_id
                                            ])->first();
                                            // Get the total allowed time in this exams                
                if(isset($HistoryStudentExamsData) && !empty($HistoryStudentExamsData) && !empty($HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL})){
                    $second = $HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_TOTAL_SECONDS_COL};
                    if($ExamMaximumSeconds !="unlimited_time"){
                        $RemainingSeconds = ($ExamMaximumSeconds - $second);
                    }
                    $QuestionId = $HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL};
                    $IsAttemptTrialNo = $HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL} ?? 1;
                    // if the first trial
                    if($IsAttemptTrialNo==1){
                        $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                        $answered_flag_question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL});
                        $not_attempted_flag_question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL});
                    }
                    if($IsAttemptTrialNo==2){
                        $question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL});
                        $answered_flag_question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL});
                        $not_attempted_flag_question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL});
                    }
                }else{
                    // If student no attempt then start from first question
                    $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                    $QuestionId = $question_ids[0];
                }

                $QuestionsFirstId = $question_ids[0];
                $QuestionsLastId = end($question_ids);
    
                // Get the question number value
                if($question_ids){
                    //$QuestionNumber = (array_search($QuestionId, $question_ids) + 1);
                    $QuestionNumber = (array_search($QuestionId, explode(',',$examDetail->question_ids)) + 1);
                }
                $questionSize = sizeof($question_ids);
                $Question = Question::with('answers')
                        ->where(cn::QUESTION_TABLE_ID_COL,$QuestionId)
                        ->first();
                if(isset($QuestionId) && !empty($QuestionId)){
                    $HistoryStudentQuestionAnswer = array();
                    if(HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                    ])->exists()){
                        if(HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                        ])                        
                        ->doesntExist()){
                            $RandomAnswerOrdering = $this->GetRandomAnswerOrdering();
                            HistoryStudentQuestionAnswer::updateOrCreate([
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => null,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL => implode(',',$RandomAnswerOrdering)
                            ]);
                        }
                        
                        $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                        ])->first();

                        $random_number_array = explode(',',$HistoryStudentQuestionAnswer->answer_ordering);
                    }else{
                        if(HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                        ])
                        ->doesntExist()){
                            $RandomAnswerOrdering = $this->GetRandomAnswerOrdering();
                            HistoryStudentQuestionAnswer::updateOrCreate([
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => null,
                                cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL => implode(',',$RandomAnswerOrdering)
                            ]);
                        }
                        $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $exam_id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                        ])->first();

                        $random_number_array = explode(',',$HistoryStudentQuestionAnswer->answer_ordering);
                    }
                }
            }
            if($examDetail->{cn::EXAM_TABLE_CREATED_BY_COL} == Auth::user()->{cn::USERS_ID_COL}){
                // This exam is personal then 1
                $testType = 1;
            }
            return view('backend/exams/student_attempt_test_exercise',compact('exam_id','Question','QuestionNumber','QuestionsFirstId','QuestionsLastId',
            'examLanguage','examDetail','taking_exam_timing','testType','questionSize','question_ids','ExamMaximumSeconds','IsAttemptTrialNo',
            'answered_flag_question_ids','not_attempted_flag_question_ids','HistoryStudentQuestionAnswer','second','RemainingSeconds','HistoryStudentExamsData',
            'random_number_array'));
        }
    }

    /**
     * USE : Update Time second
     */
    public function UpdateTimeSeconds($examId,$second){
        if(isset($second) && !empty($second)){
            HistoryStudentExams::where([
                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
            ])->Update([
                cn::HISTORY_STUDENT_EXAMS_TOTAL_SECONDS_COL => $second
            ]);
        }
    }

    /**
     * USE : Get the next question from the database
     */
    public function NextQuestion(Request $request){
        try{
            if(isset($request->second) && !empty($request->second)){
                $this->UpdateTimeSeconds($request->examid,$request->second);
            }
            $CurrentQuestionId = $request->CurrentQuestionId;
            $examId = $request->examid;
            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->first();
            if(HistoryStudentExams::where([
                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
            ])
            ->where(cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL,null)->exists()){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL => $request->no_of_trial_exam
                ]);
            }
            $HistoryStudentExamsData =  HistoryStudentExams::where([
                                            cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL    => Auth::user()->id,
                                            cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL       => $examId
                                        ])->first();
            if(isset($HistoryStudentExamsData) && !empty($HistoryStudentExamsData) && !empty($HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL})){
                if($HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL} == 2){
                    $question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL});
                    $questionIdsList = implode(',',$question_ids);
                }
                if($HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL} == 1){
                    $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                    $questionIdsList = implode(',',$question_ids);
                }
            }else{
                $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $questionIdsList = implode(',',$question_ids);
            }
            $examLanguage = ($request->language) ? $request->language : 'en';
            $Questionsfirstid = $question_ids[0];
            $Questionslastid = end($question_ids);
            if($request->examaction == 'Next'){
                $NextQuestionKey = array_search($CurrentQuestionId, $question_ids);
                $NewQuestionId = $question_ids[$NextQuestionKey+1];
                if(isset($question_ids[$NextQuestionKey+1])){
                    $Question = Question::with('answers')
                                ->where(cn::QUESTION_TABLE_ID_COL,$NewQuestionId)
                                ->first();
                }
            }elseif(($request->examaction == 'QuestionNavigation') || ($request->examaction == 'current')){
                if(isset($CurrentQuestionId)){
                    $NewQuestionId = $CurrentQuestionId;
                    $Question = Question::with('answers')
                                ->where(cn::QUESTION_TABLE_ID_COL,$CurrentQuestionId)
                                ->first();
                }
            }else{
                $NextQuestionKey = array_search($CurrentQuestionId, $question_ids);
                if(isset($question_ids[$NextQuestionKey-1])){
                    $NewQuestionId = $question_ids[$NextQuestionKey-1];
                    $Question = Question::with('answers')
                                ->where(cn::QUESTION_TABLE_ID_COL,$NewQuestionId)
                                ->first();
                }
            }

            // Get the question number value
            if($question_ids){
                //$QuestionNumber = (array_search($NewQuestionId, $question_ids) + 1);
                $QuestionNumber = (array_search($NewQuestionId, explode(',',$examDetail->question_ids)) + 1);
            }
            
            $this->UpdateQuestionPosition($examId,$NewQuestionId);

            // Selected all old question answers
            if(isset($NewQuestionId) && !empty($NewQuestionId)){
                $HistoryStudentQuestionAnswer = array();
                if(HistoryStudentQuestionAnswer::where([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                ])->exists()){
                    $FirstTrailHistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                    ])->first();

                    HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                    ])
                    ->Update([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL => $FirstTrailHistoryStudentQuestionAnswer->answer_ordering
                    ]);

                    $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 2
                    ])->first();
                    $random_number_array = explode(',',$HistoryStudentQuestionAnswer->answer_ordering);
                }else{
                    if(HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                    ])
                    ->doesntExist()){
                        $RandomAnswerOrdering = $this->GetRandomAnswerOrdering();
                        HistoryStudentQuestionAnswer::updateOrCreate([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL  => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL     => $examId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_ANSWER_ORDERING_COL => implode(',',$RandomAnswerOrdering)
                        ]);
                    }
                    $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                    ])->first();
                    $random_number_array = explode(',',$HistoryStudentQuestionAnswer->answer_ordering);
                }
            }
            
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                        }
                    }
                }
            }else{
                if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                        }
                    }
                }
            }
            $questionNo = $request->questionNo;
            $question_position = array();
            if(isset($request->question_position)){
                $question_position = array_column($request->question_position,'position','question_id');
            }

            $AttemptedQuestionIds =     HistoryStudentQuestionAnswer::where([
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam
                                        ])->pluck(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL)->toArray();
            if($request->no_of_trial_exam == 1){
                $TotalNoOfQuestions = count(explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL}));
                $RemainingQuestionCount = count(array_diff(explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL}),$AttemptedQuestionIds));
            }
            if($request->no_of_trial_exam == 2){
                $TotalNoOfQuestions = count(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}));
                $RemainingQuestionCount = count(array_diff(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}),$AttemptedQuestionIds));
            }
            $html = (string)View::make('backend/exams/next_question_test_exercise',compact('examId','Question','QuestionNumber','examLanguage','examDetail','questionNo',
            'Questionsfirstid','Questionslastid','RemainingQuestionCount','UploadDocumentsData','HistoryStudentQuestionAnswer','random_number_array'));
            return $this->sendResponse(['html' => $html,'question_id' => $NewQuestionId]);
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Update student question answer history of each trial
     */
    public function UpdateStudentQuestionAnswerHistory(Request $request){
        $response = array();
        if(isset($request->second) && !empty($request->second)){
            $this->UpdateTimeSeconds($request->exam_id,$request->second);
        }
        $HistoryStudentExamsData =  HistoryStudentExams::where([
                                        cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL    => Auth::user()->id,
                                        cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL       => $request->exam_id
                                    ])->first();
        if(isset($HistoryStudentExamsData) && !empty($HistoryStudentExamsData)){
            if($request->no_of_trial_exam == 1){ // The first trial
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL    => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL       => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL => $request->current_question_id,
                ]);
            }
            if($request->no_of_trial_exam == 2){ // The second trial
                $Update =   HistoryStudentExams::where([
                                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL    => Auth::user()->id,
                                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL       => $request->exam_id
                            ])->Update([
                                cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL          => $request->no_of_trial_exam,
                                cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL  => $request->current_question_id,
                            ]);
            }

            // Update HistoryStudentQuestionAnswer table data
            if(HistoryStudentQuestionAnswer::where([
                cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $request->current_question_id,
                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam
            ])->exists()){
                HistoryStudentQuestionAnswer::where([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $request->current_question_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam
                ])
                ->Update([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $request->current_question_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL => $request->selected_answer_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_NO_OF_SECOND_COL => $request->no_of_second,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => $request->is_answered_flag,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL => $request->language
                ]);
            }else{
                HistoryStudentQuestionAnswer::Create([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $request->current_question_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL => $request->selected_answer_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_NO_OF_SECOND_COL => $request->no_of_second,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => $request->is_answered_flag,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL => $request->language
                ]);
            }
            $response = ['status' => true];
        }else{
            // Create new history
            $InsertHistoryStudentExams = HistoryStudentExams::Create([
                                            cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                                            cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id,
                                            cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL => $request->no_of_trial_exam,
                                            cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL => $request->current_question_id,
                                        ]);
            if($InsertHistoryStudentExams){
                HistoryStudentQuestionAnswer::Create([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $request->current_question_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL => $request->selected_answer_id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_NO_OF_SECOND_COL => $request->no_of_second,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => $request->is_answered_flag,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL => $request->language
                ]);
                $response = ['status' => true];
            }else{
                $response = ['status' => false];
            }
        }

        $SelectedAnsweredFlagQuestionId = HistoryStudentQuestionAnswer::where([
            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => 'true'
        ])->pluck(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL);

        $SelectedNotAttemptedFlagQuestionId = HistoryStudentQuestionAnswer::where([
            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => 'false'
        ])->pluck(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL);

        if($request->no_of_trial_exam == 1){
            if(isset($SelectedAnsweredFlagQuestionId) && !empty($SelectedAnsweredFlagQuestionId)){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL => ($SelectedAnsweredFlagQuestionId) ? implode(',',$SelectedAnsweredFlagQuestionId->toArray()) : null
                ]);
            }
            if(isset($SelectedNotAttemptedFlagQuestionId) && !empty($SelectedNotAttemptedFlagQuestionId)){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL => ($SelectedNotAttemptedFlagQuestionId) ? implode(',',$SelectedNotAttemptedFlagQuestionId->toArray()) : null
                ]);
            }
        }

        if($request->no_of_trial_exam == 2){
            if(isset($SelectedAnsweredFlagQuestionId) && !empty($SelectedAnsweredFlagQuestionId)){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL => ($SelectedAnsweredFlagQuestionId) ? implode(',',$SelectedAnsweredFlagQuestionId->toArray()) : null
                ]);
            }
            if(isset($SelectedNotAttemptedFlagQuestionId) && !empty($SelectedNotAttemptedFlagQuestionId)){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL => ($SelectedNotAttemptedFlagQuestionId) ? implode(',',$SelectedNotAttemptedFlagQuestionId->toArray()) : null
                ]);
            }
        }
        $AttemptedQuestionIds =     HistoryStudentQuestionAnswer::where([
                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam
                                    ])->pluck(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL)->toArray();

        // Get the total no of question in this trial
        $ExamDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->exam_id)->first();
        if($request->no_of_trial_exam == 1){
            $response['TotalNoOfQuestions'] = count(explode(',',$ExamDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL}));
            $response['RemainingQuestionCount'] = count(array_diff(explode(',',$ExamDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL}),$AttemptedQuestionIds));
        }
        if($request->no_of_trial_exam == 2){
            $response['TotalNoOfQuestions'] = count(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}));
            $response['RemainingQuestionCount'] = count(array_diff(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}),$AttemptedQuestionIds));
        }
        return $this->sendResponse($response);
    }

    public function UpdateQuestionPosition($ExamId,$QuestionId){
        $HistoryStudentExamsData =  HistoryStudentExams::where([
            cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
            cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $ExamId
        ])->first();
        if(isset($HistoryStudentExamsData) && !empty($HistoryStudentExamsData)){
            HistoryStudentExams::where([
                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $ExamId
            ])->Update([
                cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL => $QuestionId,
            ]);
        }else{
            // Create new history
            $InsertHistoryStudentExams = HistoryStudentExams::Create([
                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $ExamId,
                cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL => $QuestionId,
            ]);
        }
    }

    /**
     * USE : VerifyQuestionAnswerTestExercise
     */
    public function VerifyQuestionAnswerTestExercise(Request $request){
        if(isset($request->exam_id)){
            if(isset($request->second) && !empty($request->second)){
                $this->UpdateTimeSeconds($request->exam_id,$request->second);
            }
            // Check the First trial or Second Attempt Trial
            switch($request->no_of_trial_exam){
                case 1:
                    $response = array();
                    $ExamDetail = Exam::find($request->exam_id);
                    $AssignedQuestionIds = explode(',',$ExamDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                    if(isset($AssignedQuestionIds) && !empty($AssignedQuestionIds)){
                        foreach($AssignedQuestionIds as $quePosition => $AssignedQuestionId){
                            $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                                                                cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                                                                cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $request->exam_id,
                                                                cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam,
                                                                cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $AssignedQuestionId
                                                            ])
                                                            ->first();
                            if(isset($HistoryStudentQuestionAnswer) && !empty($HistoryStudentQuestionAnswer)){
                                $language = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL} ?? 'en';
                                $AnswerDetail = Answer::where(cn::ANSWER_QUESTION_ID_COL,$AssignedQuestionId)->first()->toArray();
                                if(isset($AnswerDetail) && !empty($AnswerDetail) && ($AnswerDetail['correct_answer_'.$language] != $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL})){
                                    $response['questionNo'][] = (array_search($HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL}, $AssignedQuestionIds) + 1);
                                    $response['WrongQuestionIds'][] = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL};
                                }
                            }else{
                                $response['questionNo'][] = ($quePosition + 1);
                                $response['WrongQuestionIds'][] = $AssignedQuestionId;
                            }
                        }
                    }
                    return $this->sendResponse($response);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * USE : AttemptStudentSecondTrialExerciseTest
     */
    public function AttemptStudentSecondTrialExerciseTest(Request $request){
        $examLanguage = 'en';
        $second = $request->second;
        if(isset($request->second) && !empty($request->second)){
            $this->UpdateTimeSeconds($request->exam_id,$request->second);
        }
        $answered_flag_question_ids = array();
        $not_attempted_flag_question_ids = array();
        // If the user want to second attempt in this test-exercise
        if($request->no_of_trial_exam == 2){
            $examId = $request->exam_id;
            if(isset($request->WrongQuestionIds) && !empty($request->WrongQuestionIds)){
                // Update the "HistoryStudentExams" table
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL => $request->no_of_trial_exam,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL => implode(',',$request->WrongQuestionIds),
                    cn::HISTORY_STUDENT_EXAMS_EXAM_CURRENT_QUESTION_ID_COL => $request->WrongQuestionIds[0]
                ]);

                $HistoryStudentExamsData =  HistoryStudentExams::where([
                                                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                                                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $request->exam_id
                                            ])->first();
                if(isset($HistoryStudentExamsData) && !empty($HistoryStudentExamsData)){
                    $question_ids = explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL});
                    $questionIdsList = implode(',',$question_ids);
                }
                $Questionsfirstid = $question_ids[0];
                $Questionslastid = end($question_ids);
                $CurrentQuestionId = $request->WrongQuestionIds[0];
                if(isset($CurrentQuestionId)){
                    $NewQuestionId = $CurrentQuestionId;
                    $Question = Question::with('answers')
                                ->where(cn::QUESTION_TABLE_ID_COL,$CurrentQuestionId)
                                ->first();
                }
                // Get the question number value
                if($question_ids){
                    $QuestionNumber = (array_search($NewQuestionId, $question_ids) + 1);
                }
                $this->UpdateQuestionPosition($examId,$NewQuestionId);
                // Selected all old question answers
                if(isset($NewQuestionId) && !empty($NewQuestionId)){
                    $HistoryStudentQuestionAnswer = array();
                    // Update previous selected flag
                    HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => 'false'
                    ])->Update([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL => 'true'
                    ]);
                    $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $NewQuestionId,
                                                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                                                    ])->first();
                    $random_number_array = explode(',',$HistoryStudentQuestionAnswer->answer_ordering);
                }
            }

            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->first();

            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                        }
                    }
                }
            }else{
                if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                        }
                    }
                }
            }

            $AttemptedQuestionIds =     HistoryStudentQuestionAnswer::where([
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $request->no_of_trial_exam
                                        ])->pluck(cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL)->toArray();
            if($request->no_of_trial_exam == 2){
                $TotalNoOfQuestions = count(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}));
                $RemainingQuestionCount = count(array_diff(explode(',',$HistoryStudentExamsData->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL}),$AttemptedQuestionIds));
            }

            // Get The new Indexing html
            $IndexingHtml = $this->IndexingHtml($question_ids,$answered_flag_question_ids,$not_attempted_flag_question_ids,$CurrentQuestionId,$examDetail);
            $html = (string)View::make('backend/exams/next_question_test_exercise',compact('examId','Question','QuestionNumber','examLanguage','examDetail','Questionsfirstid',
            'Questionslastid','RemainingQuestionCount','UploadDocumentsData','HistoryStudentQuestionAnswer','random_number_array'));
            return $this->sendResponse([
                'html' => $html,
                'question_id' => $NewQuestionId,
                'IndexingHtml' => $IndexingHtml,
                'second' => $second
            ]);
        }
    }

    public function IndexingHtml($question_ids,$answered_flag_question_ids,$not_attempted_flag_question_ids,$CurrentQuestionId,$examDetail){
        $IndexingHtml = '';
        $IndexingHtml .= '<ol>';
        if($question_ids){
            $ExamsQuestionIds = explode(',',$examDetail->question_ids);
            foreach($question_ids as $QuestionIndex => $QuestionId){
                $IndexingHtml .= '<li class="test-navigation-item test-navigation-item-'.$QuestionId;
                                if(in_array($QuestionId,$answered_flag_question_ids)){
                                    $IndexingHtml .= ' answered-item ';
                                }
                                if(in_array($QuestionId,$not_attempted_flag_question_ids)){
                                    $IndexingHtml .= ' flagged-item ';
                                }
                                if($CurrentQuestionId == $QuestionId){
                                    $IndexingHtml .= ' selected_question_item ';
                                }
                                $IndexingHtml .= '"
                                data-index="'.$QuestionId.' " 
                                question-id-next="'.$QuestionId.' " 
                                data-text="QuestionNavigation">'.(array_search($QuestionId,$ExamsQuestionIds)+1).'</li>';
                }
            }
        $IndexingHtml .= '</ol>';
        return $IndexingHtml;
    }

    /**
     * USE : Student Submit Test details
     */
    public function SubmitStudentTestExercise(Request $request){        
        $SelectedLanguage = app()->getLocale() ?? 'en';
        $examId = $request->exam_id;

        // Get the selected student answers
        $HistoryStudentExams = HistoryStudentExams::where([
            cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
            cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
        ])->first();

        $examDetail = Exam::find($examId);
        
        /* This code to using personal exam start */
        $apiData = [];

        // Get The test question ids
        if(isset($examDetail) && !empty($examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
            $QuestionIds = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
            $NoOfCorrectAnswers = 0;
            $NoOfWrongAnswers = 0;
            $QuestionAnswer = array();
            $AttemptFirstTrialQuestions = array();
            $AttemptSecondTrialQuestions = array();
            foreach($QuestionIds as $QuestionId){
                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
                $FinalQuestionAnswer = array();
                
                $FinalQuestionAnswer['question_id'] = $QuestionId;
                
                // Get Questions Answers and difficulty level
                $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($QuestionId,$examDetail->{cn::EXAM_CALIBRATION_ID_COL});
                $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                $apiData['max_student_num'] = 1;

                if(isset($HistoryStudentExams) && !empty($HistoryStudentExams)){
                    if(HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL}
                    ])->exists()){
                        $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL}
                        ])->first();
                    }else{
                        $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                            cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => 1
                        ])->first();
                    }
                    if(isset($HistoryStudentQuestionAnswer) && !empty($HistoryStudentQuestionAnswer)){
                        $SelectedLanguage = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL};
                        $SelectedAnswer = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL};
                        if(isset($QuestionAnswerDetail)){
                            if($QuestionAnswerDetail->answers->{'correct_answer_'.$HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL}} === $SelectedAnswer){
                                $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                                $apiData['questions_results'][] = true;
                            }else{
                                $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                                $apiData['questions_results'][] = false;
                            }
                        }
                    }else{
                        $SelectedAnswer = 5;
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }
                    // get the student selected answer and if answer is not selected then it will conside default "No Answer"
                    $FinalQuestionAnswer['answer'] = $SelectedAnswer ?? 5;
                    
                    // set selected question language
                    $FinalQuestionAnswer['language'] = $SelectedLanguage;
                    
                    // set question duration seconds
                    $FinalQuestionAnswer['duration_second'] = '0:00:00';
                    
                    // set question difficulty type
                    $FinalQuestionAnswer['difficulty_type'] = $QuestionAnswerDetail->{cn::QUESTION_DIFFICULTY_LEVEL_COL};
                }else{
                    $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                    $apiData['questions_results'][] = false;
                    $FinalQuestionAnswer['answer'] = 5;
                    $FinalQuestionAnswer['language'] = $SelectedLanguage;
                    $FinalQuestionAnswer['duration_second'] = '0:00:00';
                    $FinalQuestionAnswer['difficulty_type'] = $QuestionAnswerDetail->{cn::QUESTION_DIFFICULTY_LEVEL_COL};
                }

                $QuestionAnswer[] = $FinalQuestionAnswer;
            }
            
            if(isset($QuestionAnswer) && !empty($QuestionAnswer)){
                $QuestionAnswer = json_encode($QuestionAnswer);
            }
            
            // Create First Trial Question Answer Array
            $AttemptFirstTrialQuestions = $this->GetQuestionAnswerJson(1,$examId);
            if(isset($AttemptFirstTrialQuestions) && !empty($AttemptFirstTrialQuestions)){
                $AttemptFirstTrialQuestions = json_encode($AttemptFirstTrialQuestions);
            }
            
            $AttemptSecondTrialQuestions = $this->GetQuestionAnswerJson(2,$examId);
            if(isset($AttemptSecondTrialQuestions) && !empty($AttemptSecondTrialQuestions)){
                $AttemptSecondTrialQuestions = json_encode($AttemptSecondTrialQuestions);
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                $requestPayload = new \Illuminate\Http\Request();
                $requestPayload = $requestPayload->replace([
                    'questions_results'=> array($apiData['questions_results']),
                    'num_of_ans_list' => $apiData['num_of_ans_list'],
                    'difficulty_list' => array_map('floatval', $apiData['difficulty_list']),
                    'max_student_num' => 1
                ]);
                $AIApiResponse = $this->AIApiService->getStudentAbility($requestPayload);
                if(isset($AIApiResponse) && !empty($AIApiResponse)){
                    $StudentAbility = $AIApiResponse[0];
                }
            }
            
            $PostData = [
                cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL        => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL            => $this->GetCurrentAdjustedCalibrationId(),
                cn::ATTEMPT_EXAMS_EXAM_ID                       => $examId,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID            => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID              => Auth::user()->CurriculumYearGradeId,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID              => Auth::user()->CurriculumYearClassId,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL                  => 'en',
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL           => $this->setQuestionDifficultyTypeAndDifficultyValue($QuestionAnswer,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}),
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL              => '',
                cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL       => !empty($AttemptFirstTrialQuestions) ? $this->setQuestionDifficultyTypeAndDifficultyValue($AttemptFirstTrialQuestions,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}) : null,
                cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL      => !empty($AttemptSecondTrialQuestions) ? $this->setQuestionDifficultyTypeAndDifficultyValue($AttemptSecondTrialQuestions,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}) : null,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS         => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS           => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING            => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL           => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL            => json_encode($this->serverData()) ?? null,
                cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL        => $HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_BEFORE_EMOJI_ID_COL} ?? null,
                cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL         => $HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_AFTER_EMOJI_ID_COL} ?? null
            ];
            $save = AttemptExams::create($PostData);
            if($save){
                //Update Column Is_my_teaching_sync
                Exam::find($examId)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                if($examDetail->exam_type == 3 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 2)){
                    $this->CronJobController->UpdateStudentOverAllAbility();
                }

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $examId);

                if($examDetail->exam_type == 2){
                    /** Update Student Credit Points via cron job */
                    $this->CronJobController->UpdateStudentCreditPoints($examId, Auth::user()->{cn::USERS_ID_COL});
                }
                /** End Update overall ability for the student **/

                if($examDetail->exam_type == 3){
                    // Start Learning Progress Learning Unit Job
                    $this->CronJobController->UpdateLearningProgressJob(Auth::user()->{cn::USERS_ID_COL});
                    // End Learning Progress Learning Unit Job
                }

                // Delete history table data
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
                ])->delete();
                HistoryStudentQuestionAnswer::where([
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId
                ])->delete();
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');
                return redirect()->route('exams.result',['examid' => $examId, 'studentid' => Auth::user()->{cn::USERS_ID_COL}]);
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }
    }

    public function GetQuestionAnswerJson($Trial,$examId){
        $NoOfCorrectAnswers = 0;
        $NoOfWrongAnswers = 0;
        $QuestionAnswerArray = [];
        $HistoryStudentExams =  HistoryStudentExams::where([
                                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL => $examId
                                ])->first();
        if($Trial == 1){
            $examDetail = Exam::find($examId);
            $QuestionIds = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
        }

        if($Trial == 2){
            if(isset($HistoryStudentExams) && !empty($HistoryStudentExams) && !empty($HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL})){
                $QuestionIds = explode(',',$HistoryStudentExams->{cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL});
            }
        }

        if(isset($QuestionIds) && !empty($QuestionIds)){
            foreach($QuestionIds as $QuestionId){
                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
                $ArrayData = array();
                $ArrayData['question_id'] = $QuestionId;
                if(isset($HistoryStudentExams) && !empty($HistoryStudentExams)){
                    $HistoryStudentQuestionAnswer = HistoryStudentQuestionAnswer::where([
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_STUDENT_ID_COL => Auth::user()->id,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_EXAM_ID_COL => $examId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_QUESTION_ID_COL => $QuestionId,
                        cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL => $Trial
                    ])->first();
                    $SelectedLanguage = 'en';
                    if(isset($HistoryStudentQuestionAnswer) && !empty($HistoryStudentQuestionAnswer)){
                        $SelectedLanguage = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL};
                        $SelectedAnswer = $HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_SELECTED_ANSWER_ID_COL};
                        if(isset($QuestionAnswerDetail)){
                            if($QuestionAnswerDetail->answers->{'correct_answer_'.$HistoryStudentQuestionAnswer->{cn::HISTORY_STUDENT_QUESTION_ANSWER_LANGUAGE_COL}} === $SelectedAnswer){
                                $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                                $apiData['questions_results'][] = true;
                            }else{
                                $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                                $apiData['questions_results'][] = false;
                            }
                        }
                    }else{
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }
                    // get the student selected answer and if answer is not selected then it will conside default "No Answer"
                    $ArrayData['answer'] = $SelectedAnswer ?? 5;
                    
                    // set selected question language
                    $ArrayData['language'] = $SelectedLanguage;
                    
                    // set question duration seconds
                    $ArrayData['duration_second'] = '0:00:00';
                    
                    // set question difficulty type
                    $ArrayData['difficulty_type'] = $QuestionAnswerDetail->{cn::QUESTION_DIFFICULTY_LEVEL_COL};
                }
                $QuestionAnswerArray[] = $ArrayData;
            }
        }
        return $QuestionAnswerArray;
    }

    /**
     * USE : Update Test-Exercise emoji feedback
     */
    public function UpdateTestExerciseFeedbackEmoji(Request $request){
        $response = array(
            'isFormSubmit' => false,
            'status' => false
        );
        if(isset($request->exam_id) && !empty($request->exam_id)){
            if(HistoryStudentExams::where([
                cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL    => $request->exam_id
            ])->exists()){
                HistoryStudentExams::where([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL    => $request->exam_id
                ])->Update([
                    cn::HISTORY_STUDENT_EXAMS_AFTER_EMOJI_ID_COL => $request->FeedbackEmojiId
                ]);
            }else{
                HistoryStudentExams::Create([
                    cn::HISTORY_STUDENT_EXAMS_STUDENT_ID_COL        => Auth::user()->id,
                    cn::HISTORY_STUDENT_EXAMS_EXAM_ID_COL           => $request->exam_id,
                    cn::HISTORY_STUDENT_EXAMS_NO_OF_TRIAL_EXAM_COL  => 1,
                    cn::HISTORY_STUDENT_EXAMS_BEFORE_EMOJI_ID_COL   => $request->FeedbackEmojiId
                ]);
            }
            $response['status'] = true;
            if($request->FeedbackType == 'after_test_exercise'){
                $response['isFormSubmit'] = true;
            }
        }
        return $this->sendResponse($response);
    }
}