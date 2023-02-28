<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\LearningsUnits;
use  App\Models\LearningsObjectives;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningObjectiveOrdering;
use App\Models\Strands;
use App\Constants\DbConstant as cn;
use App\Traits\Common;

class OrderingLearningObjectives extends Controller
{
    use Common;
    public function getAllLearningObjectives(){
        $FociNumber = LearningsObjectives::where('stage_id','<>',3)->get()->pluck('foci_number')->toArray();
        $StrandsData = Strands::all()->pluck('id')->toArray();
        $strandsIds = implode(',',$StrandsData);
        $LearningUnits = $this->GetLearningUnits($StrandsData);
        $learningObjectiveData = $this->GetLearningObjectives($LearningUnits[0]['id']);
        $indexingData = LearningsObjectives::where('learning_unit_id',$LearningUnits[0]['id'])->where('stage_id','<>',3)->pluck('foci_number')->toArray();
        $positionArray = array_key_exists('position',$learningObjectiveData) ? array_column($learningObjectiveData,'position') : array_column($learningObjectiveData,'id');
        return view('backend.ordering_learning_objective.learning_objectives_ordering',compact('indexingData','positionArray','strandsIds','LearningUnits','learningObjectiveData','FociNumber'));
    }
    
    public function saveOrderingData(Request $request){
        $postData =[];
        if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
            $orderingFinalArray = explode(',',$request->finalOrdering);
        }
        $schoolId = Auth::user()->school_id;
       
        if(LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)->exists()){
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
                $orderingObjectiveData = LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$request->learningUnit)
                                            ->get()->toArray();
                foreach($orderingObjectiveData as $orderObjectiveKey => $objectiveData){
                    LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$request->learningUnit)
                                            ->where('learning_objective_id',$objectiveData['id'])
                                            ->update([
                                                'position'=>$orderingFinalArray[$orderObjectiveKey],
                                                'index'  =>  $request->SelectedIndex.'.'.(array_search($orderingFinalArray[$orderObjectiveKey],$orderingFinalArray) + 1)
                                            ]);
                }
            }                           
        }else{
            $LearningsUnits =  LearningsUnits::where('status',1)->where('stage_id','<>',3)->get();
            foreach($LearningsUnits as $learningUnitKey => $learningUnit){
                $LearningsObjectivesData = LearningsObjectives::where('status',1)->where('stage_id','<>',3)->where('learning_unit_id',$learningUnit->id)->get();
                foreach($LearningsObjectivesData as $learningObjectiveKey => $learningObjective){
                    $postData = [
                                    'school_id'                 => $schoolId,
                                    'learning_unit_id'          => $learningObjective->learning_unit_id,
                                    'learning_objective_id'     => $learningObjective->id,
                                    'position'                  => $learningObjective->id,
                                    'index'                     => ($learningUnitKey + 1).'.'.($learningObjectiveKey + 1)
                                ];
                    LearningObjectiveOrdering::create($postData);
                }
            }
            // update selected objective
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
                $orderingObjectiveData = LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$request->learningUnit)
                                            ->get()->toArray();
                foreach($orderingObjectiveData as $orderObjectiveKey => $objectiveData){
                    LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$request->learningUnit)
                                            ->where('learning_objective_id',$objectiveData['id'])
                                            ->update([
                                                'position'=>$orderingFinalArray[$orderObjectiveKey],
                                                'index'  =>  $request->SelectedIndex.'.'.(array_search($objectiveData['id'],$orderingFinalArray) + 1)
                                            ]);
                }
                // Update old objective value
                $orderingObjectiveData = LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$request->SelectedIndex)
                                            ->get()->toArray();
                foreach($orderingObjectiveData as $orderObjectiveKey => $objectiveData){
                    LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                    ->where('learning_unit_id',$request->SelectedIndex)
                    ->where('learning_objective_id',$objectiveData['id'])
                    ->update([
                        'index'  =>  $request->learningUnit.'.'.( $orderObjectiveKey + 1)
                    ]);
                }
            }  
        }
        return back()->with('success_msg', __('languages.ordering_sorted'));
    }
}
