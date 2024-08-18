# Date-Time-API
This is an API to calculate the days between time period.

To run this project successfully, you need 
  1. PHP 8.2
  2. open extension=fileinfo in php.ini
  3. Composer

Start the serve:
  1. cd date-time-api 
  2. composer install --no-scripts
  3. php artisan serve

Download the date-time-api collection from postman-collection folder and import to postman to do the testing.

Request:

| Component | Type | Format | Description |
| --- | --- | --- | --- |
| first_date | Compulsory | yyyy-mm-dd HH:mm:ss | The first date used to calculate the time difference | 
| second_date | Compulsory | yyyy-mm-dd HH:mm:ss | The second date used to calculate the time difference |
| first_date_timezone | Optional | Text | Default value: Australia/Adelaide, using to setup the first date timezone |
| second_date_timezone | Optional | Text | Default value: Australia/Adelaide, using to setup the second date timezone |
| type | Optional | Text | Default value: Days. Options: Days, Weekdays, Weeks. |
| convert | Optional | Text | Default value: Days. Options: Days, Hours, Minites, Seconds, Years |

Response:
| Component | Type | Format | Description |
| --- | --- | --- | --- |
| status | Compulsory | Boolean | False: invalid Date, True: successful request | 
| code | Compulsory | Number | The second date used to calculate the time difference |
| First Date | Optional | Date | Fisrt date from the request |
| First Date Timezone | Optional | Text | Fisrt date timezone from the request  |
| Second Date | Optional | Date | Second date from the request |
| Second Date Timezone | Optional | Text | Second date timezone from the request  |
| Request Type | Optional | Text | Request Type from the request  |
| Result Convert To | Optional | Text | Result will convert to options |
| Time difference | Optional | Text | Final Result |


Test:
  1. cd date-time-api 
  2. php artisan test

Feature Improvement:
  1. using xml as request body if there are more request parameters.
  2. add authentication function, such as using hash_hmac function and preshared key