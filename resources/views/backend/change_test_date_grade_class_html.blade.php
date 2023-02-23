<form method="POST" action="{{route('update.grade_class.exam_end_date')}}" id="school_extend_exam_end_date">
  @csrf
  <input type="hidden" name="exam_id" value="{{$ExamDetail->id}}">
  <input type="hidden" name="school_id" value="{{$SchoolId}}">
  <div>
    <p>
      <strong>{{__('languages.title')}}</strong> : <span class="test_title">{{$ExamDetail->title ?? '' }}</span>
    </p>
  </div>
  <div>
    <p>
      <strong>{{__('languages.reference_number')}}</strong> : <span class="test_reference_number">{{$ExamDetail->reference_no ?? ''}}</span>
    </p>
  </div>
  <div>
    <div class="grade-class-date-time-list clearfix clearfix float-left">
      @if($ExamGradeClassData)
      @foreach($ExamGradeClassData as $data)
      <div class="row">
        <div class="col-md-1">
          <label>
            @if(isset($data->PeerGroup) && !empty($data->PeerGroup))
            {{$data->PeerGroup->group_name}}
            @else
            {{$data->grade->name}}{{$data->grade_class_mapping->name}}
            @endif
          </label>
        </div>
        <div class="col-md-11">
          <div class="form-row">
            <div class="form-group col-md-6 mb-50">
              <label>Start Date</label>
              <div class="input-group date">
                <input type="text" class="form-control date_picker_start_date_{{$data->id}}" id="" name="test_start_date[{{$data->id}}]" value="{{date('d/m/Y',strtotime($data->start_date))}}" placeholder="{{__('languages.start_date')}}" autocomplete="off">
                <div class="input-group-addon input-group-append">
                  <div class="input-group-text">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-6 mb-50">
              <label>End Date</label>
              <div class="input-group date">
                <input type="text" class="form-control date_picker_end_date_{{$data->id}}" name="test_end_date[{{$data->id}}]" value="{{date('d/m/Y',strtotime($data->end_date))}}" placeholder="{{__('languages.end_date')}}" autocomplete="off">
                <div class="input-group-addon input-group-append">
                  <div class="input-group-text">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          $(document).ready(function () {
            $(".date_picker_start_date_"+{{$data->id}}).datepicker({
              dateFormat: "dd/mm/yy",              
              maxDate:'<?php echo date('d/m/Y',strtotime($data->end_date)); ?>',
              yearRange: "1950:" + new Date().getFullYear(),
            });
            //$(".date_picker_start_date_"+{{$data->id}}).datepicker("option","showOn",'none');
            $(".date_picker_start_date_"+{{$data->id}}).datepicker("refresh");
            $(".date_picker_end_date_"+{{$data->id}}).datepicker({
              dateFormat: "dd/mm/yy",
              minDate:'<?php echo date('d/m/Y',strtotime($data->end_date)); ?>',
              changeMonth: true,
              changeYear: true,
              yearRange: "1950:" + new Date().getFullYear(),
            });
            $(".date_picker_end_date_"+{{$data->id}}).datepicker("refresh");
          });
        </script>
        <div class="col-md-12">
          <hr>
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>
</div>
<div class="modal-footer w-100">
  <div calss="col-lg-3 col-md-3 col-sm-3">
    <button type="submit" class="btn btn-search">{{__('languages.submit')}}</button>
  </div>
  <button type="button" class="btn btn-secondary changeExamResultOrEndDate" data-dismiss="modal">{{__('languages.test.close')}}</button>
</div>
</form>