<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueIgnoreRule implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;

    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
                    ->where($this->column, $value);

        // Ignore the record with the given ID
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'The :attribute must be unique.';
    }
}
