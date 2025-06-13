<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckEntryDateAfterFD implements Rule
{
    protected $fdDate;

    public function __construct($fdDate)
    {
        $this->fdDate = $fdDate;
    }

    public function passes($attribute, $value)
    {
        return strtotime($value) >= strtotime($this->fdDate);
    }

    public function message()
    {
        return 'The date must be on or after the FD start date.';
    }
}
