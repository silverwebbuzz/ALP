<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\LearningsUnits;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningUnitOrdering;
use App\Models\Strands;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Events\UserActivityLog;

class OrderingLearningUnit extends Controller{
    use Common;

    public function getAllLearningUnit(){

        $StrandData = Strands::all();
        $LearningUnits = LearningsUnits::where('stage_id','<>',3)->get();
        $learningUnitData = $this->GetLearningUnits($StrandData[0]->id);
        $indexingData = LearningsUnits::where('strand_id',$StrandData[0]->id)->where('stage_id','<>',3)->pluck('id')->toArray();
        $positionArray = array_key_exists('position',$learningUnitData) ? array_column($learningUnitData,'position') : array_column($learningUnitData,'id');
        return view('backend.ordering_learning_unit.learning_unit_ordering',compact('positionArray','StrandData','LearningUnits','learningUnitData','indexingData'));
    }
    
    public function saveOrderingData(Request $request){
        $postData =[];
        if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
            $orderingFinalArray = explode(',',$request->finalOrdering);
        }        
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        if(LearningUnitOrdering::where('school_id',Auth::user()->{cn::USERS_SCHOOL_ID_COL})->exists()){
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
                $orderingUnitData = LearningsUnits::where('strand_id',$request->strand)->first()->toArray();
                $position = (int)$orderingUnitData['code'];
                foreach($orderingFinalArray as $orderKey => $LearningUnitId){
                    LearningUnitOrdering::where('school_id',Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                        ->where('strand_id',$request->strand)
                                        ->where('learning_unit_id',$LearningUnitId)
                                        ->update([
                                                    'position'  =>  ($position),
                                                    'index'  =>  ($position)
                                                ]);
                    $position++;
                }
            }
        }else{
            $strandData =  Strands::where('status',1)->get();
            foreach($strandData as $strand){
                $LearningsUnitsData = LearningsUnits::where('status',1)->where('stage_id','<>',3)->where('strand_id',$strand->id)->get();
                foreach($LearningsUnitsData as $learningUnitKey => $learningUnit){
                    $postData = [
                                    'school_id'         => $schoolId,
                                    'strand_id'         => $strand->id,
                                    'learning_unit_id'  => $learningUnit->id,
                                    'position'          => $learningUnit->id,
                                    'index'          => $learningUnit->id, 
                                ];
                    LearningUnitOrdering::create($postData);
                }
            }
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){                
                // Update Record
                $orderingUnitData = LearningsUnits::where('strand_id',$request->strand)->first()->toArray();
                $position = (int)$orderingUnitData['code'];
                foreach($orderingFinalArray as $orderKey => $LearningUnitId){
                    LearningUnitOrdering::where('school_id',Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                        ->where('strand_id',$request->strand)
                                        ->where('learning_unit_id',$LearningUnitId)
                                        ->update([
                                                    'position'  =>  ($position),
                                                    'index'  =>  ($position)
                                                ]);
                    $position++;
                }
            }
        }
        return back()->with('success_msg', __('languages.ordering_sorted'));
    }
}
