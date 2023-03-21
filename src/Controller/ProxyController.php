<?php
namespace Src\Controller;


class ProxyController {

    private $requestMethod;
    private $proxyId;
    private $protocol;


    public function __construct($requestMethod,$proxyId,$protocol)
    {
        $this->requestMethod = $requestMethod;
        $this->protocol = $protocol;
        $this->proxyId = $proxyId;
        $this->accessCode = $_ENV['ACCESS_CODE'];

    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            
            case 'POST':

                if($this->proxyId == 0){
                    $response = $this->createProxy();
                }

                if($this->proxyId == 1){
                     $response = $this->lockProxy();
                }

                if($this->proxyId == 2){
                     $response = $this->unlockProxy();
                }

                break;

            case 'DELETE':

                $response = $this->deleteProxy();
                break;

            // case 'PUT':
                
            //     $response = $this->notFoundResponse();
            //     break;

            default:

                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }


    private function createProxy()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
 
        if(!$this->validateProxy($input)){
           return $this->unprocessableEntityResponse();
        }

        if (!$this->requiredAuth($input)) {
            return $this->unauthorizedResponse();
        }
 

        $output = exec('sudo /root/tool/adduser.sh '.$input["username"].' '.$input["password"].' '.$input["ip"].' '.$input["proxy_port"].' '.$input["socks_port"]);

        $response['status_code_header'] = 'HTTP/2.0 201 Created';
        $response['body'] = json_encode([
            'success' => true,
            'data' => $output
        ]);

        return $response;
    }

    private function updateProxy($id)
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(empty($input)){
           return $this->unprocessableEntityResponse();
        }

        $output = exec('sudo /root/tool/changeip.sh '.$input["username"].' '.$input["port"].' '.$input["pppnumber"]);

        if($output == 0 ){
            return $this->invaildRequest();
        }

        $response['status_code_header'] = 'HTTP/2.0 200 OK';
        $response['body'] =  json_encode([
            'success' => true,
            'data' => $output
        ]);

        return $response;
    }

    private function deleteProxy()
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(!$this->validateProxy($input)){
           return $this->unprocessableEntityResponse();
        }

        if (!$this->requiredAuth($input)) {
            return $this->unauthorizedResponse();
        }

        $output = exec('sudo /root/tool/deluser.sh '.$input["username"].' '.$input["password"].' '.$input["ip"].' '.$input["proxy_port"].' '.$input["socks_port"]);

        if($output == 0 ){
            return $this->invaildRequest();
        }

        $response['status_code_header'] = 'HTTP/2.0 200 OK';
        $response['body'] =  json_encode([
            'success' => true,
            'data' => $output
        ]);

        return $response;
    }

    private function lockProxy()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
 
        if(!$this->validateProxy($input)){
           return $this->unprocessableEntityResponse();
        }

        if (!$this->requiredAuth($input)) {
            return $this->unauthorizedResponse();
        }
 

        $output = exec('sudo /root/tool/lockuser.sh '.$input["username"].' '.$input["password"].' '.$input["ip"].' '.$input["proxy_port"].' '.$input["socks_port"]);

        $response['status_code_header'] = 'HTTP/2.0 202 Accepted';
        $response['body'] = json_encode([
            'success' => true,
            'data' => $output
        ]);

        return $response;
    }

    private function unlockProxy()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
 
        if(!$this->validateProxy($input)){
           return $this->unprocessableEntityResponse();
        }

        if (!$this->requiredAuth($input)) {
            return $this->unauthorizedResponse();
        }
 

        $output = exec('sudo /root/tool/unlockuser.sh '.$input["username"].' '.$input["password"].' '.$input["ip"].' '.$input["proxy_port"].' '.$input["socks_port"]);

        $response['status_code_header'] = 'HTTP/2.0 202 Accepted';
        $response['body'] = json_encode([
            'success' => true,
            'data' => $output
        ]);

        return $response;
    }



    private function requiredAuth($input)
    {
        if (empty($input['access_code'])){
            return false;
        }

        if(trim($input['access_code']) != $this->accessCode){
            return false;
        }

        return true;
    }

    private function validateProxy($input)
    {
        if (empty($input['username'])) {
            return false;
        }

        if (empty($input['password'])) {
            return false;
        }

        if (empty($input['ip'])) {
            return false;
        }

        if (empty($input['proxy_port'])) {
            return false;
        }

        if (empty($input['socks_port'])) {
            return false;
        }
        return true;
    }

    private function unauthorizedResponse()
    {
        $response['status_code_header'] = 'HTTP/2.0 401 Unauthorized';
        $response['body'] = json_encode([
            'success' => false,
            'message' => 'Invalid login'
        ]);
        return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/2.0 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'success' => false,
            'message' => 'Invalid input'
        ]);
        return $response;
    }

    private function invaildRequest()
    {
        $response['status_code_header'] = 'HTTP/2.0 400 Bad Request';
        $response['body'] = json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
        return $response;
    }


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/2.0 404 Not Found';
        $response['body'] = null;
        
        return $response;
    }
}
