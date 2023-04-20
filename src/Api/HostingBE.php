<?php

/**
 * @author Constan van Suchtelen van de Haere <constan@hostingbe.com>
 * @copyright 2023 HostingBE
 */

namespace hostingbe\phpapi;

class HostingBE extends Api {

protected $codes = array('404','500','501','502','503');

/**
 *  Execute the login command with username and password
 */
public function login($username, $password) {
        $form = array('form_params' => array('username' => $username, 'password' => $password));
        $response = $this->getUri('POST','auth/login',$form);
        /** 
        * Login succesfull save the token
        */
        if ($response->data->code == "20000") {
        $this->settoken($response->data->data->token);
        }
        return $response;
}

/**
*  Common API interface handling the get and post requests
*/
public function common(string $method, string $command, array $params = []) :object {
        
        $method = strtolower($method);

        $this->checkmethod($method);

        if ($method == 'get') {
        $response = $this->getUri($method, $command.$this->paramstostring($params), ['headers' => ['Authorization' => 'Bearer ' . $this->gettoken()]]);     
        }
        if ($method == "post") {
        $response = $this->getUri($method, $command.$paramsstr, ['headers' => ['Authorization' => 'Bearer ' . $this->gettoken()], 'form_params' => $params]);   
        }
        return $response;
}
/**
 *  check the method
 */
protected function checkmethod($method) {
    
    if (!in_array($method, array('get','post'))) {
        throw new \Exception("invalid method received!");
    }
}
/**
 *  create the get extra parameters string
 */
protected function paramstostring($params) {
$paramsstr = '';

if (count($params)!= 0) {
$paramsstr = "/".implode("/",$params);
}
return $paramsstr;
}

/**
 * Get the current active token
 */
protected function gettoken() :string {
        return $this->token;
}
/**
 * Set the current active token
 */
protected function settoken($token) {
        $this->token = $token;
}

/**
* Decode output JSON to an object
*/
protected function output($output) {
        return json_decode($output);
}

/**
* Get the requested URI and return the response
*/
protected function getUri(string $method, string $uri,array $form = array()) :object {
       $response = $this->client->request($method, $uri, $form);
       $response->getBody()->rewind();

       if (in_array($response->getStatusCode(),$this->codes)) {
       return (object) array('code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase()); 
       } else {
       return (object) array('code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase(),'data' => $this->output($response->getBody()->getContents()));
       }
    }
}
?>