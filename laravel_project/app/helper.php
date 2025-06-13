<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;



function user_role_loop($roleId, $permId)
{
    $Array = array(
        'role_id' => $roleId,
        'module_id' => $permId
    );
    $permission = DB::table('admin_user_role_permission')->select('permission')->where($Array)->get()->toArray();
    // echo '<pre>';
    // print_r($permission);die;

    if (!empty($permission)) {
        $array = [];
        foreach ($permission as $row) {
            $array[] = $row->permission;
        }
        return $array;
    }
}
function permission($permId, $permission)
{
    // $session = Session::All();
    // print_r(Session::all());die;
    $roleId = Session::get('roleId');
    $Array = array(
        'role_id' => $roleId,
        'module_id' => $permId,
        'permission' => $permission
    );
    // print_r($Array);die;
    $user = DB::table('admin_user_role_permission')->where($Array)->get()->toArray();
    // print_r(count($user));die;
    $role = count($user);
    return $role;
}

function setMenu($menuName)
{
    $sessionMenu = Session::get('menu');
    if ($sessionMenu == $menuName) {
        return 'active';
    }
    return "";
}

function form_check($type, $jobcard)
{
    $jobcard_no = Session::get('jobcard_no');
    $type = Session::get('type');

    $Array = array(
        'type' => $type,
        'jobcard_no' => $jobcard,
    );
    $result = DB::table('logistics_jobcard_no')->where($Array)->first();
    if (!empty($result) && $result->jobcard_no == $jobcard_no && $result->type == $type) {
        return 1;
    }

    return 0;
}

function getfinancialYear($date)
{
    $year = date("y", strtotime($date));
    $newYear = date('y', strtotime($date . ' + 1 years'));
    return $year . "-" . $newYear;
}

function to_number($number)
{
    $no = round($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
        " and " . @$words[$point / 10] . " " .
        @$words[$point = $point % 10] : '';
    if ($points) {
        return strtoupper("Rupees  " . $result .  $points . " Paise only");
    } else {
        return strtoupper("Rupees  " . $result . " only");
    }

}
function numberTowords($num)
{
    $ones = array(1 => "one", 2 => "two", 3 => "three", 4 => "four", 5 => "five", 6 => "six", 7 => "seven", 8 => "eight", 9 => "nine", 10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen", 14 => "fourteen", 15 => "fifteen", 16 => "sixteen", 17 => "seventeen", 18 => "eighteen", 19 => "nineteen");
    $tens = array(1 => "ten", 2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty", 6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety");
    $hundreds = array("hundred", "thousand", "million", "billion", "trillion", "quadrillion"); //limit t quadrillion 
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        if ($i < 20) {
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            $rettxt .= $tens[substr($i, 0, 1)];
            $rettxt .= " " . $ones[substr($i, 1, 1)];
        } else {
            $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
            $rettxt .= " " . $tens[substr($i, 1, 1)];
            $rettxt .= " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $ones[$decnum];
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " " . $ones[substr($decnum, 1, 1)];
        }
    }
    return $rettxt;
}

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

function last_query()
{
    // Get the last query and bindings from the DB connection
    $query = DB::getQueryLog(); // Retrieve query log
    if (!empty($query)) {
        $lastQuery = end($query); // Get the last query
        $sql = $lastQuery['query']; // Get the SQL query
        $bindings = $lastQuery['bindings']; // Get the bindings

        // Replace placeholders with actual bindings
        $queryWithBindings = vsprintf(str_replace('?', '%s', $sql), $bindings);

        return $queryWithBindings;
    }

    return 'No query executed yet.';
}
