<?php 

class Recaptcha
{
    public function __construct($keys = array())
    {
        $this->site_key = $keys['site_key'];
        $this->secret_key = $keys['secret_key'];
    }
    
    public function set()
    {
        if (isset($_POST['g-recaptcha-response'])) {
            return true;
        }
        
        return false;
    }
    
    
    public function render()
    {
    
        //Create the html code
        $html = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        $html .= '<div class="g-recaptcha" data-sitekey="'.$this->site_key.'"></div>';
            
        //return the html
        return $html;
    }
    
    public function verify($response)
    {
        
        //Get user ip
        $ip = $_SERVER['REMOTE_ADDR'];
        
        //Build up the url
       // $url = 'https://www.google.com/recaptcha/api/siteverify';
        //$full_url = $url.'?secret='.$this->secret_key.'&response='.$response.'&remoteip='.$ip;
    
		//$token = $_POST['token'];
	//	$action = $_POST['action'];
		 
		// call curl to POST request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $this->secret_key, 'response' => $response)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		error_log(print_r($response,true));
		curl_close($ch);
		$arrResponse = json_decode($response, true);
		error_log(print_r($arrResponse,true));
	
	
	
	
        //Get the response back decode the json
       // $data = json_decode(file_get_contents($full_url));
        
        //Return true or false, based on users input
        if ($arrResponse["success"] == '1') {
            return true;
        }
        
        return false;
    }
}
