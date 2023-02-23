@extends('backend.layouts.app')
    @section('content')
		@php
			$permissions = [];
			$user_id = auth()->user()->id;
			if($user_id){
				$module_permission = App\Helpers\Helper::getPermissions($user_id);
				if($module_permission && !empty($module_permission)){
					$permissions = $module_permission;
				}
			}else{
				$permissions = [];
			}
		@endphp
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					@if(session()->has('success_msg'))
					<div class="alert alert-success">
						{{ session()->get('success_msg') }}
					</div>
					@endif
					@if(session()->has('error_msg'))
					<div class="alert alert-danger">
						{{ session()->get('error_msg') }}
					</div>
					@endif
					<div class="row">
                        <form method="post" action="{{route('save-learning-unit-ordering')}}">
                            @csrf
                            @method("post")
                            <input type="hidden" value="" name="finalOrdering" id="finalOrdering"/>
                            <div class="col-md-12">
                                <div class="col-md-3 mb-2">
                                    <select name="strand" class="form-control select-option" id="ordering_strand_id">
                                        @if(isset($StrandData) && !empty($StrandData))
                                            @foreach($StrandData as $key => $Strand)
                                                <option value="{{$Strand->id}}" @if($key==0) selected @endif>{{$Strand->{'name_'.app()->getLocale()} }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div  class="question-bank-sec">
                                    <div class="d-flex review-question-main tab-content-wrap">
                                        <div class="review-question-left-section">
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" aria-orientation="vertical">
                                                @foreach($learningUnitData as $key => $learningUnit)
                                                    <span class="w-10 nav-link ordering-unit-objective Indexing d-inline-block @if($key==0) active @endif" >{{($key+1)}}</span>
                                                    <li class="w-90 ordering-unit-objective nav-item">
                                                        <p class="nav-link learning_unit_ordering @if($key==0) active @endif" data-id={{$learningUnit['id']}} > {{ $learningUnit['name_'.app()->getLocale()]}} ({{$learningUnit['id']}})</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="review-question-right-section">
                                        </div>
                                        <div class="btn_group mb-3 review-question-position-button">
                                                <button type="button" class="btn-search bg-pink btn-up"><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.up')}}</button>
                                                <button type="button" class="btn-search bg-pink  btn-down"><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.down')}}</button>
                                                <button type="button" class="btn-search bg-pink set-top"><i class="fa fa-arrow-up" aria-hidden="true"></i><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_top')}}</button>
                                                <button type="button" class="btn-search set-bottom bg-pink"><i class="fa fa-arrow-down" aria-hidden="true"></i><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_bottom')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row select-data float-left">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group mb-50 btn-sec">
                                        <button type="submit" name="save" value="save" class="blue-btn btn btn-primary"  value="save" id="save">{{__('languages.save')}}</button>                                                
                                    </div>
                                </div>
                            </div>
                        </form>
				    </div>
			    </div>
	        </div>
		</div>
    </div>
		@include('backend.layouts.footer')
<script>
    $(document).ready(function () {
        var lastposition = $("#pills-tab li p:last").data('id');
        
        var position1_array = {!! json_encode($positionArray) !!};
        var position =$("#pills-tab li p.nav-link.active").data('id');
        console.log(position1_array);
        $(document).on("change", "#ordering_strand_id", function () {
            html = '';
            $.ajax({
                url: BASE_URL + "/getMultiLearningUnitFromStrands",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    strand_id: $("#ordering_strand_id").val(),
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    $(".review-question-left-section").html("");
                    position1_array = [];
                    var data = JSON.parse(JSON.stringify(response));
                    if (data.data) {
                        var tab_left = '';
                        var tab_right = '';
                        var qIndex = 1;
                        html +='<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" aria-orientation="vertical">';
                           
                        $.each(data.data, function(K,Q) {
                            var tab_active = '';
                            var tab_active_contact = '';
                            if(qIndex == 1){
                                tab_active = 'active';
                                tab_active_contact = 'show active';
                            }
                            html+= '<span class="w-10 nav-link ordering-unit-objective Indexing d-inline-block '+tab_active+'" >'+qIndex+'</span>';
                            html+='<li class="w-90 ordering-unit-objective nav-item">';
                            html+='<p class="nav-link learning_unit_ordering '+tab_active+'" data-id="'+this.id+'" >'+ this["name_"+APP_LANGUAGE]+' ('+ this.id +')</p>';
                            html+='</li>';
                            position1_array.push(this.id);
                            qIndex++;
                        });
                        html += "</ul>";
                        $(".review-question-left-section").html(html);
                        console.log(position1_array);
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        $(document).on('click','.learning_unit_ordering',function(){
            position = ((position1_array.indexOf($(this).data('id'))) ); 
            $(".learning_unit_ordering").removeClass("active");
            $(".Indexing").removeClass("active");
            $(this).addClass("active");
            $(this).parent().prev().addClass('active');
        });

        $(document).on('click','.btn-up',function(){
            selectedPositionId =$("#pills-tab li p.nav-link.active").data('id');
            position = (position1_array.indexOf(selectedPositionId));
            var $current = $("#pills-tab li p.nav-link.active").closest('li');
            var $currentSpan = $current.prev('span');
            var $PreviousSpan = $currentSpan.prev().prev();
            var $previousContent = $("#pills-tab li p.nav-link.active").closest('li').prev().prev();
            
            if($PreviousSpan.length !== 0){
                $(".Indexing").removeClass("active");
                $PreviousSpan.addClass("active");
                $current.insertAfter($PreviousSpan);
                $previousContent.insertAfter($currentSpan);
                $("#finalOrdering").val(array_move(position1_array,position,position-1));
                position--;
            }            
        });

        $(document).on('click','.btn-down',function(){
            selectedPositionId =$("#pills-tab li p.nav-link.active").data('id');
            position = (position1_array.indexOf(selectedPositionId));
            // var $current = $("#pills-tab li p.nav-link.active").closest('li');
            // var $next = $current.next('li');
            var $currentSpan = $("#pills-tab li p.nav-link.active").closest('li').prev();
            var $nextContent = $("#pills-tab li p.nav-link.active").closest('li').next().next();
            var $current = $("#pills-tab li p.nav-link.active").closest('li');
            var $next = $current.next('span');
            
            if($next.length !== 0){
                $(".Indexing").removeClass("active");
                $next.addClass('active');
                $current.insertAfter($next);
                $nextContent.insertAfter($currentSpan);
                $("#finalOrdering").val(array_move(position1_array,position,position+1));
                position++;
            }
        });

        $(document).on('click','.set-top',function(){
            selectedPositionId =$("#pills-tab li p.nav-link.active").data('id');
            position = (position1_array.indexOf(selectedPositionId));
            topPosition = position1_array.indexOf($("#pills-tab li p").first().data('id'));
            var $TopContent = $("#pills-tab li:eq(0)");
            var $TopSpan = $("#pills-tab span:eq(0)");
            var $CurrentSpan = $("#pills-tab li p.nav-link.active").closest('li').prev();
            var $currentContent = $("#pills-tab li p.nav-link.active").closest('li');
            
            
            if($TopSpan.length !== 0){
                // $current.insertBefore($previous);
                $(".Indexing").removeClass("active");
                $TopSpan.addClass('active');
                $TopContent.insertAfter($CurrentSpan);
                $currentContent.insertAfter($TopSpan);
                // $("#finalOrdering").val(array_move(position1_array,position,topPosition));
                $lastValue = position1_array[topPosition];//last value
                $firstValue = position1_array[position]; // First Value
                position1_array[position] = $lastValue;
                position1_array[topPosition] = $firstValue;
                position = lastposition;
                position = 1;
                $("#finalOrdering").val(position1_array);
                                
            }
        });

        $(document).on('click','.set-bottom',function(){
            selectedPositionId =$("#pills-tab li p.nav-link.active").data('id');
            position = (position1_array.indexOf(selectedPositionId));
            bottomposition = position1_array.indexOf($("#pills-tab li p:last").data('id'));
            // var $current = $("#pills-tab li p.nav-link.active").closest('li');
            // var $previous = $("#pills-tab li").last();
            var $BottomContent = $("#pills-tab li:last");
            var $BottomSpan = $("#pills-tab span:last");
            var $CurrentSpan = $("#pills-tab li p.nav-link.active").closest('li').prev();
            var $currentContent = $("#pills-tab li p.nav-link.active").closest('li');

            if($BottomContent.length !== 0){
                // $current.insertAfter($previous);
                $(".Indexing").removeClass("active");
                $BottomSpan.addClass('active');
                $BottomContent.insertAfter($CurrentSpan);
                $currentContent.insertAfter($BottomSpan);
                // $("#finalOrdering").val(array_move(position1_array,position,bottomposition));
                $lastValue = position1_array[bottomposition];//last value
                $firstValue = position1_array[position]; // First Value
                position1_array[position] = $lastValue;
                position1_array[bottomposition] = $firstValue;
                position = lastposition;
                $("#finalOrdering").val(position1_array);
            }
        });
        
        // 
        function array_move(arr, old_index, new_index) {
            if (new_index >= arr.length) {
                var k = new_index - arr.length + 1;
                while (k--) {
                    arr.push(undefined);
                }
            }
            arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
            return arr; //return new array
        };

    });
   
</script>
@endsection