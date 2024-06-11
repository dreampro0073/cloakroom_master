<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use DB;

class Sitting extends Model
{

    protected $table = 'sitting_entries';

    public static function payTypes(){
        $ar = [];
        $ar[] = ['value'=>1,'label'=>'Cash'];
        $ar[] = ['value'=>2,'label'=>'UPI'];

        return $ar;
    }

    public static function getAvailLockers(){
        return DB::table('lockers')->where('status',0)->get();
    }

    public static function showPayTypes(){
        return [1=>'Cash',2=>"UPI"];
    }

    public static function hours(){
        $ar = [];
        for ($i=1; $i <= 24; $i++) { 
           $ar[] = ['value'=>$i,'label'=>$i];
        }
        return $ar;
    }

    public static function days(){
        $ar = [];
        for ($i=1; $i <= 15; $i++) { 
           $ar[] = ['value'=>$i,'label'=>$i];
        }
        return $ar;
    }

    public static function checkShift($type = 1){
        $a_shift = strtotime("06:00:00");
        $b_shift =strtotime("14:00:00");
        $c_shift =strtotime("22:00:00");

        $current_time = strtotime(date("H:i:s"));
        // $current_time = "03:09:00";

        if($current_time > $a_shift && $current_time < $b_shift){

            if($type == 1){
                return "A";
            } else {
                return "C";
            }

        }else if($current_time > $b_shift && $current_time < $c_shift){
            if($type == 1){
                return "B";
            } else {
                return "A";
            }
        }else{
            if($type == 1){
                return "C";
            } else {
                return "B";
            }
        }

    }

    public static function totalShiftData($input_date='',$user_id=0){
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
        }

        $input_date = date("Y-m-d",strtotime($input_date));

        if(Auth::user()->priv == 1){
            if($user_id == 0){
                $total_shift_upi = Entry::where('date',$input_date)->where('deleted',0)->where('pay_type',2)->sum("paid_amount");

                $total_shift_cash = Entry::where('date',$input_date)->where('deleted',0)->where('pay_type',1)->sum("paid_amount");


                $last_hour_upi_total = Entry::where('date',$input_date)->where('deleted',0)->where('pay_type',2)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount"); 
                $last_hour_cash_total = Entry::where('date',$input_date)->where('deleted',0)->where('pay_type',1)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount");
            }else{
                $total_shift_upi = Entry::where('added_by',$user_id)->where('date',$input_date)->where('deleted',0)->where('pay_type',2)->sum("paid_amount");

                $total_shift_cash = Entry::where('added_by',$user_id)->where('date',$input_date)->where('deleted',0)->where('pay_type',1)->sum("paid_amount");


                $last_hour_upi_total = Entry::where('added_by',$user_id)->where('date',$input_date)->where('deleted',0)->where('pay_type',2)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount"); 
                $last_hour_cash_total = Entry::where('added_by',$user_id)->where('date',$input_date)->where('deleted',0)->where('pay_type',1)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount");
            }
        }  else{
            $total_shift_upi = Entry::where('date',$p_date)->where('deleted',0)->where('added_by',Auth::id())->where('pay_type',2)->sum("paid_amount");

            $total_shift_cash = Entry::where('date',$p_date)->where('deleted',0)->where('added_by',Auth::id())->where('pay_type',1)->sum("paid_amount");

            $last_hour_upi_total = Entry::where('date',$p_date)->where('deleted',0)->where('added_by',Auth::id())->where('pay_type',2)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount"); 
            $last_hour_cash_total = Entry::where('date',$p_date)->where('deleted',0)->where('added_by',Auth::id())->where('pay_type',1)->whereBetween('created_at', [date('Y-m-d H:00:00'), date("Y-m-d H:i:s")])->sum("paid_amount");
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
    public static function getPDate(){
        $p_date = date("Y-m-d");
        return $p_date;
    }


}