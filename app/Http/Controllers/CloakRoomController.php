<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Redirect, Validator, Hash, Response, Session, DB;
use App\Models\Massage, App\Models\User;
use App\Models\Entry;
use App\Models\CloakRoom;

class CloakRoomController extends Controller {	
	public function index(){
		return view('admin.cloakrooms.index', [
            "sidebar" => "cloakrooms",
            "subsidebar" => "cloakrooms",
            "type" => 0,
        ]);
	}
	public function allRooms(){
		return view('admin.cloakrooms.index', [
            "sidebar" => "all-cloakrooms",
            "subsidebar" => "all-cloakrooms",
            "type" => 1,
        ]);
	}
	public function initRoom(Request $request,$type =0){

		$l_entries = DB::table('cloakroom_entries')->select('cloakroom_entries.*','users.name as username')->leftJoin('users','users.id','=','cloakroom_entries.delete_by')->where("cloakroom_entries.client_id", Auth::user()->client_id);
		if($request->id){
			$l_entries = $l_entries->where('cloakroom_entries.id', $request->id);
		}

		if($request->unique_id){
			$l_entries = $l_entries->where('cloakroom_entries.unique_id', 'LIKE', '%'.$request->unique_id.'%');
		}		

		if($request->name){
			$l_entries = $l_entries->where('cloakroom_entries.name', 'LIKE', '%'.$request->name.'%');
		}		
		if($request->mobile_no){
			$l_entries = $l_entries->where('cloakroom_entries.mobile_no', 'LIKE', '%'.$request->mobile_no.'%');
		}		
		if($request->pnr_uid){
			$l_entries = $l_entries->where('cloakroom_entries.pnr_uid', 'LIKE', '%'.$request->pnr_uid.'%');
		}		
		
		if($type == 0){
			$l_entries = $l_entries->where('checkout_status', 0)->take(20);
		}
		
		$l_entries = $l_entries->orderBy('id', "DESC")->get();

		foreach ($l_entries as $key => $item) {
			$bm_amount = DB::table('cloakroom_penalities')->where("client_id", Auth::user()->client_id)->where('cloakroom_id','=',$item->id)->sum('paid_amount');
			$item->sh_paid_amount = $item->paid_amount + $bm_amount;
			$item->checkin_date_show = date("d M, h:i A",strtotime($item->checkin_date));
			$item->checkout_date_show = date("d M, h:i A",strtotime($item->checkout_date));
		}

		$rate_list = DB::table("cloakroom_rate_list")->where("client_id", Auth::user()->client_id)->first();
		$pay_types = Entry::payTypes();
		$days = Entry::days();
		$show_pay_types = Entry::showPayTypes();

		$data['success'] = true;
		$data['l_entries'] = $l_entries;
		$data['pay_types'] = $pay_types;
		$data['rate_list'] = $rate_list;
		$data['days'] = $days;
		return Response::json($data, 200, []);
	}
	public function editRoom(Request $request){
		$l_entry = CloakRoom::where('id', $request->entry_id)->where("client_id", Auth::user()->client_id)->first();

		if($l_entry){
			$l_entry->mobile_no = $l_entry->mobile_no*1;
			$l_entry->train_no = $l_entry->train_no*1;
			$l_entry->pnr_uid = $l_entry->pnr_uid;
			$l_entry->paid_amount = $l_entry->paid_amount*1;
			$l_entry->check_in = date("d-m-Y",strtotime($l_entry->date))." ".date("h:i A",strtotime($l_entry->check_in));
			$l_entry->check_out = date("d-m-Y h:i A",strtotime($l_entry->checkout_date));
			$bm_amount = DB::table('cloakroom_penalities')->where('cloakroom_id','=',$l_entry->id)->sum('paid_amount');
			$l_entry->paid_amount = $l_entry->paid_amount + $bm_amount;

			$l_entry->bm_amount = $bm_amount;
		}

		$data['success'] = true;
		$data['l_entry'] = $l_entry;
		return Response::json($data, 200, []);
	}
	
