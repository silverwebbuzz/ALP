@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="coltainer">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">
									{{__('Games')}} 
								</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if(!empty($Games))
                    <div class="row">
                        @foreach($Games as $key => $game)
                            <div class="card col-md-3 p-2 m-2">
								<img src="{{ asset($game->image_path) }}"/>
                                <p class="center">
									<span><b>{{__('Game Title :')}}</b> {{$game->name}}</span><br/>
									<span><b>{{__('Game Description :')}}</b> {{$game->description}}</span><br/>
									{{-- <a href="{{route()}}"> --}}
										<button class="start_game btn-back mt-2" data-game_name="{{$game->name}}">Start Game</button>
									{{-- </a> --}}
								</p>
                            </div>
                        @endforeach
                    </div>
                    @endif
					
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
		
		<script>
			$(document).ready(function(){
				$(document).on("click",".start_game",function(){
					$.ajax({
						url: BASE_URL + "/api/game/login",
						type: 'POST',
						data: {
							username: 'alp_game',
							password: 12345678,
							userId:{{Auth::user()->id}}
						},
						success: function(response) {
							GameName = $(this).data('game_name');
							window.open(BASE_URL + "/play-game","_blank");
						},
						error: function(xhr, status, error) {
							toastr.error(error);
						}
					});
				});
			});
		</script>
@endsection