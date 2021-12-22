<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerApiCommonPreflight extends AControllerAPI
{

    public function main()
    {
        // This might require future imptovment. 
        if ($_SERVER["REQUEST_METHOD"] == 'OPTIONS') {
            $this->registry->get('response')->addHeader("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
            $this->registry->get('response')->addHeader("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
            $this->registry->get('response')->addHeader("Access-Control-Allow-Credentials: true");
            $this->registry->get('response')->addHeader("Access-Control-Max-Age: 60");

            $this->rest->sendResponse(200);
        }
    }
}



