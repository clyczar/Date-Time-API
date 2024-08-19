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
    const HOURS_PER_DAY = 24;
    const MINUTES_PER_HOUR = 60;
    const SECOND_PER_MINUTE = 60;
    public function index(Request $request)
    {
        // Validate the request
        $validate_date = $this->validateDatesFromRequest($request);
        if (!$validate_date['status']) return response()->json($validate_date, 422);

        // Setup the basic variables
        $this->loadBasicParametersFromRequest($request);

        // Using different function based on request type
        switch ($this->type) {
            case 'DAYS':
                $result = $this->calculateNumberOfDays();
                break;
            case 'WEEKDAYS':
                $result = $this->calculateNumberOfWeekDays();
                break;
            case 'WEEKS':
                $result = $this->calculateNumberOfWeeks();
                break;
            default:
                $result = $this->calculateNumberOfDays();
                break;
        }

        // Arrange the final return data
        $return_data = [
            'status' => true,
            'code' => 200,
            "First Date" => $this->first_date,
            "First Date Timezone" => $this->first_date_timezone,
            "Second Date" => $this->second_date,
            "Second Date Timezone" => $this->second_date_timezone,
            "Request Type" => $this->type,
            "Result Convert To" => $this->convert,
            "Time difference" => ($this->convert == "") ? $result . " " . $this->type : $result . " " . $this->convert
        ];
        return response()->json($return_data, 200);
    }

    public function validateDatesFromRequest($request)
    {
        // Validate first_date and second_date, setup the format 
        $validate_rules = [
            'first_date' => 'required|date|date_format:"Y-m-d H:i:s"',
            'second_date' => 'required|date|date_format:"Y-m-d H:i:s"'
        ];

        $validator = Validator::make($request->all(), $validate_rules);

        if ($validator->fails()) {
            $data = [
                'status' => false,
                'code' => 422,
                'message' => $validator->messages()
            ];
        } else {
            $data = [
                'status' => true
            ];
        }
        return $data;
    }

    public function loadBasicParametersFromRequest($request)
    {
        $this->first_date = $request->first_date;
        $this->second_date = $request->second_date;
        $this->first_date_timezone = !empty($request->first_date_timezone) ? $request->first_date_timezone : "Australia/Adelaide";
        $this->second_date_timezone = !empty($request->second_date_timezone) ? $request->second_date_timezone : "Australia/Adelaide";
        $this->type = strtoupper($request->type);
        $this->convert = strtoupper($request->convert);
    }

    public function calculateNumberOfDays()
    {
        // Setup the first date with timezone
        $first_date_timezone = new DateTimeZone($this->first_date_timezone);
        $first_date = new DateTime($this->first_date, $first_date_timezone);

        // Setup the second date with timezone
        $second_date_timezone = new DateTimeZone($this->second_date_timezone);
        $second_date = new DateTime($this->second_date, $second_date_timezone);

        // Calculate the difference
        $interval = $first_date->diff($second_date);
        $days_difference = $interval->days;
        $hours_difference = $days_difference * self::HOURS_PER_DAY + $interval->h;
        $minutes_difference = $hours_difference * self::MINUTES_PER_HOUR + $interval->i;
        $seconds_difference = $minutes_difference * self::SECOND_PER_MINUTE + $interval->s;
        $years_difference = $interval->y;

        switch ($this->convert) {
            case 'DAYS':
                return $days_difference;
            case 'HOURS':
                return $hours_difference;
            case 'MINUTES':
                return $minutes_difference;
            case 'SECONDS':
                return $seconds_difference;
            case 'YEARS':
                return $years_difference;
            default:
                return $days_difference;
        }
    }

    public function calculateNumberOfWeekDays()
    {
        // Setup the first date with timezone
        $first_date_timezone = new DateTimeZone($this->first_date_timezone);
        $first_date = new DateTime($this->first_date, $first_date_timezone);

        // Setup the second date with timezone
        $second_date_timezone = new DateTimeZone($this->second_date_timezone);
        $second_date = new DateTime($this->second_date, $second_date_timezone);

        //Switch the two date if the first date is greater than second
        if ($first_date > $second_date) {
            $temp = $first_date;
            $first_date = $second_date;
            $second_date = $temp;
        }
        
        $interval = $first_date->diff($second_date);
        $weekdays_difference = 0;
        $weekdays_hours_difference = 0;
        $weekdays_minutes_difference = 0;
        $weekdays_seconds_difference = 0;
        $weekdays_years_difference = 0;
        while ($first_date <= $second_date) {
            // Check if the current day is a weekday (Monday to Friday)
            if ($first_date->format('N') < 6) { // 'N' returns the day of the week as a number (1 for Monday, 7 for Sunday)
                $weekdays_difference += 1;
                // Assume 1 weekday has 24 hours
                $weekdays_hours_difference += self::HOURS_PER_DAY;
                $weekdays_minutes_difference += self::HOURS_PER_DAY * self::MINUTES_PER_HOUR;
                $weekdays_seconds_difference += self::HOURS_PER_DAY * self::MINUTES_PER_HOUR * self::SECOND_PER_MINUTE;
            }
            // Move to the next day
            $first_date->modify('+1 day');
        }
        $weekdays_hours_difference += $interval->h;
        $weekdays_minutes_difference += $interval->h * self::MINUTES_PER_HOUR + $interval->i;
        $weekdays_seconds_difference += ($interval->h * self::MINUTES_PER_HOUR + $interval->i) * self::SECOND_PER_MINUTE + $interval->s;
        $weekdays_years_difference = $interval->y;

        switch ($this->convert) {
            case 'DAYS':
                return $weekdays_difference;
            case 'HOURS':
                return $weekdays_hours_difference;
            case 'MINUTES':
                return $weekdays_minutes_difference;
            case 'SECONDS':
                return $weekdays_seconds_difference;
            case 'YEARS':
                return $weekdays_years_difference;
            default:
                return $weekdays_difference;
        }
    }

    public function calculateNumberOfWeeks()
    {
        $date_diff = $this->calculateNumberOfDays();
        $number_of_weeks = floor($date_diff / 7);
        return $number_of_weeks;
    }
}
