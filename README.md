# PHP API client by HostingBE

HostingBE's API has many options for retrieving SEO data as well as for retrieving data about houses and information about ip addresses of your visitors. More options are being added all the time.

**Installing this API** 

`composer require hostingbe/php-api`

**Capabilities of this API**

First of all, requesting different data via REST API with the answer in JSON format. If the API gets an error, it tries the same command a number of times. There is also logging functionality standard in this app.


**Using the API for the first time** 
To use it you need an account with HostingBE, [Linkcreate it here(https://hostingbe.com/create-account)].

When you are logged in, click on API settings and enter the IP address where the API requests come from (whitelist)
After saving you will receive a username and password which you need when using API.

To connect to the API you need at least the following lines

```
use App\Api\Logging\ApiLogger;
use App\Api\HostingBE;
use GuzzleHttp\Exception\RequestException;
$logger = (new ApiLogger)->create('test-api');
$api = new HostingBE($logger);
$response = $api->login('username','password');
```

You now have an object with your JWT token in response.

Below are some commands that you can execute with this API.

**Search Google for PHP script**

`$response = $api->common('post','google/search',['q' => 'PHP script']);`

**Search Bing for top 10 websites**

`$response = $api->common('post','bing/search',['q' => 'top 10 websites']);` 

**Information about IP-address**

`$response = $api->common('get','ipinfo',['136.144.136.12']);` 