	public function store(Request $request){
		$check_shift = Entry::checkShift();
		$cre = [
			'name'=>$request->name,
		];

		$rules = [
			'name'=>'required',
		];

		$validator = Validator::make($cre,$rules);

		if($validator->passes()){
			
			$date = Entry::getPDate();
			$balance_amount = $request->balance_amount;
			$paid_amount = $request->paid_amount;
			if($request->id){
				$group_id = $request->id;
				$entry = CloakRoom::find($request->id);
				$message = "Updated Successfully!";
				$entry->check_in = date("H:i:s",strtotime($request->check_in));
				
				DB::table('cloakroom_penalities')->insert([
					'cloakroom_id' => $entry->id,
					'paid_amount' => $balance_amount,
					'pay_type' => $request->pay_type,
					'shift' => $check_shift,
					'date' =>$date,
					'added_by' =>Auth::id(),
					'client_id' =>Auth::user()->client_id,
					'current_time' => date("H:i:s"),
					'created_at' => date('Y-m-d H:i:s'),
				]);

			} else {
				$entry = new CloakRoom;
				$message = "Stored Successfully!";
				$entry->unique_id = strtotime('now');
				$entry->created_at = date("Y-m-d H:i:s");
				$entry->checkin_date = date("Y-m-d H:i:s");
				$entry->check_in = date("H:i:s");
				$entry->date = date("Y-m-d");
				$entry->shift = $check_shift;
				$entry->added_by = Auth::id();
				$entry->paid_amount = $request->paid_amount;

			}

			$entry->name = $request->name;
			$entry->no_of_bag = $request->no_of_bag;
			$entry->pnr_uid = $request->pnr_uid;
			$entry->mobile_no = $request->mobile_no;
			$entry->no_of_day = $request->no_of_day;
			$entry->total_day = $request->no_of_day;
			$entry->pay_type = $request->pay_type;
			$entry->remarks = $request->remarks;
			$entry->paid_amount = $request->paid_amount;
			$entry->client_id = Auth::user()->client_id;
			$entry->save();

			$checkout_date = date("Y-m-d H:i:s",strtotime("+".$entry->no_of_day.' day',strtotime($entry->check_in)));
	        $entry->checkout_date = $checkout_date;
			$entry->save();

			$data['id'] = $entry->id;
			$data['success'] = true;
		} else {
			$data['success'] = false;
			$message = $validator->errors()->first();
		}

		return Response::json($data, 200, []);

	}

	// public function printPost($id = 0){
    //     $print_data = DB::table('cloakroom_entries')->where('id', $id)->first();

    //     $bm_amount = DB::table('cloakroom_penalities')->where('cloakroom_id','=',$print_data->id)->sum('paid_amount');
	// 	$print_data->paid_amount = $print_data->paid_amount + $bm_amount;
	// 	$print_data->bm_amount = $bm_amount;

    //     return view('admin.print_page_cloack', compact('print_data'));
	// }

	public function printPost($id = 0){

		$rate_list = DB::table("cloakroom_rate_list")->where("client_id", Auth::user()->client_id)->first();
        $print_data = DB::table('cloakroom_entries')->where('id', $id)->first();


        $total_amount = $print_data->paid_amount;

        $bm_amount = DB::table('cloakroom_penalities')->where('cloakroom_id','=',$print_data->id)->sum('paid_amount');

        $total_amount = $total_amount+$bm_amount;
       
        $print_data->for_first_day = 0;
        $print_data->for_other_day = 0;        
        $total_day = $print_data->total_day - 1;
        
        if($print_data->total_day > 0) {
            $print_data->for_first_day = $print_data->no_of_bag * $rate_list->first_rate;
            
        }           
        if($total_day > 0){
            $print_data->for_other_day = $print_data->no_of_bag * $total_day * $rate_list->second_rate;

        }
        $rate_list = DB::table("cloakroom_rate_list")->where("client_id", Auth::user()->client_id)->first();
              
		return view('admin.print_page_cloack',compact('print_data','total_amount','rate_list'));
	}
	public function printLuggage($id = 0){

        $print_data = DB::table('cloakroom_entries')->where('id', $id)->first();


    
              
		return view('admin.print_bag_no',compact('print_data'));
	}

