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

Test:
  1. cd date-time-api 
  2. php artisan test

Feature Improvement:
  1. using xml as request body if there are more request parameters.
  2. add authentication function, such as using hash_hmac function and preshared key