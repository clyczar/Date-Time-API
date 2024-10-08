<?php

namespace Tests\Feature;
use Tests\TestCase;

class DateTimeTest extends TestCase
{
    public function test_date_time_api_without_first_date(): void
    {
      $request_data= [
        'first_date' => "",
        'second_date' => "2024-08-02 01:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Sydney",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(422);
      $response->assertSee('The first date field is required.');
    }
    public function test_date_time_api_without_first_date_with_incorrect_format(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00",
        'second_date' => "2024-08-02 01:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Sydney",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(422);
      $response->assertSee('The first date field must match the format Y-m-d H:i:s.');
    }

    public function test_date_time_api_without_second_date(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Sydney",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(422);
      $response->assertSee('The second date field is required.');
    }

    public function test_date_time_api_with_same_timezone_days(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '10 DAYS']);
    }
    public function test_date_time_api_with_same_timezone_weeks(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "weeks",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '1 WEEKS']);
    }
    public function test_date_time_api_with_same_timezone_weekdays(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "weekdays",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '7 WEEKDAYS']);
    }

    public function test_date_time_api_with_same_timezone_days_convert_to_hours(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "hours"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '241 HOURS']);
    }

    public function test_date_time_api_with_same_timezone_days_convert_to_minutes(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "minutes"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '14460 MINUTES']);
    }

    public function test_date_time_api_with_same_timezone_days_convert_to_seconds(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:01",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "seconds"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '867601 SECONDS']);
    }

    public function test_date_time_api_with_same_timezone_days_convert_to_years(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2025-08-11 02:00:01",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "years"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '1 YEARS']);
    }
    public function test_date_time_api_with_same_timezone_days_convert_to_years_test_2(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2025-07-11 02:00:01",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "years"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '0 YEARS']);
    }
    public function test_date_time_api_with_same_timezone_weekdays_convert_to_hours(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "weekdays",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "hours"
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '169 HOURS']);
    }
    public function test_date_time_api_with_same_timezone_weekdays_convert_to_minutes(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:10:10",
        'type' => "weekdays",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "minutes"
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '10150 MINUTES']);
    }
    public function test_date_time_api_with_same_timezone_weekdays_convert_to_seconds(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:10:10",
        'type' => "weekdays",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "seconds"
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '609010 SECONDS']);
    }
    public function test_date_time_api_with_different_timezone_days(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '9 DAYS']);
    }

    public function test_date_time_api_with_different_timezone_weekdays(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "weekdays",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '7 WEEKDAYS']);
    }
    public function test_date_time_api_with_different_timezone_weeks(): void
    {
      $request_data= [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "weeks",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ];
      $response = $this->postJson(
        '/api/date', 
        $request_data
        );

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '1 WEEKS']);
    }

    public function test_date_time_api_with_different_timezone_convert_to_hours(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "hours"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '232 HOURS']);
    }

    public function test_date_time_api_with_different_timezone_convert_to_minutes(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "minutes"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '13950 MINUTES']);
    }

    public function test_date_time_api_with_different_timezone_convert_to_seconds(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2024-08-11 02:00:01",
        'type' => "days",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "seconds"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '837001 SECONDS']);
    }

    public function test_date_time_api_with_different_timezone_convert_to_years(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-08-01 01:00:00",
        'second_date' => "2025-08-11 02:00:01",
        'type' => "days",
        'first_date_timezone' => "Europe/London",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "years"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '1 YEARS']);
    }
    
    public function test_date_time_api_with_same_timezone_days_in_leap_year(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-02-28 01:00:00",
        'second_date' => "2024-03-01 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => ""
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '2 DAYS']);
    }
    public function test_date_time_api_with_same_timezone_days_convert_to_hours_in_leap_year(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-02-28 01:00:00",
        'second_date' => "2024-03-01 02:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "hours"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '49 HOURS']);
    }

    public function test_date_time_api_with_same_timezone_days_convert_to_hours_in_summer_time(): void
    {
      $response = $this->postJson(
        uri:'/api/date', 
        data: [
        'first_date' => "2024-04-07 01:00:00",
        'second_date' => "2024-04-07 04:00:00",
        'type' => "days",
        'first_date_timezone' => "Australia/Adelaide",
        'second_date_timezone' => "Australia/Adelaide",
        'convert' => "hours"
      ]);

      $response->assertStatus(200);
      $response->assertJsonFragment(['Time difference' => '4 HOURS']);
    }
}