    public function checkoutInit(Request $request){

    	$now_time = strtotime(date("Y-m-d H:i:s",strtotime("+5 minutes")));
    	$l_entry = CloakRoom::where('id', $request->entry_id)->first();
    	$checkout_time = strtotime($l_entry->checkout_date);
    	$rate_list = DB::table("cloakroom_rate_list")->where("client_id", Auth::user()->client_id)->first();

    	if($checkout_time > $now_time){
    		$data['timeOut'] = false;
    		$entry = CloakRoom::find($request->entry_id);
    		$entry->status = 1; 
    		$entry->checkout_status = 1; 
    		$entry->checkout_time = date("Y-m-d H:i:s"); 
    		$entry->save();
    		$data['success'] = true;
    
    	} else {
    		$str_day = ($now_time - $checkout_time)/(60 * 60 * 24);
    		$day =0;
    		if($str_day > 0 && $str_day <= 1){
    			$day = 1;
    		}else if($str_day > 1 && $str_day <= 2){
    			$day = 2;
    		}if($str_day > 2 && $str_day <= 3){
    			$day = 3;
    		}if($str_day > 3 && $str_day <= 4){
    			$day = 4;
    		}if($str_day > 4 && $str_day <= 5){
    			$day = 5;
    		}if($str_day > 5 && $str_day <= 6){
    			$day = 6;
    		}if($str_day > 6 && $str_day <= 7){
    			$day = 7;
    		}if($str_day > 7 && $str_day <= 8){
    			$day = 8;
    		}if($str_day > 8 && $str_day <= 9){
    			$day = 9;
    		}if($str_day > 9 && $str_day <= 10){
    			$day = 10;
    		}if($str_day > 10 && $str_day <= 11){
    			$day = 11;
    		}if($str_day > 11 && $str_day <= 12){
    			$day = 12;
    		}if($str_day > 12 && $str_day <= 13){
    			$day = 13;
    		}if($str_day > 13 && $str_day <= 14){
    			$day = 14;
    		}if($str_day > 14 && $str_day <= 15){
    			$day = 15;
    		}if($str_day > 15 && $str_day <= 16){
    			$day = 16;
    		}if($str_day > 16 && $str_day <= 17){
    			$day = 17;
    		}if($str_day > 17 && $str_day <= 18){
    			$day = 18;
    		}if($str_day > 18 && $str_day <= 19){
    			$day = 19;
    		}if($str_day > 19 && $str_day <= 20){
    			$day = 20;
    		}if($str_day > 20 && $str_day <= 21){
    			$day = 21;
    		}if($str_day > 21 && $str_day <= 22){
    			$day = 22;
    		}if($str_day > 22 && $str_day <= 23){
    			$day = 23;
    		}if($str_day > 23 && $str_day <= 24){
    			$day = 24;
    		}

			$l_entry->mobile_no = $l_entry->mobile_no*1;
			$l_entry->train_no = $l_entry->train_no*1;
			$l_entry->pnr_uid = $l_entry->pnr_uid*1;
			$l_entry->paid_amount = $l_entry->paid_amount*1;
			$l_entry->balance = $day* $rate_list->second_rate *$l_entry->no_of_bag;
			$l_entry->total_balance = $l_entry->paid_amount+$l_entry->balance;
			$l_entry->day = $day;

			
			$data['l_entry'] = $l_entry;
			$data['success'] = true;
			$data['timeOut'] = true;
		}

		return Response::json($data, 200, []);
    }

    public function checkoutStore(Request $request){
    	$check_shift = Entry::checkShift();
    	$entry = CloakRoom::find($request->id);

    	$total_day =  $entry->total_day + $request->day;

		$entry->status = 1; 
		$entry->checkout_status = 1;
		$entry->is_late = 1;
		$entry->total_day = $total_day;
		$entry->penality = $request->balance;
		$entry->checkout_time = date('Y-m-d H:i:s'); 

		$entry->save();

		$date = Entry::getPDate();

		DB::table('cloakroom_penalities')->insert([
			'cloakroom_id' => $entry->id,
			'paid_amount' => $request->balance,
			'pay_type' => $request->pay_type,
			'shift' => $check_shift,
			'date' =>$date,
			'added_by' =>Auth::id(),
			'client_id' =>Auth::user()->client_id,
			'current_time' => date("H:i:s"),
			'created_at' => date('Y-m-d H:i:s'),
		]);
		$data['success'] = true;
		$data['id'] = $request->id;
		return Response::json($data, 200, []);
    }
    
    // public function delete($id){
    // 	DB::table('cloakroom_entries')->where('id',$id)->update([
    // 		'deleted' => 1,
    // 		'delete_by' => Auth::id(),
    // 		'delete_time' => date("Y-m-d H:i:s"),
    // 	]);

    // 	$data['success'] = true;
    // 	$data['message'] = "Successfully";

	// 	return Response::json($data, 200, []);
	// }


}
