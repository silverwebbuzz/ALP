@extends('backend.layouts.app')
    @section('content')
    <style>
.thankyou-page .wrapper-1{
  width:100%;
  display: flex;
    flex-direction: column;
}
.thankyou-page .wrapper-2{
  padding :30px;
  text-align:center;
}
.thankyou-page h1{
    letter-spacing:3px;
    margin:0;
    margin-bottom:20px;
}
.thankyou-page .wrapper-2 p{
  margin:0;
  font-size:1.3em;
  color:#fff;
  letter-spacing:1px;
}
.thankyou-page .go-home{
  color:#fff;
  background:#afb927 ;
  border:none;
  padding:10px 50px;
  margin:30px 0;
  border-radius:10px;
  text-transform:capitalize;
  box-shadow: 0 10px 16px 1px rgba(174, 199, 251, 1);
}
.thankyou-page .wrapper-2 {
    padding: 30px;
    text-align: center;
    background: #fff;
    border-radius: 10px;
}
.thankyou-page .go-home {
    box-shadow: 0 7px 16px -4px #afb927;
}
.thankyou-page .wrapper-2 p {
    color: #000;
    font-size: 20px;
    letter-spacing: 0
}
.thankyou-page .wrapper-2 h1{
    font-size: 50px;
}


@media (min-width:360px){
    .thankyou-page h1{
    font-size:4.5em;
  }
  .thankyou-page .go-home{
    margin-bottom:20px;
  }
}

@media (min-width:600px){
    .thankyou-page.content{
  max-width:1000px;
  margin:0 auto;
}
.thankyou-page .wrapper-1{
  height: initial;
  max-width:620px;
  margin:0 auto;
  margin-top:50px;
  box-shadow: 4px 8px 40px 8px rgba(88, 146, 255, 0.2);
}
  
}
    </style>
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            <div class="sm-right-detail-sec pl-5 pr-5">
                {{-- <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                             <div class="sec-title">
                               
                            </div> 
                        </div>
                    </div>
                </div> --}}
                @php
                $exam = $examDetail->toArray();
                @endphp
                <div class="content thankyou-page">
                <div class="wrapper-1">
                      <div class="wrapper-2">
                        <h1>{{__('languages.thank_you')}} !</h1>
                        <p>{{__('languages.the')}} {{($exam['exam_type'] == 2) ? __('languages.exercise') : __('languages.test_text')}} {{__('languages.is_submitted_result_released_on')}} {{ date('d/m/Y H:i:s',strtotime($exam['result_date'].' 23:59:59'))}}</p>
                        @if(date('Y-m-d H:i:s',strtotime($exam['result_date'].' 23:59:59') >= date('Y-m-d H:i:s')))
                          <a href="{{route('exams.result',[$exam['id'],Auth::user()->id])}}"><button class="go-home">{{__('languages.view')}} {{__('languages.result_text')}}</button></a>
                        @endif
                        @if(($exam['exam_type'] == 2))
                          <a href="{{route('getStudentExerciseExamList')}}"><button class="go-home">Go Back to {{($exam['exam_type'] == 2) ? __('languages.exercise') : __('languages.test_text')}} List</button>
                        @endif
                        @if(($exam['exam_type'] == 3))
                          <a href="{{route('getStudentTestExamList')}}"><button class="go-home">Go Back to {{($exam['exam_type'] == 2) ? __('languages.exercise') : __('languages.test_text')}} List</button>
                        @endif
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.layouts.footer')
    <script language="JavaScript">
      window.history.forward(1);
    </script>
@endsection