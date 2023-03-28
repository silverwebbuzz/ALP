<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modules;
use App\Constants\DbConstant as cn;

class ModulesTableSeeder extends Seeder {
    public function run(){
        $modules = [
            [
                'module_name'     => 'Question Bank',
                'module_slug'     => 'question_bank',
            ],
            [   
                'module_name'     => 'User Management',
                'module_slug'     => 'user_management',
            ],
            [   
                'module_name'     => 'Group Management',
                'module_slug'     => 'group_management',
            ],
            [   
                'module_name'     => 'Exam Management',
                'module_slug'     => 'exam_management',
            ],
            [   
                'module_name'     => 'School Management',
                'module_slug'     => 'school_management',
            ],
            [   
                'module_name'     => 'Roles Management',
                'module_slug'     => 'roles_management',
            ],
            [   
                'module_name'     => 'User Activity',
                'module_slug'     => 'user_activity',
            ],
            [
                'module_name'     => 'Reports',
                'module_slug'     => 'reports',
            ],
            [
                'module_name'      => 'Setting',
                'module_slug'      => 'setting',
            ],
            [
                'module_name'     => 'Modules Management',
                'module_slug'     => 'modules_management',
            ],
            [
                'module_name'     => 'Result Management',
                'module_slug'     => 'result_management',
            ],
            [                
                'module_name'     => 'Profile Management',
                'module_slug'     => 'profile_management',
            ],
            [
                'module_name'     => 'Teacher Management',
                'module_slug'     => 'teacher_management',
            ],
            [
                'module_name'     => 'Grade Management',
                'module_slug'     => 'grade_management',
            ],
            [                
                'module_name'     => 'Subject Management',
                'module_slug'     => 'subject_management',
            ],
            [
                'module_name'     => 'Student Management',
                'module_slug'     => 'student_management',
            ],
            [
                'module_name' => 'Knowledge Tree',
                'module_slug' => 'knowledge_tree'
            ],
            [
                'module_name' => 'Node Management',
                'module_slug' => 'node_management'
            ],
            [
                'module_name'     => 'Assign Test Question',
                'module_slug'     => 'assign_test_question',
            ],
            [
                'module_name'     => 'Assign Test User',
                'module_slug'     => 'assign_test_user',
            ],
            [
                'module_name'     => 'Assign Test Group',
                'module_slug'     => 'assign_test_group',
            ],
            [
                'module_name' => 'Upload Documents',
                'module_slug' => 'upload_documents'
            ],
            [
                'module_name' => 'Test Template Management',
                'module_slug' => 'test_template_management'
            ],
            [
                'module_name'     => 'Attempt Exam',
                'module_slug'     => 'attempt_exam',
            ],
            [
                'module_name'     => 'Teacher Class And Subject Assign',
                'module_slug'     => 'teacher_class_and_subject_assign',
            ],
            [
                'module_name'     => 'Ai Calculate Difficulty',
                'module_slug'     => 'ai_calculate_difficulty',
            ],
            [
                'module_name'     => 'Pre Configure Difficulty',
                'module_slug'     => 'pre_configure_difficulty',
            ],
            [
                'module_name'     => 'Strands Management',
                'module_slug'     => 'strands_management',
            ],
            [
                'module_name'     => 'Learning Units Management',
                'module_slug'     => 'learning_units_management',
            ],
            [
                'module_name'     => 'Learning Objectives Management',
                'module_slug'     => 'learning_objectives_management',
            ],
            [
                'module_name'     => 'Global Configurations',
                'module_slug'     => 'global_configurations'
            ],
            [
                'module_name'     => 'Sub Admin Management',
                'module_slug'     => 'sub_admin_management'
            ],
            [   
                'module_name'     => 'My Classes',
                'module_slug'     => 'my_classes',
            ],
            [   
                'module_name'     => 'My Subjects',
                'module_slug'     => 'my_subjects',
            ],
            [   
                'module_name'     => 'My Teaching',
                'module_slug'     => 'my_teaching',
            ],
            [   
                'module_name'     => 'Self Learning',
                'module_slug'     => 'self_learning',
            ],
            [   
                'module_name'     => 'Assignment Or Test',
                'module_slug'     => 'assignment_or_test',
            ],
            [   
                'module_name'     => 'Progress Report',
                'module_slug'     => 'progress_report',
            ],
            [   
                'module_name'     => 'Peer Group',
                'module_slug'     => 'peer_group',
            ],
            [   
                'module_name'     => 'My Teachers',
                'module_slug'     => 'my_teachers',
            ],
            [   
                'module_name'     => 'My Calendar',
                'module_slug'     => 'my_calendar',
            ],
            [   
                'module_name'     => 'My Account',
                'module_slug'     => 'my_account',
            ],
            [   
                'module_name'     => 'Change Password',
                'module_slug'     => 'change_password',
            ],
            [   
                'module_name'     => 'Principal Management',
                'module_slug'     => 'principal_management',
            ],
            [   
                'module_name'     => 'Documents',
                'module_slug'     => 'documents',
            ],
            [   
                'module_name'     => 'Leaderboard',
                'module_slug'     => 'leaderboard',
            ],
            [
                'module_name'     => 'Assign Credit Points',
                'module_slug'     => 'assign_credit_points',
            ],
            [
                'module_name'     => 'Intelligent Tutor',
                'module_slug'     => 'intelligent_tutor',
            ],
            [
                'module_name'     => 'AI-Calibration',
                'module_slug'     => 'ai-calibration',
            ],
            [
                'module_name'     => 'Ordering Learning Units',
                'module_slug'     => 'ordering_learning_units',
            ],
            [
                'module_name'     => 'Ordering Learning Objectives',
                'module_slug'     => 'ordering_learning_objectives',
            ],
        ];

        foreach ($modules as $module) {
            $ModuleData = Modules::where(cn::MODULES_MODULE_SLUG_COL,$module['module_slug'])->first();
            if(isset($ModuleData) && !empty($ModuleData)){
                Modules::where(cn::MODULES_MODULE_SLUG_COL,$module['module_slug'])->update($module);
            }else{
                Modules::updateOrCreate($module);
            }
        }
    }
}
