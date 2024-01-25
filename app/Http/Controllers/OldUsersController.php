<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class OldUsersController extends Controller
{
	public function index(Request $request, Builder $builder){

		$data = [];

		$data['html'] = $builder->ajax([
			'url' => url()->current(),
			'type' => 'POST',
		])->columns([
			Column::make('employee_code')->title('Employee Code')->width(200),
			Column::make('name')->title('Name')->width(200),
			Column::make('email')->title('E-Mail')->width(200),
			Column::make('designation')->title('Designation')->width(200),
			Column::make('mobile_number')->title('Mobile No.')->width(200),
			Column::make('reporting_to')->title('Reporting To')->width(200),
			Column::make('city')->title('City')->width(200),
			Column::make('state')->title('State')->width(200),
		])->parameters([
			'paging' => true,
			'searching' => true,
			'info' => true,
			'searchDelay' => 350,
			'scrollX' => true,
			'language' => [
				'paginate' => [
					'previous' => '<',
					'next' => '>',
				]
			]
		]);

		return view('old_users/view',$data);
	}

    public function old(Request $request){
		
		$data = DB::table('users as u')
			->join('users_details as ud','u.id', '=', 'ud.user_id')
			->leftjoin('users as rt','rt.id', '=', 'ud.reporting_to')
			->where('ud.is_Old','=',1)
			->select([
				'ud.employee_code',
				'u.name',
				'u.email',
				'ud.designation',
				'ud.mobile_number',
				'rt.name as reporting_to',
				'ud.city',
				'ud.state',
			]);

		$data = Datatables::of($data);

		$columns = [
			'employee_code' => 'ud.employee_code',
			'name' => 'u.name',
			'email' => 'u.email',
			'designation' => 'ud.designation',
			'mobile_number' => 'ud.mobile_number',
			'reporting_to' => 'rt.name',
			'city' => 'ud.city',
			'state' => 'ud.state',
		];

		if(!empty($request['search']['value'])){
			$keyword = $request['search']['value'];
			foreach($columns as $key => $column){
				$data->filterColumn($key, function($query, $keyword) use ($column){
					$sql = "LOWER($column) like ?";
					$query->whereRaw($sql, ["%{$keyword}%"]);
				});
			}
		}
		
		return $data->toJson();
	}

}
