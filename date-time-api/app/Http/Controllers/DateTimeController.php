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

        switch($this->type)
		{
			case 'DAYS' : $result = $this->calculateNumberOfDays();break;
			case 'WEEKDAYS' :  $result = $this->calculateNumberOfWeekDays();break;
			case 'WEEKS' :  $result = $this->calculateNumberOfWeeks();break;
			default:  $result = $this->calculateNumberOfDays();
		}
        $return_data = [
            'status' => true,
            'code' => 200,
            "First Date" => $this->first_date,
            "First Date Timezone" => $this->first_date_timezone,
            "Second Date" => $this->second_date,
            "Second Date Timezone" => $this->second_date_timezone,
            "Request Type" => $this->type,
            "Result Convert To" => $this->convert,
            "Time difference" => $result
        ];

        return response()->json($return_data,200);
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
        $this->first_date = $request->first_date;
        $this->second_date = $request->second_date;
        $this->first_date_timezone = !empty($request->first_date_timezone) ? $request->first_date_timezone : "Australia/Adelaide";
        $this->second_date_timezone = !empty($request->second_date_timezone) ? $request->second_date_timezone : "Europe/London";
        $this->type = strtoupper($request->type);
        $this->convert = strtoupper($request->convert);
    }

    public function calculateNumberOfDays($convert_to=null){
        $first_date_timezone = new DateTimeZone($this->first_date_timezone);
        $first_date = new DateTime($this->first_date, $first_date_timezone);

        $second_date_timezone = new DateTimeZone($this->second_date_timezone);
        $second_date = new DateTime($this->second_date, $second_date_timezone);
        
        // Calculate the difference
        $interval = $first_date->diff($second_date);

        $days_difference = $interval->days;

        $hours_difference = $days_difference * 24 + $interval->h;

        $minutes_difference = $hours_difference * 60 + $interval->i;

        $seconds_difference = $minutes_difference * 60 + $interval->s;

        $years_difference = $interval->y;

        switch($this->convert)
		{
			case 'DAYS' : return $days_difference;
			case 'HOURS' : return $hours_difference;
			case 'MINITES' : return $minutes_difference;
			case 'SECONDS' : return $seconds_difference;
			case 'YEARS' : return $years_difference;
			default: return $days_difference;
		}
        // Get the number of days
    }

    public function calculateNumberOfWeekDays(){
        $first_date_timezone = new DateTimeZone($this->first_date_timezone);
        $first_date = new DateTime($this->first_date, $first_date_timezone);

        $second_date_timezone = new DateTimeZone($this->second_date_timezone);
        $second_date = new DateTime($this->second_date, $second_date_timezone);
        
        if ($first_date > $second_date) {
            $temp = $first_date;
            $first_date = $second_date;
            $second_date = $temp;
        }
        
        $weekdays_difference = 0;
        
        while ($first_date <= $second_date) {
            // Check if the current day is a weekday (Monday to Friday)
            if ($first_date->format('N') < 6) { // 'N' returns the day of the week as a number (1 for Monday, 7 for Sunday)
                $weekdays_difference++;
            }
            // Move to the next day
            $first_date->modify('+1 day');
        }
        
        return $weekdays_difference;
    }

    public function calculateNumberOfWeeks()
    {
        $datediff = $this->calculateNumberOfDays();        
        $number_of_weeks = floor($datediff / 7);
        return $number_of_weeks;
    }
}
