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
            request()->is('school/profile') 

        ) ? 'collapsed': '' }}" 
        href="#school_admin_privilege" data-toggle="collapse" data-target="#school_admin_privilege">
        <span class="fa"><i class="fa fa-cogs"></i></span>
        <span class="text">{{__('School Admin')}}</span>
    </a>
    <div class="collapse 
        {{ (request()->is('school-users') || request()->is('school-users/*') || 
            request()->is('Student') || request()->is('Student/*') || 
            request()->is('school/class/importStudent') || 
            request()->is('school/class/schoolprofile') || 
            request()->is('class') || request()->is('class/*') || 
            request()->is('teacher-class-subject-assign') || 
            request()->is('teacher-class-subject-assign/*') ||
            request()->is('school/profile')
        ) ? 'show': '' }}" id="school_admin_privilege" aria-expanded="false">
        <ul class="flex-column pl-2 nav">
            <li class="nav-item {{ (request()->is('school/profile')) ? 'active': '' }}">
                <a class="nav-link" href="{{route('schoolprofile')}}">
                    <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                    <span class="text">{{__('School Profile')}}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-truncate {{ (request()->is('school-users') || request()->is('school-users/*') || request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'collapsed': '' }}" href="#account_management" data-toggle="collapse" data-target="#account_management">
                    <span class="fa"><i class="fa fa-cogs"></i></span>
                    <span class="text">{{__('Account Management')}}</span>
                </a>
                <div class="collapse {{ (request()->is('school-users') || request()->is('school-users/*') || request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'show': '' }}" id="account_management" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item {{ (request()->is('school-users') || request()->is('school-users/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('school-users.index')}}">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('School User')}}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('Student') || request()->is('Student/*') || request()->is('school/class/importStudent')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('Student.index')}}">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('Student')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link text-truncate " href="#class_management" data-toggle="collapse" data-target="#class_management">
                    <span class="fa"><i class="fa fa-cogs"></i></span>
                    <span class="text">{{__('Class Management')}}</span>
                </a>
                <div class="collapse {{ (request()->is('class') || request()->is('class/*') || request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/*')) ? 'show': '' }}" id="class_management" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">
                        <li class="nav-item {{ (request()->is('class') || request()->is('class/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('class.index')}}">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('Form/Class')}}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('teacher-class-subject-assign') || request()->is('teacher-class-subject-assign/*')) ? 'active': '' }}">
                            <a class="nav-link" href="{{route('teacher-class-subject-assign.index')}}">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('Assign Teacher')}}</span>
                            </a>
                        </li>            
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link text-truncate" href="#school_management_config" data-toggle="collapse" data-target="#school_management_config">
                    <span class="fa"><i class="fa fa-cogs"></i></span>
                    <span class="text">{{__('Config')}}</span>
                </a>
                <div class="collapse" id="school_management_config" aria-expanded="false">
                    <ul class="flex-column pl-2 nav">

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('Enable Game')}}</span>
                            </a>
                        </li>  
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="fa sub-menu"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                <span class="text">{{__('Annual Credit Point')}}</span>
                            </a>
                        </li>        
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span class="fa "><i class="fa fa-cogs"></i></span>
                    <span class="text">{{__('User Activity')}}</span>
                </a>
            </li>
        </ul>
    </div>
</li>
@endif