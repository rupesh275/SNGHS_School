<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckEntryDateTwo implements ValidationRule
{
    /**
     * The custom parameter for validation.
     *
     * @var mixed
     */
    protected $customParam;

    /**
     * Create a new rule instance.
     *
     * @param mixed $customParam
     * @return void
     */
    public function __construct($customParam = null)
    {
        $this->customParam = $customParam;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Convert the value to date format if it's not already
        $en_date = date('Y-m-d', strtotime($value));

        // Check against finance year dates
        $result = DB::table('account_fin_year')
            ->where('id', Session::get('finyearId'))
            ->first();

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
            ->select('acc_auditlock')
            ->where('branchno', Session::get('branchId'))
            ->where('fin_year_id', Session::get('finyearId'))
            ->first();

        if (!empty($result2)) {
            if ($en_date < date('Y-m-d', strtotime($result2->acc_auditlock))) {
                $fail("Date is below the audit lock date.");
                return;
            }
        }

        // Check against branch closing date
        $result3 = DB::table('branch_day_close_trn')
            ->where('branch_id', Session::get('branchId'))
            ->where('fin_year', Session::get('finyearId'))
            ->where('status','active')
            ->orderBy('date', 'desc')
            ->first();

        if (!empty($result3)) {
            if ($en_date <= date('Y-m-d', strtotime($result3->date))) {
                $fail("Date is closed for the branch.");
                return;
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

        if (!empty($result4)) {
            if ($en_date <= date('Y-m-d', strtotime($result4->close_date))) {
                $fail("Date is closed for the user.");
                return;
            }
        }

        // Check for receipt_mst records with rcm_type = 'G' or 'M'
        if (isset($this->customParam['receipt_date']) && !empty($this->customParam['receipt_date'])) {
            $receipt_date = $this->customParam['receipt_date'];
            if ($receipt_date) {
                $formatted_date = date('d-m-Y', strtotime($receipt_date));
                $fail("Cannot enter date before $formatted_date as a receipt entry exists on or after this date.");
                return;
            }
        }

        // Check for acc_voucher records with av_type = 'S' or 'M'
        if (isset($this->customParam['voucher_date']) && !empty($this->customParam['voucher_date'])) {
            $voucher_date = $this->customParam['voucher_date'];
            if ($voucher_date) {
                $formatted_date = date('d-m-Y', strtotime($voucher_date));
                $fail("Cannot enter date before $formatted_date as a voucher entry exists on or after this date.");
                return;
            }
        }
    }
}
