<div class="row study_status_colors">
    <div class="study_status_colors-sec">
        <strong>{{__('languages.study_status')}}:</strong>
    </div>
    <div class="study_status_colors-sec">
        <span class="dot-color" style="background-color:{{$ColorCodes['accomplished_color']}};border-radius: 50%;display: inline-block;"></span>
        <label>{{__('languages.achieved')}}</label>
    </div>
    <div class="study_status_colors-sec">
        <span class="dot-color" style="background-color:{{$ColorCodes['not_accomplished_color']}};border-radius: 50%;display: inline-block;"></span>
        <label>{{__('languages.to_be_achieved')}}</label>
    </div>
</div>