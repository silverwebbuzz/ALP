<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\LearningsUnits;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningUnitOrdering;
use App\Models\Strands;
use App\Constants\DbConstant as cn;
use App\Traits\Common;

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
        
        $schoolId = Auth::user()->school_id;
       
        if(LearningUnitOrdering::where('school_id',Auth::user()->school_id)->exists()){
            // echo "<pre>";print_r($orderingFinalArray);die;
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){
                $orderingUnitData = LearningUnitOrdering::where('school_id',Auth::user()->school_id)
                                    ->where('strand_id',$request->strand)
                                    ->get()->toArray();
                foreach($orderingUnitData as $orderUnitKey => $unitData){
                    // echo '<pre>' .$unitData['id'] . ' '.array_search($unitData['id'],$orderingFinalArray) +1;
                    // echo "<pre>";print_r(array_search($unitData['id'],$orderingFinalArray) +1 );
                    LearningUnitOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('strand_id',$request->strand)
                                            ->where('learning_unit_id',$unitData['id'])
                                            ->update([
                                                'position' => $orderingFinalArray[$orderUnitKey],
                                                'index'    => (array_search($unitData['id'],$orderingFinalArray) + 1 )
                                            ]);
                }
                // die;
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
                                    'index'             => ($learningUnitKey + 1) 
                                ];
                    LearningUnitOrdering::create($postData);
                }
            }
            if(isset($request->finalOrdering) && !empty($request->finalOrdering)){                
                // Update Record
                $orderingUnitData = LearningUnitOrdering::where('school_id',Auth::user()->school_id)
                                    ->where('strand_id',$request->strand)
                                    ->get()->toArray();
                foreach($orderingUnitData as $orderUnitKey => $unitData){
                    LearningUnitOrdering::where('school_id',Auth::user()->school_id)
                    ->where('strand_id',$request->strand)
                    ->where('learning_unit_id',$unitData['id'])
                    ->update([
                                'position'  =>  $orderingFinalArray[$orderUnitKey],
                                'index'  =>  (array_search($unitData['id'],$orderingFinalArray) +1 ),
                            ]);
                }
            }
        }
        return back()->with('success_msg', __('Ordering Sorted...'));
    }
}
