### for testing simply enter this command in terminal
    php ./vendor/bin/phpunit
 
### for running the project simply run this command
    php -S localhost:8000 -t ./public/


please check your machine should meets the below requirements:
- PHP >= 7.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension

For defining POI be careful the `radius` unit is base on kilometer. so if you want to convert it to meter, just divide it to 1000. for example :
 
if your radius is `200` meters so you should enter `0.20`

`200/1000 = 0.20`


For accessing to all of API's urls, Install postman from [this link](https://www.postman.com/downloads/) and then import [this project](https://github.com/hpakdaman/geo-application-test/blob/main/Geo%20Application.postman_collection.json) to postman.

 
