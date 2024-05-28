<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Redirect, Validator, Hash, Response, Session, DB;
use App\Models\User;
use App\Models\Entry;
use App\Models\CloakRoom;


class ShiftController extends Controller {
	
	public function index(){
		return view('admin.shift.index', [
            "sidebar" => "shift",
            "subsidebar" => "shift",
        ]);
		
	}

	public function init(Request $request){

		$input_date = $request->input_date;
		$user_id = $request->has('user_id')?$request->user_id:0;
		$users = DB::table('users')->select('id','name')->get();

		$current_shift = Entry::checkShift();
		$cloak_data = CloakRoom::totalShiftData($input_date,$user_id);
	
		$data['cloak_data'] = $cloak_data;
	
		$data['total_shift_upi'] = $cloak_data['total_shift_upi'];
        $data['total_shift_cash'] = $cloak_data['total_shift_cash'];
        $data['total_collection'] = $cloak_data['total_collection'];

        $data['last_hour_upi_total'] = $cloak_data['last_hour_upi_total'];
        $data['last_hour_cash_total'] = $cloak_data['last_hour_cash_total'];
        $data['last_hour_total'] = $cloak_data['last_hour_total'];
        
        $data['check_shift'] = $current_shift;


		$data['success'] = true;
		$data['users'] = $users;
		return Response::json($data, 200, []);
	}
	
	public function print($type =1){
		$current_shift = Entry::checkShift();
		$cloak_data = CloakRoom::totalShiftData();

		$total_shift_upi = $cloak_data['total_shift_upi'];
        $total_shift_cash = $cloak_data['total_shift_cash'];
        $total_collection = $cloak_data['total_collection'];
		

        return view('admin.print_shift',[
        	'cloak_data'=>$cloak_data,
        	'total_shift_upi'=>$total_shift_upi,
        	'total_shift_cash'=>$total_shift_cash,
        	'total_collection'=>$total_collection
        ]);
	}

}
