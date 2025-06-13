<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckEntryDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $entryType;
    protected $id;
    protected $cash_bank;
    protected $amount;
    protected $csh_bal;
    protected $credit_head;
    public function __construct($entryType='',$id='',$cash_bank='',$amount=0,$csh_bal=0,$credit_head='')
    {
        $this->entryType = $entryType;
        $this->id = $id;
        $this->cash_bank = $cash_bank;
        $this->amount = $amount;
        $this->csh_bal = $csh_bal;
        $this->credit_head = $credit_head;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Convert the value to date format if it's not already
        $en_date = date('Y-m-d', strtotime($value));
        // Check if user is admin (userId = 1)
        // if (Session::get('userId') == 1) {
        //     // Admin users bypass date validation
        //     return;
        // }
        // Check against entry type
        $acRow = DB::table('account_mst')->select('ac_group')->where('ac_id',$this->cash_bank)->first();
        $acGroup = $acRow->ac_group ?? null;
        if($this->credit_head!='')
        {
        $CrRow = DB::table('account_mst')->select('ac_group')->where('ac_id',$this->credit_head)->first();
        $CrGroup = $CrRow->ac_group ?? null;
        }
        // Check against finance year dates
        $result = DB::table('account_fin_year')
            ->where('id', Session::get('finyearId'))
            ->first();
        // print_r($result);die();
        if (!empty($result)) {
            if ($en_date < date('Y-m-d', strtotime($result->fin_start_date))) {
                $fail("Date is below the finance year range.");
                return;
            } elseif ($en_date > date('Y-m-d', strtotime($result->fin_end_date))) {
                $fail("Date is outside the finance year range.");
                return;
            }
        }
        
        // Check against audit lock date
        $result2 = DB::table('general_setup')
            ->select('acc_auditlock','isrestrictdate','cur_date','voucher_cash_limit')
            ->where('branchno', Session::get('branchId'))
            ->where('fin_year_id', Session::get('finyearId'))
            ->first();

        if (!empty($result2)) {
            if ($en_date < date('Y-m-d', strtotime($result2->acc_auditlock))) {
                $fail("Date is below the audit lock date.");
                return;
            }
            elseif($result2->isrestrictdate == 'Y' && $this->entryType == 'R' && $acGroup == '138')
            {
                if ($en_date != $result2->cur_date) {
                    $fail("You can only entry on " . date('d-m-Y', strtotime($result2->cur_date)) );
                    return;
                }
            }
            elseif($result2->isrestrictdate == 'Y' && $this->entryType == 'V')
            {
                if ($en_date != $result2->cur_date) {
                    $fail("You can only entry on " . date('d-m-Y', strtotime($result2->cur_date)) );
                    return;
                }
            }
        }
        $specialEditPerm = permission(22, 'Specialedit');

        // Check against branch closing date
            $result3 = DB::table('branch_day_close_trn')
            ->where('branch_id', Session::get('branchId'))
            ->where('fin_year', Session::get('finyearId'))
            ->where('status','active')
            ->orderBy('date', 'desc')
            ->first();
        if($this->entryType == 'V' || $this->entryType == 'BP'){
            if (!empty($result3) && $specialEditPerm == 0) {
                if ($en_date <= date('Y-m-d', strtotime($result3->date))) {
                    $fail("Date is closed for the branch.");
                    return;
                }
            }
        }elseif($this->entryType == 'R'){
            if (!empty($result3) && $specialEditPerm == 0) {
                if ($en_date <= date('Y-m-d', strtotime($result3->date)) && $acGroup != '138') {
                    $fail("Date is closed for the branch.");
                    return;
                }
            }
        }

        // Check against user closing date
        $result4 = DB::table('userclosenew_trn')
            ->where('user_id', Session::get('userId'))
            ->where('branch_id', Session::get('branchId'))
            ->where('fin_year_id', Session::get('finyearId'))
            ->where('status','active')
            ->orderBy('id', 'desc')
            ->first();
        if($this->entryType == 'V' || $this->entryType == 'BP'){
            if (!empty($result4) && $specialEditPerm == 0) {
                if ($en_date <= date('Y-m-d', strtotime($result4->close_date))) {
                    $fail("Date is closed for the user.");
                    return;
                }
            }
        }elseif($this->entryType == 'R'){
            if (!empty($result4) && $specialEditPerm == 0) {
                if ($en_date <= date('Y-m-d', strtotime($result4->close_date)) && $acGroup != '138') {
                    $fail("Date is closed for the user.");
                    return;
                }
            }
        }
        if($this->entryType == 'R')
        {

            if($acGroup != '138')
            {
            $result5 = DB::table('receipt_mst')
                ->where('rcm_branch_id', Session::get('branchId'))
                ->where('rcm_fin_year_id', Session::get('finyearId'))
                ->where('rcm_date','>', $en_date)
                ->where('rcm_status','active')
                ->first();

            if (!empty($result5) && $specialEditPerm == 0) {
                $fail("Receipt entry already exists on forward date.");
                return;
            }
            }

        }
        elseif($this->entryType == 'V' || $this->entryType == 'BP')
        {
            $result5 = DB::table('acc_voucher')
                ->where('branch_id', Session::get('branchId'))
                ->where('av_fin_year_id', Session::get('finyearId'))
                ->where('av_date','>', $en_date)
                ->where('av_status','active')
                ->first();
            if (!empty($result5) && $specialEditPerm == 0) {
                $fail("Voucher entry already exists on forward date.");
                return;
            }

            if($this->csh_bal < $this->amount)
            {
                $fail("Your Cash/Bank Balance is " . round($this->csh_bal,2) );
                return;
            }
            elseif($acGroup == '137' && $this->amount > $result2->voucher_cash_limit && $CrGroup != '138' )
            {
                $fail("You cannot pay Cash more than " . round($result2->voucher_cash_limit,2) );
                return;
            }
        }
        elseif($this->entryType == 'IBT')
        {
            if($CrGroup == '138')
            {
                $fail("Your Cannot Select Bank as an Expense Head");
                return;
            }
            elseif($this->csh_bal < $this->amount)
            {
                $fail("Your Cash/Bank Balance is " . round($this->csh_bal,2) );
                return;
            }
            // elseif($acGroup == '137' && $this->amount > $result2->voucher_cash_limit )
            // {
            //     $fail("You cannot pay Cash more than " . round($result2->voucher_cash_limit,2) );
            //     return;
            // }            
        }
        elseif($this->entryType == 'SJ')
        {
            if($acGroup == '137'  && $this->amount > $result2->voucher_cash_limit && $CrGroup != '138')
            {
                $fail("You cannot pay Cash more than " . round($result2->voucher_cash_limit,2) );
                return;
            }
            elseif($this->csh_bal < $this->amount && ($acGroup == '137' || $acGroup == '138') )
            {
                $fail("Your Cash/Bank Balance is " . round($this->csh_bal,2) );
                return;
            }       
        }
    }

}



