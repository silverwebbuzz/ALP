<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Grades;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Models\GradeClassMapping;
use App\Helpers\Helper;
use League\Csv\Writer;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Auth;
use App\Constants\DbConstant As cn;
use App\Events\UserActivityLog;

use Exception;
class ExportController extends Controller
{
    use ResponseFormat,Common;

    /**
     * USE : Export user using upload csv file
     */
    public function exportUsers(Request $request){
        try{
           $userList = User::with('roles')->with('schools')->with('grades')->get();
           $csvExporter = new \Laracsv\Export();
           $csvExporter->beforeEach(function ($user) {
                $user->id                   = $user->id;
                $user->roles->role_name     = ucfirst($user->roles->role_name);
                $user->name                 = ucfirst($user->name);
                $user->email                = $user->email;
                $user->mobile_no            = $user->mobile_no;
                $user->dob                  = $user->dob;
                $user->city                 = ucfirst($user->city);
                $user->address              = ucfirst($user->address);
                $user->gender               = ucfirst($user->gender);
                $user->status               = ucfirst($user->status);
                $user->created_at           = $user->created_at;
            });
            $csvExporter->build($userList, [
                'id'                 => __('languages.user').' '.__('languages.id'),
                'roles.role_name'    => __('languages.role').' '.__('languages.name'),
                'name'               => __('languages.user').' '.__('languages.name'),
                'email'              =>  __('languages.email_address'),
                'mobile_no'          =>  __('languages.phone_number'),
                'dob'                => __('languages.user_management.date_of_birth'),
                'city'               => __('languages.region'),
                'address'            => __('languages.user_management.address'),
                'gender'             => __('languages.gender'),
                'grades.name'        => __('languages.form'),
                'schools.school_name'=> __('languages.school').' '.__('languages.name'),
                'status'             => __('languages.status'),
                'created_at'         => __('languages.joining_date')
            ])->download('Users.CSV');
        }catch(Exception $exception){
           return back()->withError($exception->getMessage());
        }
    }

