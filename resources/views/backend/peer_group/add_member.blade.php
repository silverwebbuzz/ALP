<form class="displayStudentProfileFilterForm" id="displayStudentProfileFilterForm" action="javascript:void(0);">	
    <div class="row">
        <div class="col-lg-2 col-md-4">
            <div class="select-lng pt-2 pb-2">
                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="student_grade_id" id="student_grade_id">
                    <option value='all'>{{ __('languages.all') }}</option>
                    @if(!empty($gradesList))
                        @foreach($gradesList as $grade)
                        @if(Auth::user()->role_id == 2)
                            <option value="{{$grade->getClass->id}}" {{ (request()->get('student_grade_id')) == $grade->getClass->id ? 'selected' : '' }}>{{ $grade->getClass->name}}</option>
                        @endif
                        @if(Auth::user()->role_id == 5
						|| Auth::user()->role_id == 7
						|| Auth::user()->role_id == 8
						|| Auth::user()->role_id == 9)
                        <option value="{{$grade->id}}" {{ (request()->get('student_grade_id')) == $grade->id ? 'selected' : '' }}>{{ $grade->name}}</option>
                        @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-md-3">
            <div class="select-lng pt-2 pb-2">
                <select name="class_type_id[]" class="form-control select-option" id="classType-select-option" multiple >
                    {!!$classTypeOptions!!}
                </select>
            </div>
        </div>
        <!-- For a Filtration on name,email & city -->
        {{-- <div class="col-lg-4 col-md-5">
            <div class="select-lng pt-2 pb-2">
                <input type="text" class="input-search-box mr-2" name="searchtext" id="searchtext" value="{{request()->get('searchtext')}}" placeholder="{{__('languages.search_by_name')}},{{__('languages.email')}},{{__('languages.user_management.city')}}">
                @if($errors->has('searchtext'))
                    <span class="validation_error">{{ $errors->first('searchtext') }}</span>
                @endif
            </div>
        </div> --}}
        
        <div class="col-lg-2 col-md-3">
            <div class="select-lng pt-2 pb-2">
                <button type="button" name="filter" value="filter" class="btn-search" id="btn-filter-member-list">{{ __('languages.search') }}</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12">
        <div class="peer-group-member-list-section">
            <table id="add-member-peer-group" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" name="" class="all-select-member-checkbox" class="checkbox">
                            </th>
                            <th class="first-head"><span>{{__('languages.name')}}</span></th>
                            <th class="first-head"><span>{{__('languages.name')}} ({{__('languages.chinese')}})</span></th>
                            <th class="sec-head selec-opt"><span>{{__('languages.email_address')}}</span></th>
                            <th class="selec-head">{{__('languages.form')}}</th>
                            <th class="selec-head">{{__('languages.class')}}</th>
                            <th class="selec-head">{{__('languages.student_code')}}</th>
                        </tr>
                    </thead>
                    <tbody class="scroll-pane">
                        @if(!empty($studentList))
                        @foreach($studentList as $User)
                        <tr>
                            <td><input type="checkbox" name="memberIds[]" class="checkbox select-member-checkbox" value="{{$User->id}}" data-alp-chat-id="{{ $User->alp_chat_user_id }}" @if(in_array($User->id, $MemberIds)) checked @endif></td>
                            <td  data-alp-chat-id="{{ $User->alp_chat_user_id }}" >{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name }}</td>
                            <td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
                            <td>{{ $User->email }}</td>
                            <td class="classname_{{$User->id}}">{{$User->grades->name ?? 'N/A'}}</td>
                            <td>{{ $User->getClassname($User->id) }}</td>
                            <td>{{ ($User->class_student_number) ? $User->class_student_number : ''}}</td>
                        </tr>
                        @endforeach
                        @endif
                </tbody>
            </table>
        </div>
    </div>
</div>