<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Redirect, Validator, Hash, Response, Session, DB;
use App\Models\Entry, App\Models\User, App\Models\Sitting;

class SittingController extends Controller {
	public function sitting(Request $request){
		    
		return view('admin.sitting.index_new', [
            "sidebar" => "sitting",
            "subsidebar" => "sitting",
        ]);
	}
	public function initEntries(Request $request){
		$entries = Sitting::select('sitting_entries.*','users.name as username')->leftJoin('users','users.id','=','sitting_entries.delete_by');
		if($request->unique_id){
			$entries = $entries->where('sitting_entries.unique_id', 'LIKE', '%'.$request->unique_id.'%');
		}		
		if($request->name){
			$entries = $entries->where('sitting_entries.name', 'LIKE', '%'.$request->name.'%');
		}		
		if($request->mobile_no){
			$entries = $entries->where('sitting_entries.mobile_no', 'LIKE', '%'.$request->mobile_no.'%');
		}		
		if($request->pnr_uid){
			$entries = $entries->where('sitting_entries.pnr_uid', 'LIKE', '%'.$request->pnr_uid.'%');
		}		
		
		if(Auth::user()->priv != 1){
			$entries = $entries->where('deleted',0);

		}
		$entries = $entries->orderBy('id', "DESC");

		// if(Auth::user()->priv != 1){

		// }
		$entries = $entries->take(200);

		$entries = $entries->get();

		$pay_types = Entry::payTypes();
		$hours = Entry::hours();
		$data['success'] = true;
		$data['entries'] = $entries;
		$data['pay_types'] = $pay_types;
		$data['hours'] = $hours;
		return Response::json($data, 200, []);
	}	
	
	public function editEntry(Request $request){
		$sitting_entry = Sitting::where('id', $request->entry_id)->first();

		if($sitting_entry){
			$sitting_entry->mobile_no = $sitting_entry->mobile_no*1;
			$sitting_entry->train_no = $sitting_entry->train_no*1;
			$sitting_entry->pnr_uid = $sitting_entry->pnr_uid;
			$sitting_entry->paid_amount = $sitting_entry->paid_amount*1;
			$sitting_entry->total_amount = $sitting_entry->paid_amount*1;
			$sitting_entry->check_in = date("h:i A",strtotime($sitting_entry->check_in));
			$sitting_entry->check_out = date("h:i A",strtotime($sitting_entry->check_out));
		}

		$data['success'] = true;
		$data['sitting_entry'] = $sitting_entry;
		return Response::json($data, 200, []);
	}
	public function calCheck(Request $request){
		
		$check_in = $request->check_in;
		$hours_occ = $request->hours_occ;

		$ss_time = strtotime(date("h:i A",strtotime($check_in)));

		$new_time = date("h:i A", strtotime('+'.$hours_occ.' hours', $ss_time));

		$data['success'] = true;
		$data['check_out'] = $new_time;
		return Response::json($data, 200, []);
	}

	public function store(Request $request){

		$check_shift = Sitting::checkShift();

		$cre = [
			'name'=>$request->name,
		];

		$rules = [
			'name'=>'required',
		];

		$validator = Validator::make($cre,$rules);
		$date = Entry::getPDate();

		if($validator->passes()){
			$total_amount = $request->total_amount;
			if($request->id){
				$group_id = $request->id;
				$entry = Sitting::find($request->id);
				$message = "Updated Successfully!";
				$old_entry = $entry;


				if(Auth::id() != $entry->added_by){
					// dd($entry->check_out);
					$total_amount = $total_amount - $entry->paid_amount;
					$entry = new Sitting;
					$message = "Stored Successfully!";
					$entry->unique_id = strtotime('now');
					$entry->check_in = $old_entry->check_out;
					
					$entry->hours_occ = $request->hours_occ - $old_entry->hours_occ;
					$entry->added_by = Auth::id();
					$entry->date = $date;
					$entry->created_at = date('Y-m-d H:i:s');
					$entry->save();

				}else{
					$entry->check_in = date("H:i:s",strtotime($request->check_in));
					$entry->hours_occ = $request->hours_occ ? $request->hours_occ : 0;
				}
			} else {
				$entry = new Sitting;
				$message = "Stored Successfully!";
				$entry->unique_id = strtotime('now');
				$entry->check_in = date("H:i:s");
				$entry->hours_occ = $request->hours_occ ? $request->hours_occ : 0;
				$entry->added_by = Auth::id();
				$entry->date = $date;
				$entry->created_at = date('Y-m-d H:i:s');
			}

			$entry->name = $request->name;
			$entry->pnr_uid = $request->pnr_uid;
			$entry->mobile_no = $request->mobile_no;
			
			$entry->no_of_adults = $request->no_of_adults ? $request->no_of_adults : 0;
			$entry->no_of_children = $request->no_of_children ? $request->no_of_children : 0;
			$entry->no_of_baby_staff = $request->no_of_baby_staff ? $request->no_of_baby_staff : 0;
			$entry->seat_no = $request->seat_no;
			$entry->paid_amount = $total_amount;
			$entry->pay_type = $request->pay_type;
			$entry->remarks = $request->remarks;
			$entry->shift = $check_shift;
			$entry->save();
			$no_of_min = $entry->hours_occ*60;
			$entry->check_out = date("H:i:s",strtotime("+".$no_of_min." minutes",strtotime($entry->check_in)));
			$entry->save();

			$data['id'] = $entry->id;
			$data['success'] = true;

		} else {
			$data['success'] = false;
			$message = $validator->errors()->first();
		}

		return Response::json($data, 200, []);

	}

	public function printReports(){
		$print_data = new \stdClass;
		$data = Sitting::totalShiftData();
		$print_data->type = "shift";
		$print_data->total_shift_cash = $data['total_shift_cash']; 
		$print_data->total_shift_upi = $data['total_shift_upi'];
		$print_data->total_collection = $data['total_collection'];
		$print_data->last_hour_upi_total = $data['last_hour_upi_total'];
		$print_data->last_hour_cash_total = $data['last_hour_cash_total'];
		$print_data->last_hour_total = $data['last_hour_total'];
		$print_data->check_shift = $data['check_shift'];
		$print_data->shift_date = $data['shift_date'];
		$this->printFinal($print_data);
	}
	
	public function printPost($id = 0){

        $print_data = DB::table('sitting_entries')->where('id', $id)->first();
		$print_data->type = "silip";
        
        $print_data->total_member = $print_data->no_of_adults + $print_data->no_of_children + $print_data->no_of_baby_staff;
        $print_data->adult_amount = 0;
        $print_data->children_amount = 0;
        $hours = $print_data->hours_occ;

        if($hours > 0) {
            $print_data->adult_amount = $print_data->no_of_adults * 30 * $hours;
            $print_data->children_amount = $print_data->no_of_children * 20 * $hours;
        }
              
		return view('admin.sitting.print_sitting',compact('print_data'));
	}

    public function delete($id){
    	DB::table('sitting_entries')->where('id',$id)->update([
    		'deleted' => 1,
    		'delete_by' => Auth::id(),
    		'delete_time' => date("Y-m-d H:i:s"),

    	]);

    	$data['success'] = true;
    	$data['message'] = "Successfully";
		
		return Response::json($data, 200, []);
    }

}
