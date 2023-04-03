@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec user-activity-log-page">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.user_activity.user_name')}}: {{ ($UsesDetail->name_en) ? App\Helpers\Helper::decrypt($UsesDetail->name_en) : $UsesDetail->name }} </h4>
							</div>
						</div>
					</div>
                    @if(isset($UsesDetail->schools) && !empty($UsesDetail->schools))
					<div class="row">
                        <div class="col-md-3">
							<div class="sec-title">
								<h6 class="mb-4">{{__('languages.school')}} : {{$UsesDetail->schools->DecryptSchoolNameEn}}</h6>
							</div>
						</div>
					</div>
                    @endif
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
                    
                    <form class="filterUserActivity" id="filterUserActivity" method="get">	
                        <div class="row">
                            <div class="col-lg-2 col-md-4">
                                <div class="select-lng pt-2 pb-2">
                                    <input type="text" class="input-search-box mr-2" name="searchText" value="{{request()->searchText}}" placeholder="{{__('languages.search_by_activity_text')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<div class="test-list-clandr">                                        
										<input type="text" class="form-control from-date-picker" name="from_date" value="{{ (request()->get('from_date')) }}" placeholder="{{ __('languages.from')}} {{ __('languages.date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
                            <div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<div class="test-list-clandr">
										<input type="text" class="form-control to-date-picker" name="to_date" value="{{ (request()->get('to_date'))}}" placeholder="{{ __('languages.to')}} {{ __('languages.date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
                            <div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <button type="submit" name="filter" value="filter" class="btn-search">{{__('languages.search')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Question form Listinf Start -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                        @if($ActivityLogs->isNotEmpty() && !empty($ActivityLogs))
                        <ul id="progress">
                            @foreach($ActivityLogs as $History)
                            <li class="user-activity-history-list">
                                <div class="node green"></div>
                                <p class="d-block">
                                    {{$History->user->DecryptNameEn}} 
                                    ({{date("Y-m-d h:i:s", strtotime($History->created_at))}})
                                </p>
                                <p><?php echo ucwords($History->activity_log); ?></p>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        {{__('languages.no_available_any_activity_history')}}
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@include('backend.layouts.footer') 
@endsection