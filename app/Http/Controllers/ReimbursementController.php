<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class ReimbursementController extends Controller
{
    public function index(Request $request){

		$data = [];

		$tmp = explode('R',$request->logid);

		if(isset($tmp[0])){
			$request->logid = $tmp[0];
		}

		if(isset($tmp[1])){
			$request->remid = $tmp[1];
		}

		$log  = DB::table('drm_meeting_logger')->where('user_id','=',Auth()->user()->id)		
			->whereNotNull('meeting_remarks')->where(DB::raw('date(end_datetime)'),'<=',DB::raw('date(now())'))->where(DB::raw('date(end_datetime)'),'>',DB::raw('DATE_ADD( now( ) , INTERVAL -1 MONTH )'))->orderby('start_datetime','desc');

		if(!empty($request->logid)){
			$log->where('id','=',$request->logid);
		}

		$data['logs'] = $log->get()->toArray();
		$data['log_id'] = $request->logid;
		
		$data['type'] = '';
		$data['amount'] = '';
		$data['location'] = '';
		$data['date'] = '';
		$data['description'] = '';
		$data['travel_type'] = '';
		$data['TransportType'] = '';
		$data['tolocation'] = '';
		$data['todate'] = '';
		$data['approx_km'] = '';

		if(count($data['logs']) == 1 && isset($data['logs'][0])){
			$data['date'] = date('Y-m-d',strtotime($data['logs'][0]->end_datetime));
			$data['description'] = $data['logs'][0]->meeting_remarks;
		}

		if(isset($request->logid) && isset($request->remid)){

			$reimbus  = DB::table('drm_reimbursement')->where('user_id','=',Auth()->user()->id);
			
			if(!empty($request->logid)){
				$reimbus->where('meeting_id','=',$request->logid);
			}

			if(!empty($request->remid)){
				$reimbus->where('id','=',$request->remid);
			}

			$reimbus = $reimbus->limit(1)->get()->toArray();
			
			if(isset($reimbus[0])){
				$data['type'] = $reimbus[0]->type;
				$data['amount'] = $reimbus[0]->amount;
				$data['location'] = $reimbus[0]->location;
				$data['date'] = $reimbus[0]->date;
				$data['description'] = $reimbus[0]->description;
				$data['travel_type'] = $reimbus[0]->travel_type;
				$data['TransportType'] = $reimbus[0]->TransportType;
				$data['tolocation'] = $reimbus[0]->tolocation;
				$data['todate'] = $reimbus[0]->todate;
				$data['approx_km'] = $reimbus[0]->approx_km;
			}
		}
		
		return view('reimbursement/view',$data);
	}

    public function add(Request $request){

		$segments = explode('R',request()->headers->get('referer'));

		$id = end($segments);
		
		if($request->resubmit != 'true'){
			if($request->type != 'Other'){
				$tmp = DB::table('drm_reimbursement')
					->where([
						'meeting_id' => $request->meeting_id,
						'type' => $request->type
					])->count();
					if(!empty($tmp)){
						return response()->json([
							'status' => 'success',
							'msg' => 'exist',
						]);
					}
			}elseif($request->type == 'Other' and is_numeric($id)){
				return response()->json([
					'status' => 'success',
					'msg' => 'exist',
				]);
			}
		}

		$error = [
			'meeting_id.required' => 'Please Select Meeting.',
			'type.required' => 'Please Select Reimbursement Type.',
			'amount.required' => 'Please fill amount',
			'amount.numeric' => 'Please enter a valid number for amount.',
			'amount.min' => 'Amount should be greater than 0.',
			'date.required' => 'Please Select Date.',
			'date.date_format' => 'Invalid Date format.',
			'file.required' => 'Please Select File.',
			'file.mimes' => 'Only jpeg, jpg, png, gif are supported.',
			'file.max' => 'Upload less than 5120kb file.',
			'description.required' => 'Please fill description.',
			'description.max' => 'Please fill less than 1000 characters.',
			'location.required' => 'Please enter location.',
			'travel_type.required' => 'Please Select Travel Type.',
			'TransportType.required' => 'Please Select Transport Type.',
			'tolocation.required' => 'Please enter to location.',
			'approx_km.required' => 'Please fill Distance',
			'approx_km.numeric' => 'Please enter a valid number for Distance.',
			'approx_km.min' => 'Distance should be greater than 0.',
			'todate.required' => 'Please Select to date.',
			'todate.date_format' => 'Invalid Date format.',
		];

		$validate = [
			'type' => 'required',
			'amount' => 'required|numeric|min:0',
			'date' => 'required|date|date_format:Y-m-d',
			'file' => 'mimes:jpeg,jpg,png,gif|required|max:5120',
			'description' => 'required|max:1000',
		];

		if(
			$request->type == 'Courier charges' or
			$request->type == 'Travelling reimbursement' or
			$request->type == 'Food reimbursement' or
			$request->type == 'Stay Expense' or
			$request->type == 'Halting Charges' or
			$request->type == 'Nism fee reimbursement' or
			$request->type == 'Other'
		){
			$validate['location'] = 'required';
		}

		if($request->type == 'Travelling reimbursement'){
			$validate['travel_type'] = 'required';
			$validate['TransportType'] = 'required';
			$validate['tolocation'] = 'required';
			$validate['approx_km'] = 'required|numeric|min:0';
		}

		if(
			$request->type == 'Stay Expense' or
			$request->type == 'Halting Charges'
		){
			$validate['todate'] = 'required|date|date_format:Y-m-d';
		}

		if($request->type != 'Other'){
			$validate['meeting_id'] = 'required';
		}

		$request->validate($validate,$error);

		$insert = $request->all();

		unset($insert['resubmit']);

		$employee_code = DB::table("users_details")
		->select("employee_code")
		->where('user_id','=',Auth()->user()->id)->first();

		$employee_code = $employee_code->employee_code;

		$file = $request->file('file');
		$insert['user_id'] = Auth()->user()->id;
		$insert['created_at'] = date('Y-m-d H:i:s');
		$insert['updated_at'] = date('Y-m-d H:i:s');
		$insert['em_code'] = $employee_code;

		if(!empty($file)){
			$destinationPath = 'uploads/'.Auth()->user()->id.'/';
			if(!is_dir('uploads')){
				mkdir('uploads');
			}
			if(!is_dir($destinationPath)){
				mkdir($destinationPath);
			}
			$file_name = rand(1000,9999).'R'.strtotime(date('Y-m-d h:i:s')).'.'.$file->extension();
			$file->move($destinationPath,$file_name);
			$insert['file'] = $destinationPath.$file_name;
		}else{
			unset($insert['file']);
		}
		
		$response = [
			'status' => 'failed',
			'msg' => 'Something went wrong.',
		];

		$output = [];

		$segments = explode('R',request()->headers->get('referer'));

		$id = end($segments);

		$output = DB::table('drm_reimbursement')->where([
			'id' => $id,
			'user_id' => Auth()->user()->id
		])->first();

		if(isset($output->status)){
			if($output->status == 1){
				return response()->json([
					'status' => 'success',
					'msg' => 'Reimbursement request is Approved.',
				]);
			}
			if($output->status == 2){
				return response()->json([
					'status' => 'success',
					'msg' => 'Reimbursement request is Rejected.',
				]);
			}
		}

		if(!empty($output->hrms_id)){
			return response()->json([
				'status' => 'success',
				'msg' => 'Can not update at the moment, reimbursement request is in process.',
			]);
		}

		DB::table('drm_reimbursement')->where([
			'id' => $id,
			'user_id' => Auth()->user()->id
		])->update(['updated_at' => date('Y-m-d H:i:s')]);
		
		$upsert_msg = 'Reimbursement added successfully.';

		if($request->type != 'Other'){
			$tmp = DB::table('drm_reimbursement')
				->where([
					'meeting_id' => $request->meeting_id,
					'type' => $request->type
				])->count();
			if(!empty($tmp)){
				unset($insert['created_at']);
				$upsert_msg = 'Reimbursement Updated successfully.';
			}
			$addorupdate = DB::table('drm_reimbursement')->updateOrInsert([
				'meeting_id' => $request->meeting_id,
				'type' => $request->type,
			],$insert);
		}elseif($request->type == 'Other' and is_numeric($id)){
			unset($insert['created_at']);
			$upsert_msg = 'Reimbursement Updated successfully.';
			$addorupdate = DB::table('drm_reimbursement')->updateOrInsert([
				'id' => $id,
				'user_id' => Auth()->user()->id
			],$insert);
		}else{
			$addorupdate = DB::table('drm_reimbursement')->insert($insert);
		}

		if($addorupdate){
			$response = [
				'status' => 'success',
				'msg' => $upsert_msg,
			];
		}

		return response()->json($response);
	}

	public function list(Request $request){

		$user_id = Auth()->user()->id;

		$data = DB::table('drm_reimbursement')
				->select('hrms_id','id','em_code','meeting_id','type','amount','location','tolocation','approx_km','date','todate','file','description','TransportType','travel_type',
				DB::raw('(case status
				when 0 then "Pending"
				when 1 then "Approved"
				when 2 then "Rejected"
				end) as "status"'),
				'remark')->where('user_id','=',$user_id);
		
		if(is_array($request->order) && !empty($request->order[0]['column'])){
			$column = $request->order[0]['column'];
			$direction = $request->order[0]['dir'];
			$data->orderby($request->columns[$column]['data'],$direction);
		}else{
			$data->orderby('updated_at','desc');
		}

		//$arr = $request->search['value'];

		// $data->where(function($query) use ($request,$data){
		// 	if(isset($request->search['value'])){
		// 		foreach($request->columns as $column){
		// 			$data->orwhere(DB::raw('LOWER('.$column['name'].')'),'LIKE',"%{$request->search['value']}%");
		// 		}
		// 	}
		// });
		
		// if(isset($request->search['value'])){
		// 	foreach($request->columns as $column){
		// 		$data->orwhere($column['name'],'LIKE',"%{$request->search['value']}%");
		// 	}
		// }

		return Datatables::of($data->get())
				->addIndexColumn()
				->addColumn('type', function($row){
					return '<a data-text="'.$row->type.'" href="/reimbursement/'.$row->meeting_id.'R'.$row->id.'" >'.$row->type.'</a>';
				})
				->addColumn('file', function($row){
					return '<a href="javascript:void();" onclick="showimage(\'/'.$row->file.'\');"><i class="icons view-icon" title="View file" alt="View file"></i></a>';
				})
				->addColumn('description', function($row){
					$str = htmlspecialchars($row->description);
					if(strlen($row->description) > 7){
						$str = substr(htmlspecialchars($row->description),0,7).'...';
					}
					return '<a href="javascript:void();" style="text-decoration: none;" onclick="showtext(\''.strrep(htmlspecialchars($row->description)).'\');">'.''.$str.'</a>';
				})
				->addColumn('remark', function($row){
					$str = htmlspecialchars($row->remark);
					if(strlen($row->remark) > 7){
						$str = substr(htmlspecialchars($row->remark),0,7).'...';
					}
					return '<a href="javascript:void();" style="text-decoration: none;" onclick="showtext(\''.strrep(htmlspecialchars($row->remark)).'\');">'.$str.'</a>';
				})
				->addColumn('status', function($row){

					$class = [
						'Pending' => 'info',
						'Approved' => 'success',
						'Rejected' => 'danger',
					];

					$status = $row->status;

					if(!empty($row->hrms_id) && $row->status == 'Pending'){
						$status = 'In Process';
					}

					return '<div class="alert alert-'.$class[$row->status].' reimb_center" style="padding:5px;">'.$status.'</div>';
				})
				->rawColumns(['status','type','file','description','remark'])
				->make(true);
	}

	public function expense_list(Request $request){

		$user_id = Auth()->user()->id;

		$data = DB::table('users_details as ud')
		->join('drm_reimbursement as r', function ($join) {
			$join->on('ud.user_id', '=', 'r.user_id')
				 ->where('ud.reporting_to', '=', Auth()->user()->id);
		})
		->join('users as u', 'u.id', '=', 'r.user_id')
		->select('r.id','u.name',DB::raw('u.id as userid'),'r.em_code','r.meeting_id','r.type','r.amount','r.location','r.tolocation','r.approx_km','r.date','r.todate','r.file','r.description','r.TransportType','r.travel_type','r.status','r.remark','r.hrms_id')->orderby('r.updated_at','desc');

		$data = $data->get();

		return Datatables::of($data)
				->addIndexColumn()
				->addColumn('status', function($row){
					
					$status = [
						0 => 'Pending',
						1 => 'Approved',
						2 => 'Rejected',
					];

					$class = [
						0 => 'info',
						1 => 'success',
						2 => 'danger',
					];
					
					if($row->status){ 
						// To Display Response from HRMS if Approved or Rejected
						return '<div class="alert status-alert alert-'.$class[$row->status].'" style="padding:5px;">'.$status[$row->status].'</div>';
					}elseif($row->hrms_id){
						// To Display Refresh Button if Request is send to HRMS
						return '<button type="submit" data-id="'.$row->id.'"  data-hrms_id="'.$row->hrms_id.'" onclick="get_status(this);" class="btn btn-primary" style="width: 100px;text-align: center;" >Refresh</button>';
					}

					$html = '<select class="form-control status_select" data-id="'.$row->id.'" data-uid="'.$row->userid.'" onchange="update_status(this)">';
					$html .= '<option value="0">Pending</option>';
					$html .= '<option value="1">Approve</option>';
					$html .= '<option value="2">Reject</option>';
					$html .= '</select>';
					return $html;
				})
				->addColumn('file', function($row){
					   return '<a href="javascript:void();" onclick="showimage(\'/'.$row->file.'\');"><i class="icons view-icon" title="View file" alt="View file"></i></a>';
				})
				->addColumn('description', function($row){
						$str = $row->description;
					   if(strlen($row->description) > 7){
							$str = substr(htmlspecialchars($row->description),0,7).'...';
					   }
					   return '<a href="javascript:void();" style="text-decoration: none;" onclick="showtext(\''.strrep(htmlspecialchars($row->description)).'\');">'.$str.'</a>';
				})
				->addColumn('remark', function($row){
						$str = $row->remark;
						if(strlen($row->remark) > 7){
							$str = substr(htmlspecialchars($row->remark),0,7).'...';
						}
						// approval request sent to hrms.
						if(!empty($row->hrms_id) and $row->status == 0){
							return '';
						}
						// Rejected from panel and remark is empty
						if(empty($str) && empty($row->hrms_id) && $row->status == 2){
							$str = '<button type="submit" class="btn btn-primary" >Add Remark</button>';
						}
						// Rejected from panel and remark is not empty
						if(empty($row->hrms_id) and $row->status != 0){
							return '<a href="javascript:void(0);" style="text-decoration: none;"onclick="addRemark(this,true);" data-text="'.strrep(htmlspecialchars($row->remark)).'" data-id="'.$row->id.'" data-uid="'.$row->userid.'">'.$str.'</a>';
						}
						// if head approved and rejected, hrms status is not pending
						if(!empty($row->hrms_id) and $row->status != 0){
							return '<a href="javascript:void(0);" style="text-decoration: none;"onclick="addRemark(this,false);" data-text="'.strrep(htmlspecialchars($row->remark)).'" data-id="'.$row->id.'" data-uid="'.$row->userid.'">'.$str.'</a>';
						}
						return '<a href="javascript:void(0);" style="text-decoration: none;"onclick="addRemark(this,false);" data-text="'.strrep(htmlspecialchars($row->remark)).'" data-id="'.$row->id.'" data-uid="'.$row->userid.'">'.$str.'</a>';
				})
				->rawColumns(['status','file','description','remark'])
				->make(true);
	}

	public function addRemark(Request $request){
		
		$users = DB::table("users_details")
		->where('reporting_to', '=', Auth()->user()->id)
		->where('user_id','=',$request->userid)->get()->toArray();

		if(count($users) >= 1){
			$result = DB::table('drm_reimbursement')
				->where('user_id', $request->userid)
				->where('id', $request->id)
				->update(array(
					'remark' => $request->remark,
					'updated_at' => date('Y-m-d H:i:s')
				));
			if(!empty($result)){
				$response = [
					'msg' => 'Remark Added Successfully.',
					'status' => 'success'
				];
				return response()->json($response);
			}
		}

		$response = [
			'msg' => 'UnAuthorized access.',
			'status' => 'error'
		];

		return response()->json($response);
	}

	public function status(Request $request){
		
		$users = DB::table("users_details")
		->where('reporting_to', '=', Auth()->user()->id)
		->where('user_id','=',$request->userid)->get()->toArray();

		if(count($users) >= 1){

			if($request->status == 2){
				$result = DB::table('drm_reimbursement')
					->where('user_id', $request->userid)
					->where('id', $request->id)
					->update(array(
						'status' => $request->status,
						'updated_at' => date('Y-m-d H:i:s')
					));
					
				if(!empty($result)){
					$response = [
						'msg' => 'Status updated Successfully.',
						'status' => 'success'
					];
					return response()->json($response);
				}
			}

			$url = getSettingsTableValue('HRMS_CURL_URL');

			$result = DB::table('drm_reimbursement')
				->where('user_id', $request->userid)
				->where('id', $request->id)->first();

			$reimbursementTypeCheck = array('Mobile Internet Expenses','Courier charges','Food reimbursement','Nism fee reimbursement','Other','Travelling reimbursement','Stay Expense','Halting Charges');

			if(!in_array($result->type, $reimbursementTypeCheck)){
				return response()->json([
					'msg' => 'Reimbursement Type doesnt exist.',
					'status' => 'fail'
				]);
			}

			$data = [
				'em_code'                    => trim($result->em_code),
				'from_date_expense_occurred' => trim($result->date),
				'amount'                     => trim($result->amount),
				'reimbursement_type'         => trim($result->type),
				'location'                   => trim($result->location),
				'location_to'                => trim($result->tolocation),
				'km'                         => trim($result->approx_km),
				'transport_type'             => trim($result->TransportType),
				'travel_type'                => trim($result->travel_type),
				'description'                => trim($result->description),
				'from_date'                  => trim($result->date),
				'date'                       => trim($result->todate),
				'files'                      => url(trim($result->file)),
			];

			$output = json_decode(get_curl_call($url,$data),1);
			
			if(!isset($output['status'])){
				return response()->json([
					'msg' => 'something went wrong',
					'status' => 'fail'
				]);
			}

			if($output['status'] == 'fail'){
				return response()->json([
					'msg' => $output['response'],
					'status' => 'fail'
				]);
			}

			$result = DB::table('drm_reimbursement')
				->where('user_id', $request->userid)
				->where('id', $request->id)
				->update(array(
					'hrms_id' => $output['id_return'],
					'updated_at' => date('Y-m-d H:i:s')
				));
			
			if(!empty($result)){
				$response = [
					'msg' => 'Status updated Successfully.',
					'status' => 'success'
				];
				return response()->json($response);
			}
		}

		$response = [
			'msg' => 'UnAuthorized access.',
			'status' => 'error'
		];

		return response()->json($response);
	}

	public function getStatus(Request $request){

		$url = getSettingsTableValue('HRMS_STATUS_CURL_URL');

		$data = [];
		$data['id'] = $request->hrms_id;

		$output = json_decode(get_curl_call($url,$data),1);

		if(isset($output['status'])){
			if($output['status'] == 'fail'){
				return response()->json([
					'msg' => $output['response'],
					'status' => 'fail'
				]);
			}
		}else{
			return response()->json([
				'msg' => "Something went wrong.",
				'status' => 'fail'
			]);
		}

		$output = array_map(function($item){
			return [
				'remark' => $item['remark'],
				'hrms_id' => $item['id'],
				'status' => $item['status'],
			];
		},$output['response']);

		try {
			foreach($output as $item){
				if($item['status'] != 0){
					DB::table('drm_reimbursement')
					->where('hrms_id','=',$item['hrms_id'])
					->update([
						'status' => $item['status'],
						'remark' => $item['remark'],
						'updated_at' => date('Y-m-d H:i:s')
					]);
				}
			}
			if($item['status'] == 0){
				return response()->json([
					'msg' => 'Not Approved yet.',
					'status' => 'success'
				]);
			}
			if($item['status'] == 1){
				return response()->json([
					'msg' => 'Aproved, Status Updated as Approved.',
					'status' => 'success'
				]);
			}
			if($item['status'] == 2){
				return response()->json([
					'msg' => 'Rejected, Status Updated as Rejected.',
					'status' => 'success'
				]);
			}
		} catch(\Illuminate\Database\QueryException $ex){ 
			return response()->json([
				'msg' => 'Refresh UnComplete.',
				'status' => 'fail'
			]);
		}

		return response()->json([
			'msg' => 'Refresh Completed.',
			'status' => 'success'
		]);
	}

}