    public function exportQuestions(Request $request){
        try{
            // $questionList = Question::with('schools')->get();
            $questionList = Question::get();
            $csvExporter = new \Laracsv\Export();
            $csvExporter->beforeEach(function ($question) {
                $question->id                       = $question->id;
                $question->question_code            = $question->question_code;
                $question->naming_structure_code    = $question->naming_structure_code;
                $question->question_unique_code     = $question->question_unique_code;
                $question->question_en              = $question->question_en; 
                $question->question_ch              = $question->question_ch;
                //$question->question_type            = ($question->question_type == '1') ? 'Excercise' : 'Learning';
                if($question->question_type == 1){
                    $question->question_type = 'Self-Learning';
                }elseif($question->question_type == 2){
                    $question->question_type = 'Exercise/Assignment';
                }elseif($question->question_type == 3){
                    $question->question_type = 'Testing';
                }else{
                    $question->question_type = 'Seed';
                }
                
                if($question->dificulaty_level == 1){
                    $question->dificulaty_level = "Easy";
                }elseif($question->dificulaty_level == 2){
                    $question->dificulaty_level = "Medium";
                }elseif($question->dificulaty_level == 3){
                    $question->dificulaty_level = "Difficult";
                }else{
                    $question->dificulaty_level = "Tough";
                }

                $question->general_hints            = $question->general_hints;
                $question->e                        = $question->e;
                $question->f                        = $question->f;
                $question->g                        = $question->g;
                // $question->schools->school_name     = $question->schools->school_name;
                $question->status                   = $question->status;
                $question->created_at               = $question->created_at;                             
            });
            $csvExporter->build($questionList, 
            [
                'id'                    => __('languages.question').' '.__('languages.id'),
                'question_code'         => __('languages.question_code'),
                'naming_structure_code' => __('languages.naming_structure_code'),
                'question_unique_code'  => __('languages.question').' '.__('languages.unique').' '.__('languages.code'),
                'question_en'           => __('languages.question'),
                'question_ch'           => __('languages.question').' '.'('.__('languages.chinese').')',
                'question_type'         => __('languages.question').' '.__('languages.type'),
                'dificulaty_level'      => __('languages.difficulty_level'),
                'general_hints'         => __('languages.general_hints'),
                'e'                     => __('languages.questions.e'),
                'f'                     =>__('languages.questions.f'),
                'g'                     => __('languages.questions.g'),
                // 'schools.school_name'   => "School Name",
                'status'                => __('languages.status'),
                'created_at'            => __('languages.created_date')
            ])->download('Questions.CSV');
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
        
    }

    /**
     * USE : Export Performance Report
     */
    public function exportPerformanceReport(Request $request){
        //define Variables
        $questionIds = '';
        $AttemptExamData = [];
        $studentIds = '';
        $QuestionList = '';
        $correctAnswerArray = [];
        $QuestionAnswerData = [];
        $examData = '';
        $records = [];
        $QuestionHeaders = [];
        $totalStudent = 0;
        
        //Get Exam ID
        $examId = $request->examId;
        $classIds = $request->classIds;
        $groupIds = $request->groupIds;
        //Set Main Header Row
        $header = [
            __('languages.class'),
            __('languages.student_number')
        ];

        //Set Correct Answer Row
        $correctAnswerArray = [
            '',
           __('languages.correct_answers')
        ];

        //Set Sub Main Header
        $QuestionHeaders = [
            '',
            __('languages.questions_text')
        ];
        //Get Exam Data
        $ExamData = Exam::find($examId);
        if(!empty($ExamData)){
            //Get Questions in Exam Assigns
            if(!empty($ExamData->question_ids)){
                $questionIds= explode(',',$ExamData->question_ids);
                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
            }
            
            //Get Student data in Exam Assigns
            if(!empty($ExamData->student_ids)){
                $studentIds = explode(',',$ExamData->student_ids);
            }

            //Set Header And Correct Answer Array
            foreach($QuestionList as $questionKey =>  $question){
                $header[] = __('languages.q').($questionKey + 1);
                $QuestionHeaders[] = ($questionKey + 1);
                $correctAnswerArray[] = $this->setOptionBasedAlphabet($question->answers->correct_answer_en);
            }
            if($ExamData->exam_type != 1){
                $header[] = __('languages.overall_percentile');
            }
            // Store in first row headings
            $records[] = $correctAnswerArray;
            $Query = AttemptExams::with('user')->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId);
            $getAllStudentIds = [];
            $getStudentsFromClassIds = [];
            $getStudentFromGroupIds = [];
            if($this->isAdmin()){
                if(empty($groupIds) && empty($classIds)){
                    $userData = User::find($ExamData->student_ids);
                    $AttemptExamData = $Query->whereHas('user',function($q) use($userData){
                        $q->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                            ->where(cn::USERS_ID_COL,$userData->id);
                        })->get();
                }else{
                    if(!empty($classIds)){
                        $getStudentsFromClassIds = User::get()->whereIn('CurriculumYearClassId',$classIds)->pluck(cn::USERS_ID_COL)->toArray();
                    }
                    if(!empty($groupIds)){
                        $getStudentFromGroupIds =   PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                    ->whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$groupIds)
                                                    ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->unique()->toArray();
    
                    }
                    $getAllStudentIds = array_unique(array_merge($getStudentsFromClassIds,$getStudentFromGroupIds));
    
                    $AttemptExamData =  $Query->whereHas('user',function($q) use($getAllStudentIds){
                                            $q->whereIn(cn::USERS_ID_COL,$getAllStudentIds);
                                        })->get();
                }
            }
            if($this->isTeacherLogin()){
                if(empty($groupIds) && empty($classIds)){
                    $userData = User::find($ExamData->student_ids);
                    $AttemptExamData = $Query->whereHas('user',function($q) use($userData){
                        $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                            ->where(cn::USERS_ID_COL,$userData->id);
                        })->get();
                }elseif(empty($groupIds)){
                    $TeachersGradeClass =   TeachersClassSubjectAssign::where([
                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                            ])
                                        ->where(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,Auth::user()->{cn::USERS_ID_COL});
                    if(!empty($TeachersGradeClass)){
                        $assignTeacherGrades = $TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
                        if(!empty($TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray())){
                            $assignTeacherClass = explode(',',implode(',',$TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray()));
                            $AttemptExamData = $Query->whereHas('user',function($q) use($assignTeacherGrades,$assignTeacherClass, $classIds){
                                $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                ->whereIn('id',$this->curriculum_year_mapping_student_ids($assignTeacherGrades,$classIds));
                            })->get();
                        }
                    }
                }else{
                    $peerGroupIds = PeerGroup::whereIn('id',$groupIds)
                                    ->where([
                                        cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ])
                                    ->pluck(cn::PEER_GROUP_ID_COL)->toArray();
                    if(!empty($peerGroupIds)){
                        $peerGroupMemberIds =   PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupIds)
                                                ->where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->unique()->toArray();
                        $AttemptExamData =  $Query->whereHas('user',function($q) use($peerGroupMemberIds){
                                                $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                ->whereIn(cn::USERS_ID_COL,$peerGroupMemberIds);
                                            })->get();
                    }
                }
                                
            }
            if($this->isPrincipalLogin() || $this->isSchoolLogin() || $this->isPanelHeadLogin() || $this->isCoOrdinatorLogin()){
                if(empty($groupIds) && empty($classIds)){
                    $userData = User::find($ExamData->student_ids);
                    $AttemptExamData =  $Query->whereHas('user',function($q) use($userData){
                                            $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                            ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                            ->where(cn::USERS_ID_COL,$userData->id);
                                        })->get();
                }elseif(empty($groupIds)){
                    
                    $AttemptExamData = $Query->whereHas('user',function($q) use($classIds){
                        $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                        ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                        ->whereIn('id',$this->curriculum_year_mapping_student_ids('',$classIds));
                    })->get();
                }else{
                    $peerGroupIds = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                    ->whereIn(cn::PEER_GROUP_MEMBERS_ID_COL,$groupIds)
                                    ->pluck(cn::PEER_GROUP_MEMBERS_ID_COL)->toArray();
                    if(!empty($peerGroupIds)){
                        $peerGroupMemberIds =   PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupIds)
                                                ->where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->unique()->toArray();
                        $AttemptExamData =  $Query->whereHas('user',function($q) use($peerGroupMemberIds){
                                                $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                ->whereIn(cn::USERS_ID_COL,$peerGroupMemberIds);
                                            })->get();
                    }
                }
            }
            if($AttemptExamData->isNotEmpty()){
                $totalStudent = count($AttemptExamData);
                foreach($AttemptExamData as $attemptedExamKey => $attemptedExam){
                    $rowArray = [];
                    $rowArray[] = $attemptedExam->user->class;
                    $rowArray[] = ($attemptedExam->user->CurriculumYearData['class_student_number']!='') ? $attemptedExam->user->CurriculumYearData['class_student_number'] : $this->decrypt($attemptedExam->user->name_en);
                    
                    foreach($QuestionList as $questionKey => $questions){
                    // get Selected Answer
                        if(isset($attemptedExam->question_answers)){
                            $filterAttemptQuestionAnswer = array_filter(json_decode($attemptedExam->question_answers), function ($var) use($questions){
                                if($var->question_id == $questions->id){
                                    return $var ?? [];
                                }
                            });
                            //Count Answer Selected By Student
                            if(isset($filterAttemptQuestionAnswer) && !empty($filterAttemptQuestionAnswer)){
                                foreach($filterAttemptQuestionAnswer as $fanswer){
                                    $objToArrayConvert = get_object_vars($fanswer);
                                    $rowArray[] = $this->setOptionBasedAlphabet($objToArrayConvert['answer']);
                                    if($fanswer->answer == 1){
                                        if(isset($QuestionAnswerData[$questionKey]['1'])){
                                            $QuestionAnswerData[$questionKey]['1'] = $QuestionAnswerData[$questionKey]['1'] + 1;

                                        }else{
                                            $QuestionAnswerData[$questionKey]['1'] = 1;
                                        }
                                    }
                                    if($fanswer->answer == 2){
                                        if(isset($QuestionAnswerData[$questionKey]['2'])){
                                            $QuestionAnswerData[$questionKey]['2'] = $QuestionAnswerData[$questionKey]['2'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['2'] = 1;
                                        }
                                    }
                                    if($fanswer->answer == 3){
                                        if(isset($QuestionAnswerData[$questionKey]['3'])){
                                            $QuestionAnswerData[$questionKey]['3'] = $QuestionAnswerData[$questionKey]['3'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['3'] = 1;
                                        }
                                    }
                                    if($fanswer->answer == 4){
                                        if(isset($QuestionAnswerData[$questionKey]['4'])){
                                            $QuestionAnswerData[$questionKey]['4'] = $QuestionAnswerData[$questionKey]['4'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['4'] = 1;
                                        }
                                    }
                                    if($fanswer->answer == 5){
                                        if(isset($QuestionAnswerData[$questionKey]['N'])){
                                            $QuestionAnswerData[$questionKey]['N'] = $QuestionAnswerData[$questionKey]['N'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['N'] = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($ExamData->exam_type != 1){
                        $rowArray[] = $this->GetStudentPercentileRank($examId,$attemptedExam->user->id).'%';
                    }
                    $records[] = $rowArray;
                }
                // Question headings
                $records[] = $QuestionHeaders;

                
                //Set Selected Answers Row On Based Question
                // $HeadingIndexArray = ['1','2','3','4','N','1%','2%','3%','4%','N%',__('languages.report.correct').'%'];
                $HeadingIndexArray;
                if($ExamData->exam_type != 1){
                    $HeadingIndexArray = ['1','2','3','4','N','1%','2%','3%','4%','N%','Correct %'];
                }else{
                    $HeadingIndexArray = ['1','2','3','4','N'];
                }
                for($row = 1;$row <= count($HeadingIndexArray);$row++){
                    $rowArray = [];
                    if($row == 1){
                        $rowArray[] = ($ExamData->exam_type == 1 ) ? '' : __('languages.total_students') .' : ' .$totalStudent;
                        $rowArray[] = ($HeadingIndexArray[$row-1]);
                    }else{
                        $rowArray[] = '';
                        $rowArray[] = ($HeadingIndexArray[$row-1]);
                    }

                    foreach($QuestionList as $questionKey => $question){
                        if($ExamData->exam_type != 1 || ($ExamData->exam_type == 1 && $row <=5)){
                            switch($row){
                                case 1 :    //Case 1 : is used For Display Row  of 1 = No. of Student Selected Answer 1
                                    $rowArray[] = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                    break;
                                case 2:     //Case 2 : is used For Display Row  of 2 = No. of Student Selected Answer 2
                                    $rowArray[] = ($QuestionAnswerData[$questionKey]['2']) ?? 0;
                                    break;
                                case 3:     //Case 3 : is used For Display Row  of 3 = No. of Student Selected Answer 3
                                    $rowArray[] = ($QuestionAnswerData[$questionKey]['3']) ?? 0;
                                    break;
                                case 4:     //Case 4 : is used For Display Row  of 4 = No. of Student Selected Answer 4
                                    $rowArray[] = ($QuestionAnswerData[$questionKey]['4']) ?? 0;
                                    break;
                                case 5:     //Case 4 : is used For Display Row  of N = No. of Student Selected Answer N
                                    $rowArray[] = ($QuestionAnswerData[$questionKey]['N']) ?? 0;
                                    break;
                                case 6:     //Case 5 : is used For Display Row  of 1(%) = Average of Student Selected Answer 1(%)
                                    $value = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                    $rowArray[] = round((($value * 100) / $totalStudent),2);
                                    break;
                                case 7:     //Case 5 : is used For Display Row  of 2(%) = Average of Student Selected Answer 2(%)
                                    $value = ($QuestionAnswerData[$questionKey]['2']) ?? 0;
                                    $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                    break;
                                case 8:     //Case 5 : is used For Display Row  of 3(%) = Average of Student Selected Answer 3(%)
                                    $value = ($QuestionAnswerData[$questionKey]['3']) ?? 0;
                                    $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                    break;
                                case 9:     //Case 5 : is used For Display Row  of 4(%) = Average of Student Selected Answer 4(%)
                                    $value = ($QuestionAnswerData[$questionKey]['4']) ?? 0;
                                    $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                    break;
                                case 10:     //Case 5 : is used For Display Row  of N(%) = Average of Student Selected Answer N(%)
                                    $value = ($QuestionAnswerData[$questionKey]['N']) ?? 0;
                                    $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                    break;
                                case 11://Average of Student Selected  Correct Answer(%)
                                    $value = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                    $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                    break;
                            }
                        }
                    }
                    //Total Attempted Student + Sub Main Heading + row // Maintain Rows(with Sub Heading)
                    $records[(count($ExamData->attempt_exams) +1)+$row] =  $rowArray;
                }
            }else{
                return $this->sendError(__('languages.no_any_student_attempt_exam_you_have_selected_class'), 422);
            }
            $fileName = 'ClassPerformanceReport.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
            $columns = $header;
            $callback = function () use ($records, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($records as $task) {
                    fputcsv($file, $task);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers); 
        }
    }

    /***
     * USE : Export Csv File of Student of School
     */
    public function exportStudents(Request $request){
        $userList = User::with('schools')
                    ->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                    ->where(cn::USERS_STATUS_COL,'active')->get();
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($student){
            $student->email                         = $student->email;
            $student->password                      = '';
            $student->name_en                       = $student->DecryptNameEn;
            $student->name_ch                       = $student->DecryptNameCh;
            $student->permanent_reference_number    = $student->permanent_reference_number;
            $student->grade_id                      = $student->CurriculumYearGradeId;
            $student->class_id                      = !empty($student->CurriculumYearClassId) ? $this->getSingleClassName($student->CurriculumYearClassId) : '';
            $student->student_number_within_class   = $student->CurriculumYearData['student_number_within_class'];
        });        
        $csvExporter->build($userList, [
            'email'                                 => __('languages.email_address'),
            'password'                              => __('languages.password'),
            'name_en'                               => __('languages.name'),
            'name_ch'                               => __('languages.name').' '.'('.__('languages.chinese').')',
            'permanent_reference_number'            => __('languages.std_number'),
            'grade_id'                              => __('languages.form'),
            'class_id'                              => __('languages.class'),
            'student_number_within_class'           => __('languages.student_number'),
        ])->download('Students.CSV');
    }
}
