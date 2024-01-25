<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class RolemasterModel extends Model
{
    use HasFactory;
    public static function getRolesList($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array

        $flag_refresh_datatable = false;    // decides whether to just refresh datatable or complete page
        $output_arr = array();              // stores datatable required JSON output values
        if(isset($load_datatable) && is_numeric($load_datatable) && ($load_datatable == 1)){
            $flag_refresh_datatable = true;
        }

        $flag_export_data = false;          // decides whether request came for exporting the data or not
        if(isset($export_data) && !empty($export_data) && ($export_data == 1)){
            $flag_export_data = true;
        }

        if($flag_export_data){
            if(isset($columns) && !empty($columns) && json_decode($columns) !== FALSE){
                $columns = json_decode($columns, true);     // json_decode with parameter TRUE returns data in an array format
            }
            else{  $columns = array();  }
        }

        if(!isset($start) || empty($start) || !is_numeric($start)){
            $start = 0;
        }
        $start = intval($start);    // offset of records to be shown

        if(!isset($length) || empty($length) || !is_numeric($length)){
            $length = 10;        // default records to be shown on one page
        }
        $length = intval($length);

        $where_conditions = array();
        // $columns variable have list of all Table Headings/ Column names associated against the datatable
        if(isset($columns) && is_array($columns) && count($columns) > 0){
            foreach($columns as $key => $value){
                // data key have field names for whom data needs to be searched
                if(isset($value['search']['value'])){
                    $value['search']['value'] = trim($value['search']['value']);        // removing leading & trailing spaces from the searched value
                }

                switch($value['data']){
                    case 'created_at':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('roles.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('roles.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('roles.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('roles.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'roles.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'label':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                        }
                        break;
                    default:
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                        }
                }
            }
            unset($key, $value);
        }

        $order_by_clause = '';
        // $order is the POST variable sent by Datatable plugin. We are taking on single column ordering that's why considering $order[0] element only.
        if(isset($order[0]['column']) && isset($order[0]['dir']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data'])){
            // $columns variable have list of all Table Headings/ Column names associated against the datatable
            switch($columns[$order[0]['column']]['data']){
                default:
                    $order_by_clause = $columns[$order[0]['column']]['data'];
            }
            $order_by_clause .= ' ' .$order[0]['dir'];
        }
        else{
            $order_by_clause = 'roles.label ASC';
        }

        $records = DB::table('role_master AS roles')
                    ->select('roles.id', 'roles.label', 'roles.status',
                             (!$flag_export_data?'roles.created_at':DB::raw('DATE_FORMAT(roles.created_at, "%d/%m/%Y") AS created_at')), 'roles.have_all_permissions', 'roles.show_all_arn_data');
        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }

        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                $no_of_records = $records->where($where_conditions)->count();
            }
            catch(Exception $e){
            }

            if(isset($start) && is_numeric($start)){
                $records = $records->offset($start);
            }

            if(isset($length) && is_numeric($length)){
                $records = $records->limit($length);
            }
        }

        // checking whether permission entries available against a role needs to be retrieved or not
        $arr_role_permissions = array();
        if(isset($include_role_permissions) && $include_role_permissions){
            $permission_records = DB::table('role_master AS roles')
                                    ->join('role_permissions AS permissions', 'permissions.role_id', '=', 'roles.id')
                                    ->select('roles.id AS role_id', 'roles.label', 'roles.status AS role_status', 'permissions.page_url', 'permissions.status AS permission_status');
            // if flag include_status_check is not set or it's available and TRUE then only include status field conditions
            if(!isset($include_status_check) || (isset($include_status_check) && $include_status_check)){
                $permission_records = $permission_records->where('permissions.status', '=', 1)->where('roles.status', '=', 1);
            }
            if(count($where_conditions) > 0){
                $permission_records = $permission_records->where($where_conditions);
            }
            $permission_records = $permission_records->get();

            if(!$permission_records->isEmpty()){
                foreach($permission_records->toArray() as $key => $value){
                    // chaging the keys for better data retrieval
                    if(!isset($arr_role_permissions['role_'. $value->role_id])){
                        $arr_role_permissions['role_'. $value->role_id] = array('active' => array(), 'inactive' => array());
                    }

                    // preparing only ACTIVE permissions list
                    if(intval($value->permission_status) == 1){
                        $arr_role_permissions['role_'. $value->role_id]['active'][] = (array) $value;
                    }

                    // preparing only INACTIVE permissions list
                    if(intval($value->permission_status) == 0){
                        $arr_role_permissions['role_'. $value->role_id]['inactive'][] = (array) $value;
                    }
                }
                unset($key, $value);
            }
            unset($permission_records);
        }

        try{
            $records = $records->orderByRaw($order_by_clause)->get();
            if(!$records->isEmpty()){
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        $value->action = '<a href="'. env('APP_URL') . '/roles/addedit/'. $value->id .'"><i class="icons edit-icon" title="Edit Role" alt="Edit Role"></i></a>';
                    }

                    // assigning label to current partner a readable status i.e. Created/Activated etc.
                    if(isset($value->status) && (intval($value->status) == 1)){
                        $value->status = 'Active';
                    }
                    else{
                        $value->status = 'Inactive';
                    }

                    // if permission needs to be retrieved against a role then adding those details in roles array
                    if(isset($include_role_permissions) && $include_role_permissions && is_array($arr_role_permissions)){
                        $value->permissions = array();
                        if(isset($arr_role_permissions['role_'. $value->id])){
                            $value->permissions = $arr_role_permissions['role_'. $value->id];
                        }
                    }
                }
                unset($key, $value);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $order_by_clause, $arr_role_permissions);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function insertRoleData($inserting_data = array(), $arr_permissions = array()){
        $err_flag = 0;          // err_flag is 0 means no error
        $err_msg = array();     // err_msg stores list of errors found during execution
        $role_id = 0;           // stores the id which got inserted

        if(!is_array($arr_permissions) || count($arr_permissions) == 0){
            $arr_permissions = array();
        }

        // inserting records into MySQL table: role_master
        if(is_array($inserting_data) && count($inserting_data) > 0){
            try{
                $role_id = DB::table('role_master')->insertGetId($inserting_data);

                // preparing list of permissions which needs to be inserted
                $permission_records = array();
                array_walk($arr_permissions, function($_value, $_key, $_user_data) use($role_id){
                    if(in_array($_value, $_user_data[1]) === FALSE){
                        $_user_data[0][] = array('role_id' => $role_id, 'page_url' => $_value);
                    }
                }, [&$permission_records, array()]);

                if(count($permission_records) > 0){
                    $retrieve_data = self::insertRolePermissions($permission_records);
                    if($retrieve_data['err_flag'] == 1){
                        $err_flag = 1;
                        $err_msg = array_merge($err_msg, $retrieve_data['err_msg']);
                    }
                    unset($retrieve_data);
                }
            }
            catch(Exception | \Illuminate\Database\QueryException $e){
                $err_flag = 1;
                $err_msg[] = $e->getMessage();
            }
        }
        else{
            $err_flag = 1;
            $err_msg[] = 'Inserting data not found';
        }
        return array('err_flag' => $err_flag, 'err_msg' => $err_msg, 'role_id' => $role_id);
    }

    public static function updateRoleData($updating_data = array(), $role_id = 0, $arr_permissions = array()){
        $err_flag = 0;          // err_flag is 0 means no error
        $err_msg = array();     // err_msg stores list of errors found during execution

        $known_record_data = array();
        if(!is_array($arr_permissions) || count($arr_permissions) == 0){
            $arr_permissions = array();
        }

        if(!empty($role_id) && is_numeric($role_id)){
            // checking is passed input parameter $role_id is a valid DB record not
            $retrieve_data = self::getRolesList(array('columns' => array(
                                                                        array('data' => 'roles.id',
                                                                              'search' => array('value' => $role_id)
                                                                            )
                                                                    ),
                                                     'include_role_permissions' => true,
                                                     'include_status_check' => false
                                                    )
                                            );
            if(isset($retrieve_data['records']) && !$retrieve_data['records']->isEmpty() && isset($retrieve_data['records'][0]) && is_object($retrieve_data['records'][0]) && get_object_vars($retrieve_data['records'][0]) > 0){
                $known_record_data = (array) $retrieve_data['records'][0];
                $known_record_data['permissions'] = array_merge((isset($known_record_data['permissions']['active'])?array_column($known_record_data['permissions']['active'], 'page_url'):array()), (isset($known_record_data['permissions']['inactive'])?array_column($known_record_data['permissions']['inactive'], 'page_url'):array()));
            }
            unset($retrieve_data);

            if(is_array($known_record_data) && count($known_record_data) > 0 && is_array($updating_data) && count($updating_data) > 0){
                try{
                    // updating records from MySQL table: role_master
                    DB::table('role_master')
                        ->where('id', $role_id)
                        ->update($updating_data);

                    $permission_records = array();
                    // a) Updating permissions as INACTIVE which were present earlier and not available in currently selected permission records and were ACTIVE.
                    DB::table('role_permissions')
                        ->where('role_id', $role_id)
                        ->whereNotIn('page_url', $arr_permissions)
                        ->update(array('status' => 0));

                    // b) Updating permissions as ACTIVE which were present earlier and were marked as INACTIVE
                    DB::table('role_permissions')
                        ->where('role_id', $role_id)
                        ->whereIn('page_url', $arr_permissions)
                        ->update(array('status' => 1));

                    // preparing list of permissions which needs to be inserted
                    array_walk($arr_permissions, function($_value, $_key, $_user_data) use($role_id){
                        if(in_array($_value, $_user_data[1]) === FALSE){
                            $_user_data[0][] = array('role_id' => $role_id, 'page_url' => $_value);
                        }
                    }, [&$permission_records, ($known_record_data['permissions']??array())]);

                    if(count($permission_records) > 0){
                        $retrieve_data = self::insertRolePermissions($permission_records);
                        if($retrieve_data['err_flag'] == 1){
                            $err_flag = 1;
                            $err_msg = array_merge($err_msg, $retrieve_data['err_msg']);
                        }
                        unset($retrieve_data);
                    }
                }
                catch(Exception | \Illuminate\Database\QueryException $e){
                    $err_flag = 1;
                    $err_msg[] = $e->getMessage();
                }
            }
            else{
                // coming here if either updating_data is not found or either role details not found in DB
                $err_flag = 1;
                $err_msg[] = 'Role id details not found';
            }
        }
        else{
            // coming here if entered role id is either not available or non numeric
            $err_flag = 1;
            $err_msg[] = 'Role id not found';
        }
        return array('err_flag' => $err_flag, 'err_msg' => $err_msg, 'role_id' => $role_id);
    }

    public static function insertRolePermissions($arr_permissions = array()){
        $err_flag = 0;          // err_flag is 0 means no error
        $err_msg = array();     // err_msg stores list of errors found during execution
        $return_id = 0;         // stores the id which got inserted

        if(is_array($arr_permissions) && count($arr_permissions) > 0){
            $return_id = DB::table('role_permissions')->insert($arr_permissions);
        }
        else{
            $err_flag = 1;
            $err_msg[] = 'Permission details not found';
        }
        return array('err_flag' => $err_flag, 'err_msg' => $err_msg, 'return_id' => $return_id);
    }
}
