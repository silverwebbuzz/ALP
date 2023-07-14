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
									{{__('Game Content')}} 
								</h2>
							</div>
							{{-- <div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div> --}}
							<hr class="blue-line">
						</div>
					</div>
					
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
		
@endsection