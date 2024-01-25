<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Exports\ArrayRecordsExport;

class UserHierarchy extends Controller
{
    protected $data_table_headings, $rankmf_stage_of_prospect, $samcomf_stage_of_prospect;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');

        $this->data_table_headings = array(
            'name' => array('label' => 'Name'),
            'positionName' => array('label' => 'Designation'),
            'status' => array('label' => 'Status')
        );

        // retrieving logged in user role and permission details
        $this->middleware(function ($request, $next) {
            $this->logged_in_user_roles_and_permissions = session('logged_in_user_roles_and_permissions');
            $this->logged_in_user_id = session('logged_in_user_id');
            $this->flag_have_all_permissions = false;
            if(isset($this->logged_in_user_roles_and_permissions['role_details']) && isset($this->logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) && (intval($this->logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) == 1)){
                $this->flag_have_all_permissions = true;
            }
            $this->flag_show_all_arn_data = false;
            if(isset($this->logged_in_user_roles_and_permissions['role_details']) && isset($this->logged_in_user_roles_and_permissions['role_details']['show_all_arn_data']) && (intval($this->logged_in_user_roles_and_permissions['role_details']['show_all_arn_data']) == 1)){
                $this->flag_show_all_arn_data = true;
            }
            return $next($request);
        });
    }


    //Get User Detail Hierarchy
    public function getUserDetailHierarchy(Request $request){
        $whereCondition = '';
        if(!empty($this->logged_in_user_roles_and_permissions['role_details']['label']) && $this->logged_in_user_roles_and_permissions['role_details']['label'] != 'Admin'){
            $whereCondition = " AND ud1.reporting_to = '".$this->logged_in_user_id."'";
        }

        $sqlQuery = "SELECT u1.name, ud1.designation as positionName, u1.status as parentStatus, IFNULL(ud1.reporting_to, '') as parentId, u1.id, (SELECT COUNT(`id`) FROM `users_details` WHERE `reporting_to` = u1.id AND designation IS NOT NULL AND designation != '') AS userCount, ud1.status as status
        FROM users u1 
        JOIN users_details ud1 on ud1.user_id = u1.id
        WHERE u1.is_drm_user = 1 AND ud1.designation IS NOT NULL AND ud1.designation != '' $whereCondition
        HAVING  parentId > 0 OR userCount > 0
        ORDER BY ud1.reporting_to;";

        $userHierarchyRecords = DB::select($sqlQuery);

        $userHierarchyDetail = [];
        $loginUserHierarchyData = [];
        $inactiveUsersList = [];

        if(!empty($userHierarchyRecords)){
            foreach ($userHierarchyRecords as $key => $userHierarchy) {
                if(($userHierarchy->status == '0' || $userHierarchy->parentStatus == '0') && $userHierarchy->userCount > 0){
                    $inactiveUsersList[] = [
                        'name' => $userHierarchy->name,
                        'positionName' => $userHierarchy->positionName,
                        'status' => ($userHierarchy->status == '1' ? 'Active' : 'Inactive'),
                        'id' => (string) $userHierarchy->id,
                        'parentId' => (string) $userHierarchy->parentId,
                        'parentStatus' => (string) ($userHierarchy->parentStatus == '1' ? 'Active' : 'Inactive'),
                        'userCount' => (string) $userHierarchy->userCount,
                        'size' => ''
                    ];
                }
            }
        }
        $data['data_table_headings'] = $this->data_table_headings;
        $data['inactiveUsersList'] = $inactiveUsersList;
        //$data['userHierarchyJsonURL'] = env('APP_URL').'/user-hierarchy-json';
        $data['userHierarchyJsonURL'] = env('APP_URL').'/user-hierarchy-tree-json';

        return view('userhierarchy/user_hierarchy2')->with($data);
    }

    public function userHierarchyDetailJson(){

        $whereCondition = '';
        if(!empty($this->logged_in_user_roles_and_permissions['role_details']['label']) && $this->logged_in_user_roles_and_permissions['role_details']['label'] != 'Admin'){
            $whereCondition = " AND ud1.reporting_to = '".$this->logged_in_user_id."'";
        }

        $sqlQuery = "SELECT u1.name, ud1.designation as positionName, u1.status as parentStatus, IFNULL(ud1.reporting_to, '') as parentId, u1.id, (SELECT COUNT(`id`) FROM `users_details` WHERE `reporting_to` = u1.id AND designation IS NOT NULL AND designation != '') AS userCount, ud1.status as status
        FROM users u1 
        JOIN users_details ud1 on ud1.user_id = u1.id
        WHERE u1.is_drm_user = 1 AND ud1.designation IS NOT NULL AND ud1.designation != '' $whereCondition
        HAVING  parentId > 0 OR userCount > 0
        ORDER BY ud1.reporting_to;";

        $userHierarchyRecords = DB::select($sqlQuery);

        $userHierarchyDetail = [];
        $loginUserHierarchyData = [];
        $inactiveUsersList = [];

        if(!empty($userHierarchyRecords)){
            foreach ($userHierarchyRecords as $key => $userHierarchy) {

                $userHierarchyDetail[] = [
                    'name' => $userHierarchy->name,
                    'positionName' => $userHierarchy->positionName,
                    'status' => ($userHierarchy->status == '1' ? 'Active' : 'Inactive'),
                    'id' => (string) $userHierarchy->id,
                    'parentId' => (string) $userHierarchy->parentId,
                    'parentStatus' => (string) ($userHierarchy->parentStatus == '1' ? 'Active' : 'Inactive'),
                    'userCount' => (string) $userHierarchy->userCount,
                    'size' => ''
                ];
            }

            $userHierarchyJson = json_encode($userHierarchyDetail);
            $loginUserHierarchyJson = json_encode($loginUserHierarchyData);

            $data['userHierarchyJson'] = $userHierarchyJson;
            $data['loginUserHierarchyJson'] = $loginUserHierarchyJson;

            echo $userHierarchyJson;
        }        
    }

    public function userHierarchyDetailJsonNew(){
        
        $whereCondition = '';
        if(!empty($this->logged_in_user_roles_and_permissions['role_details']['label']) && $this->logged_in_user_roles_and_permissions['role_details']['label'] != 'Admin'){
            $whereCondition = " AND ud1.reporting_to = '".$this->logged_in_user_id."'";
        }

        $sqlQuery = "SELECT u1.name, ud1.designation as positionName, IFNULL(ud1.reporting_to, '') as parentId, u1.id, (SELECT COUNT(`id`) FROM `users_details` WHERE `reporting_to` = u1.id AND designation IS NOT NULL AND designation != '') AS userCount, ud1.status as status
        FROM users u1 
        JOIN users_details ud1 on ud1.user_id = u1.id
        WHERE u1.is_drm_user = 1 AND ud1.designation IS NOT NULL AND ud1.designation != '' $whereCondition
        HAVING  parentId > 0 OR userCount > 0
        ORDER BY ud1.reporting_to;";

        $userHierarchyRecords = DB::select($sqlQuery);

        $userHierarchyDetail = [];
        $loginUserHierarchyData = [];
        $inactiveUsersList = [];

        $isLoggedUser = false;

        if(!empty($userHierarchyRecords)){
            
            foreach ($userHierarchyRecords as $key => $userHierarchy) {

                if(!empty($this->logged_in_user_id) && ($userHierarchy->id == $this->logged_in_user_id)){
                    $isLoggedUser = true;
                }

                $userHierarchyDetail[] = [
                    'name' => $userHierarchy->name,
                    'positionName' => $userHierarchy->positionName,
                    'status' => ($userHierarchy->status == '1' ? 'Active' : 'Inactive'),
                    'id' => (string) $userHierarchy->id,
                    'parent_id' => (!empty($userHierarchy->parentId) ? $userHierarchy->parentId : '0'),
                    'isLoggedUser' => $isLoggedUser,
                    'imageUrl' => 'https://raw.githubusercontent.com/bumbeishvili/Assets/master/Projects/D3/Organization%20Chart/general.jpg'
                 ];
            }
        }
        $userHierarchyTreeData = $this->buildUserHierarchyTree($userHierarchyDetail);
        
        $userHierarchyJson = json_encode($userHierarchyTreeData);
        echo $userHierarchyJson;
    }

    public function buildUserHierarchyTree($array,$id_key = 'id',$parent_key = 'parent_id'){
        $res = [];
        foreach($array as $y){
            $array_with_id[$y[$id_key]] = $y;
        }
        
        foreach($array_with_id as $key => $element){

            if(!isset($array_with_id[$key]['childrenCnt'])){
                $array_with_id[$key]['childrenCnt'] = (int) '0';
            }

            if(isset($array_with_id[$element[$parent_key]]['children']) && !empty($array_with_id[$element[$parent_key]]['children'])){
                $array_with_id[$element[$parent_key]]['childrenCnt'] = (int) count($array_with_id[$element[$parent_key]]['children']);
            }
            else{
                $array_with_id[$element[$parent_key]]['childrenCnt'] = (int)'0';
            }

            if($element[$parent_key]){
                if(!empty($array_with_id[$element[$parent_key]]['children'])){
                    $array_with_id[$element[$parent_key]]['childrenCnt'] = count($array_with_id[$element[$parent_key]]['children']);
                }
                
                $array_with_id[$element[$parent_key]]['children'][] = &$array_with_id[$key];
            }else{
                $res = &$array_with_id[$key];
            }

            if(isset($array_with_id[$element[$parent_key]]['children']) && !empty($array_with_id[$element[$parent_key]]['children'])){
                $array_with_id[$element[$parent_key]]['childrenCnt'] = (int) count($array_with_id[$element[$parent_key]]['children']);
            }
            else{
                $array_with_id[$element[$parent_key]]['childrenCnt'] = (int) '0';
            }
        }
        return $res;
    }
}