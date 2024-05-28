<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use DB;
use App\Models\Entry;

class CloakRoom extends Model
{

    protected $table = 'cloakroom_entries';

    public static function totalShiftData($input_date= "",$user_id=0){
        $check_shift = Entry::checkShift();
        
        $total_shift_cash = 0;
        $total_shift_upi = 0;       

        $last_hour_cash_total = 0;
        $last_hour_upi_total = 0;

        $from_time = date('H:00:00');
        $to_time = date('H:59:59');

        $p_date = Entry::getPDate();

        $shift_date = date("d-m-Y",strtotime($p_date));

        if($input_date == ''){
            $input_date = date("Y-m-d");
        }else{
            $input_date = date("Y-m-d",strtotime($input_date));
        }


        if(Auth::user()->priv == 2 || Auth::user()->priv == 1){
            if($user_id == 0){
                $total_shift_upi = CloakRoom::where('date',$input_date)->where('pay_type',2)->sum("paid_amount");
                $total_shift_upi += DB::table('cloakroom_penalities')->where('date',$input_date)->where('pay_type',2)->sum("paid_amount");

                $total_shift_cash = CloakRoom::where('date',$input_date)->where('pay_type',1)->sum("paid_amount");
                $total_shift_cash += DB::table('cloakroom_penalities')->where('date',$input_date)->where('pay_type',1)->sum("paid_amount");

                $last_hour_upi_total = CloakRoom::where('date',$input_date)->where('pay_type',2)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount"); 
                $last_hour_upi_total += DB::table('cloakroom_penalities')->where('date',$input_date)->where('pay_type',2)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount"); 
                
                $last_hour_cash_total = CloakRoom::where('date',$input_date)->where('pay_type',1)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount");
                $last_hour_cash_total += DB::table('cloakroom_penalities')->where('date',$input_date)->where('pay_type',1)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount");
            }else{
                $total_shift_upi = CloakRoom::where('date',$input_date)->where('added_by',$user_id)->where('pay_type',2)->sum("paid_amount");
                $total_shift_upi += DB::table('cloakroom_penalities')->where('date',$input_date)->where('added_by',$user_id)->where('pay_type',2)->sum("paid_amount");

                $total_shift_cash = CloakRoom::where('date',$input_date)->where('added_by',$user_id)->where('pay_type',1)->sum("paid_amount");
                $total_shift_cash += DB::table('cloakroom_penalities')->where('date',$input_date)->where('added_by',$user_id)->where('pay_type',1)->sum("paid_amount");

                $last_hour_upi_total = CloakRoom::where('date',$input_date)->where('added_by',$user_id)->where('pay_type',2)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount"); 
                $last_hour_upi_total += DB::table('cloakroom_penalities')->where('date',$input_date)->where('added_by',$user_id)->where('pay_type',2)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount"); 
                
                $last_hour_cash_total = CloakRoom::where('date',$input_date)->where('added_by',$user_id)->where('pay_type',1)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount");
                $last_hour_cash_total += DB::table('cloakroom_penalities')->where('date',$input_date)->where('added_by',$user_id)->where('pay_type',1)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount");
            }

            
        }else{

            $total_shift_upi = CloakRoom::where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',2)->sum("paid_amount");

            $total_shift_upi += DB::table('cloakroom_penalities')->where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',2)->sum("paid_amount");

            $total_shift_cash = CloakRoom::where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',1)->sum("paid_amount");
            $total_shift_cash += DB::table('cloakroom_penalities')->where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',1)->sum("paid_amount");

            $last_hour_upi_total = CloakRoom::where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',2)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount"); 
            $last_hour_upi_total += DB::table('cloakroom_penalities')->where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',2)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount"); 
            
            $last_hour_cash_total = CloakRoom::where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',1)->whereBetween('check_in', [$from_time, $to_time])->sum("paid_amount");
            $last_hour_cash_total += DB::table('cloakroom_penalities')->where('added_by',Auth::id())->where('date',$input_date)->where('pay_type',1)->whereBetween('current_time', [$from_time, $to_time])->sum("paid_amount");
        }

        $total_collection = $total_shift_upi + $total_shift_cash;
        $last_hour_total = $last_hour_upi_total + $last_hour_cash_total;

        $data['total_shift_upi'] = $total_shift_upi;
        $data['total_shift_cash'] = $total_shift_cash;
        $data['total_collection'] = $total_collection;

        $data['last_hour_upi_total'] = $last_hour_upi_total;
        $data['last_hour_cash_total'] = $last_hour_cash_total;
        $data['last_hour_total'] = $last_hour_total;
        $data['check_shift'] = $check_shift;
        $data['shift_date'] = $shift_date;

        return $data;
    }
}