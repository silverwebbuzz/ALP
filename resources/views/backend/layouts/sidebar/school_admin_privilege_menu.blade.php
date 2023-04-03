@if(Auth::user()->is_school_admin_privilege_access == 'yes')
<li class="nav-item">
    <a class="nav-link text-truncate 
        {{ (request()->is('school-users') || request()->is('school-users/*') || 
            request()->is('Student') || request()->is('Student/*') || 
            request()->is('school/class/importStudent') ||
            request()->is('school/class/schoolprofile') ||
            request()->is('class') || request()->is('class/*') || 
            request()->is('teacher-class-subject-assign') ||
            request()->is('teacher-class-subject-assign/*') ||
            request()->is('school/profile') || 
            request()->is('user/activity-log') || 
            request()->is('user/activity-log/*')

        ) ? 'collapsed': '' }}" 
        href="#school_admin_privilege" data-toggle="collapse" data-target="#school_admin_privilege">
        <div class="sidebar_icon_main">
            <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.common_sidebar.school_admin')}}" alt="{{__('languages.common_sidebar.school_admin')}}">
        </div>
        <span class="text">{{__('languages.common_sidebar.school_admin')}}</span>
    </a>
    <div class="collapse 
        {{ (request()->is('school-users') || request()->is('school-users/*') || 
            request()->is('Student') || request()->is('Student/*') || 
            request()->is('school/class/importStudent') || 
            request()->is('school/class/schoolprofile') || 
            request()->is('class') || request()->is('class/*') || 
            request()->is('teacher-class-subject-assign') || 
            request()->is('teacher-class-subject-assign/*') ||
            request()->is('school/profile') ||
            request()->is('user/activity-log') || 
            request()->is('user/activity-log/*')
        ) ? 'show': '' }}" id="school_admin_privilege" aria-expanded="false">
        <ul class="flex-column pl-2 nav">
            <li class="nav-item {{ (request()->is('school/profile')) ? 'active': '' }}">
                <a class="nav-link" href="{{route('schoolprofile')}}">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.school_profile')}}" alt="{{__('languages.common_sidebar.school_profile')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.school_profile')}}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (request()->is('school-users') || request()->is('school-users/*') || request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'collapsed': '' }}" href="#account_management" data-toggle="collapse" data-target="#account_management">                    
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.account_management')}}" alt="{{__('languages.common_sidebar.account_management')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.account_management')}}</span>
                </a>
                <div class="collapse {{ (request()->is('school-users') || request()->is('school-users/*') || request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'show': '' }}" id="account_management" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item {{ (request()->is('school-users') || request()->is('school-users/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('school-users.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.school_user')}}" alt="{{__('languages.common_sidebar.school_user')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.school_user')}}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('Student.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user.png') }}"  title="{{__('languages.common_sidebar.student')}}" alt="{{__('languages.common_sidebar.student')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.student')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link text-truncate " href="#class_management" data-toggle="collapse" data-target="#class_management">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/school_management.png') }}"  title="{{__('languages.common_sidebar.class_management')}}" alt="{{__('languages.common_sidebar.class_management')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.class_management')}}</span>
                </a>
                <div class="collapse {{ (request()->is('class') || request()->is('class/*') || request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/*')) ? 'show': '' }}" id="class_management" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item {{ (request()->is('class') || request()->is('class/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('class.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/question_wizard.png') }}"  title="{{__('languages.common_sidebar.form_class')}}" alt="{{__('languages.common_sidebar.form_class')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.form_class')}}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('teacher-class-subject-assign.index')}}">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/knowledge_tree.png') }}"  title="{{__('languages.common_sidebar.assign_teacher')}}" alt="{{__('languages.common_sidebar.assign_teacher')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.assign_teacher')}}</span>
                            </a>
                        </li>            
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link text-truncate" href="#school_management_config" data-toggle="collapse" data-target="#school_management_config">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.common_sidebar.config')}}" alt="{{__('languages.common_sidebar.config')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.config')}}</span>
                </a>
                <div class="collapse" id="school_management_config" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.common_sidebar.enable_game')}}" alt="{{__('languages.common_sidebar.enable_game')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.enable_game')}}</span>
                            </a>
                        </li>  
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <div class="sidebar_icon_main">
                                    <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/setting.png') }}"  title="{{__('languages.common_sidebar.annual_credit_point')}}" alt="{{__('languages.common_sidebar.annual_credit_point')}}">
                                </div>
                                <span class="text">{{__('languages.common_sidebar.annual_credit_point')}}</span>
                            </a>
                        </li>        
                    </ul>
                </div>
            </li>

            <li class="nav-item {{ (request()->is('user/activity-log') || request()->is('user/activity-log/*')) ? 'active': '' }}">
                <a class="nav-link" href="{{route('activity-log.index')}}">
                    <div class="sidebar_icon_main">
                        <img class ="sidebar_icon" src="{{ asset('images/sidebar_icons/user_activity.png') }}"  title="{{__('languages.common_sidebar.user_activity')}}" alt="{{__('languages.common_sidebar.user_activity')}}">
                    </div>
                    <span class="text">{{__('languages.common_sidebar.user_activity')}}</span>
                </a>
            </li>
        </ul>
    </div>
</li>
@endif