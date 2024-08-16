<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use DateTime;
use DateTimeZone;

class DateTimeController extends Controller
{
    var $first_date;
    var $second_date;
    var $first_date_timezone;
    var $second_date_timezone;
    var $time_difference;
    var $type;
    var $convert;
    public function index(Request $request)
    {
        $validate_date=$this->validate_dates_from_request($request);

        if(!$validate_date['status'])
        {
            return response()->json($validate_date,422);
        }

        $this->load_basic_parameters_from_request($request);

        switch(strtoupper($this->type))
		{
			case 'DAYS' : $result = $this->calculateNumberOfDays();break;
			case 'WEEKDAYS' :  $result = $this->calculateNumberOfWeekDays();break;
			case 'WEEKS' :  $result = $this->calculateNumberOfWeeks();break;
			default:  $result = $this->calculateNumberOfDays();
		}

        return response()->json($result,422);
    }

    public function validate_dates_from_request($request)
    {
        // Validate start_date and end_date
        $validate_rules = [
            'first_date' => 'required',
            'second_date' => 'required'
        ];

        $validator = Validator::make($request->all(),$validate_rules);
        
        if($validator->fails())
        {
            $data=[
                'status' => false,
                'code' => 422,
                'message' => $validator->messages()
            ];
        }
        else
        {
            $data=[
                'status' => true,
                'code' => 200,
                'message' => 'validate date'
            ];
        }
        return $data;
    }

    public function load_basic_parameters_from_request($request)
    { 
        $this->first_date = strtotime($request->first_date);
        $this->second_date = strtotime($request->second_date);
        $this->first_date_timezone = !empty($request->first_date_timezone) ? $request->first_date_timezone : "Europe/London";
        $this->second_date_timezone = !empty($request->second_date_timezone) ? $request->second_date_timezone : "Europe/London";
        $this->time_difference = $request->time_difference;
        $this->type = $request->type;
        $this->convert = $request->convert;
    }

    public function calculateNumberOfDays($convert_to=null){
       
        $first_date = new DateTime();
        $first_date->setTimestamp($this->first_date);
        $first_date->setTimezone(new DateTimeZone($this->first_date_timezone));
        
        $second_date = new DateTime();
        $second_date->setTimestamp($this->second_date);
        $second_date->setTimezone(new DateTimeZone($this->second_date_timezone));

        // Calculate the difference
        $interval = $first_date->diff($second_date);
        
        // Get the number of days
        $days = $interval->days;
        return $days;
    }

    public function calculateNumberOfWeekDays(){
        $timestamp1 = $this->first_date;
        $timestamp2 = $this->second_date;
        
        if ($timestamp1 > $timestamp2) {
            $temp = $timestamp1;
            $timestamp1 = $timestamp2;
            $timestamp2 = $temp;
        }
        
        $weekdaysCount = 0;
        
        for ($current = $timestamp1; $current <= $timestamp2; $current += 86400) {
            $dayOfWeek = date('N', $current);
            
            if ($dayOfWeek < 6) {
                $weekdaysCount++;
            }
        }
        
        return $weekdaysCount;
    }

    public function calculateNumberOfWeeks()
    {
        $datediff = $this->calculateNumberOfDays();        
        $number_of_weeks = floor($datediff / 7);
        
        return $number_of_weeks;
    }
}
