<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Models\LearningsObjectives;
use App\Models\LearningsUnits;
use App\Models\LearningObjectivesSkills;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helper;

class LearningObjectivesController extends Controller
{
    use Common;

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('LearningObjectivesList',$request);
            if(!in_array('learning_objectives_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $LearningObjectivesData = LearningsObjectives::with('LearningObjectivesSkills')->where('stage_id','<>',3)->IsAvailableQuestion()->sortable()->orderBy(cn::STRANDS_ID_COL,'DESC')->paginate($items);
            $statusList = $this->getStatusOptions();
            // Filteration on School code and School Name
            $Query = LearningsObjectives::select('*')->with('LearningObjectivesSkills')->where('stage_id','<>',3);
            if(isset($request->filter)){
                if(isset($request->LearningObjectiveName) && !empty($request->LearningObjectiveName)){
                    $Query->where(cn::LEARNING_OBJECTIVES_TITLE_EN_COL,'like','%'.$request->LearningObjectiveName.'%');
                }
                if(isset($request->LearningObjectiveCode) && !empty($request->LearningObjectiveCode)){
                    $Query->where(cn::LEARNING_OBJECTIVES_CODE_COL,$request->LearningObjectiveCode);
                }
                if(isset($request->Status)){
                    $Query->where(cn::LEARNING_OBJECTIVES_STATUS_COL,$request->Status);
                }
                if(isset($request->is_available_questions)){
                    $Query->where(cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,$request->is_available_questions);
                }
                $LearningObjectivesData = $Query->where('stage_id','<>',3)->sortable()->paginate($items);
            }
            return view('backend.learning_objective.list',compact('LearningObjectivesData','statusList','items'));
        }catch(Exception $exception){
            return redirect('strands')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('learning_objectives_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $learningUnitsList = LearningsUnits::where('stage_id','<>',3)->get();
            return view('backend.learning_objective.add',compact('learningUnitsList'));
        }catch(Exception $exception){
           return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('learning_objectives_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //Check validation
            $validator = Validator::make($request->all(), LearningsObjectives::rules($request, 'create'), LearningsObjectives::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData = array(
                cn::LEARNING_OBJECTIVES_STAGE_ID_COL        => $request->stage_id,
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL    => $request->foci_number,
                // cn::LEARNING_OBJECTIVES_TITLE_COL => $request->title_en,
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => $request->title_en,
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL    => $request->title_ch,
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL  => $request->learning_unit_id,
                cn::LEARNING_OBJECTIVES_CODE_COL => $request->code,
                cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL => $request->is_available_questions,
                cn::LEARNING_OBJECTIVES_STATUS_COL => $request->status
            );
            $this->StoreAuditLogFunction($postData,'LearningObjectives','','','Create LearningObjectives',cn::LEARNING_OBJECTIVES_TABLE_NAME,'');
            $LearningObjectives = LearningsObjectives::create($postData);
            if($LearningObjectives){
                // Save Extra skills Learning objectives
                if(isset($request->is_extra_skills) && !empty($request->is_extra_skills)){
                    foreach($request->LearningObjectivesExtraSkills as $LearningObjectivesExtraSkills){
                        if(isset($LearningObjectivesExtraSkills) && !empty($LearningObjectivesExtraSkills)){
                            LearningObjectivesSkills::updateOrCreate([
                                cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL => $LearningObjectives->id,
                                cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL => $LearningObjectivesExtraSkills
                            ]);
                        }
                    }
                }
                // Insert Mapping Table records
                $this->insertStrandsUnitsObjectivesMappingRecord($request, $LearningObjectives->{cn::LEARNING_OBJECTIVES_ID_COL});
                return redirect('learning-objective')->with('success_msg', __('languages.learning_objectives_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return redirect('learning-objective')->withError($exception->getMessage())->withInput(); 
        }
    }

    /**
     * USE : Edit Record
     */
    public function edit($id){
        try{
            if(!in_array('learning_objectives_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $learningUnitsList = LearningsUnits::where('stage_id','<>',3)->get();
            $LearningObjectivesData = LearningsObjectives::with('LearningObjectivesSkills')->where('stage_id','<>',3)->find($id);
            return view('backend.learning_objective.edit',compact('LearningObjectivesData','learningUnitsList'));
        }catch(Exception $exception){
            return redirect('learning-objective')->withError($exception->getMessage())->withInput(); 
        }
    }

    /**
     * USE : Update Record
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('learning_objectives_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), LearningsObjectives::rules($request, 'create'), LearningsObjectives::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData = array(
                cn::LEARNING_OBJECTIVES_STAGE_ID_COL        => $request->stage_id,
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => $request->foci_number,
                // cn::LEARNING_OBJECTIVES_TITLE_COL => $request->title_en,
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => $request->title_en,
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL    => $request->title_ch,
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL  => $request->learning_unit_id,
                cn::LEARNING_OBJECTIVES_CODE_COL => $request->code,
                cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL => $request->is_available_questions,
                cn::LEARNING_OBJECTIVES_STATUS_COL => $request->status
            );
            $this->StoreAuditLogFunction($postData,'LearningsObjectives',cn::LEARNING_OBJECTIVES_ID_COL,$id,'Update LearningObjectives',cn::LEARNING_OBJECTIVES_TABLE_NAME,'');
            $learningObjectives = LearningsObjectives::find($id)->update($postData);
            if($learningObjectives){
                if(isset($request->is_extra_skills) && !empty($request->is_extra_skills)){
                    // Update existing learning objective skills
                    if(isset($request->ExistingLearningObjectivesExtraSkills) && !empty($request->ExistingLearningObjectivesExtraSkills)){
                        foreach($request->ExistingLearningObjectivesExtraSkills as $LearningObjectiveSkillId => $SkillData){
                            if(isset($SkillData) && !empty($SkillData)){
                                LearningObjectivesSkills::find($LearningObjectiveSkillId)->Update([
                                    cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL => $SkillData[0]
                                ]);
                            }
                        }
                    }
                    // Add new learning objective skills
                    if(isset($request->LearningObjectivesExtraSkills) && !empty($request->LearningObjectivesExtraSkills)){
                        foreach($request->LearningObjectivesExtraSkills as $LearningObjectivesExtraSkills){
                            if(isset($LearningObjectivesExtraSkills) && !empty($LearningObjectivesExtraSkills)){
                                LearningObjectivesSkills::updateOrCreate([
                                    cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL => $id,
                                    cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL => $LearningObjectivesExtraSkills
                                ]);
                            }
                        }
                    }
                }else{
                    LearningObjectivesSkills::where(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL,$id)->delete();
                }
                return redirect('learning-objective')->with('success_msg', __('languages.learning_objectives_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * USE : Delete Record
     */
    public function destroy($id){
        try{
            if(!in_array('learning_objectives_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $strand = LearningsObjectives::where('stage_id','<>',3)->find($id);
            $this->StoreAuditLogFunction('','LearningsObjectives','','','Delete Learning Objectives ID '.$id,cn::LEARNING_OBJECTIVES_TABLE_NAME,'');
            if($strand->delete()){
                return $this->sendResponse([], __('languages.learning_objectives_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Delete Learning Objectives extra skills
     */
    public function DeleteSkillLearningObjective($id){
        try{
            if(!in_array('learning_objectives_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $LearningObjectivesSkills = LearningObjectivesSkills::find($id);
            if($LearningObjectivesSkills->delete()){
                return $this->sendResponse([], __('languages.learning_objectives_skill_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
