<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
class CashLimitRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

     protected $request;

     public function __construct(Request $request)
     {
         $this->request = $request;
     }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $general_setup = DB::table('general_setup')->select('voucher_cash_limit')
        ->where('fin_year_id',Session::get('finyearId'))->where('branchno',Session::get('branchId'))->first();

        $bankAcRow = DB::table('account_mst')->select('ac_group')->where('ac_id', $this->request['av_bankcode'])->first();
        $headAcRow = DB::table('account_mst')->select('ac_group')->where('ac_id', $this->request['av_headcode'])->first();
        if (!empty($bankAcRow) && $bankAcRow->ac_group == 137 && !empty($headAcRow) && $headAcRow->ac_group != 138) { //  if cash is selected and head is not bank
            
            $exceptionsArr = DB::table('voucher_cash_limit_except_head')->where('ex_type','CVEXL')->where('status','active')->select('head_id')->pluck('head_id')->toArray();

            if(!empty($exceptionsArr)){
                // $exceptionIds = implode(',', array_column($exceptionsArr, 'head_id'));
                if (!in_array($this->request['av_headcode'], $exceptionsArr) && $this->request['av_total'] > $general_setup->voucher_cash_limit) {
                    $fail("Cash limit is " . $general_setup->voucher_cash_limit . ". Cash limit not allowed for this head.");
                    return;
                }
            }elseif($this->request['av_total'] > $general_setup->voucher_cash_limit && !empty($headAcRow) && $headAcRow->ac_group != 138){
                $fail("Cash limit is " . $general_setup->voucher_cash_limit . ". Cash limit not allowed for this head.");
                return;
            }

        }

    }

}



