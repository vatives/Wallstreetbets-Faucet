<?php
/*
                                        COPYRIGHT

Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

This file is part of JSON-RPC PHP.

JSON-RPC PHP is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

JSON-RPC PHP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with JSON-RPC PHP; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
/**
 * The object of this class are generic jsonRPC 1.0 clients
 * http://json-rpc.org/wiki/specification
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 */
class jsonRPCClient
{
       
        /**
         * Debug state
         *
         * @var boolean
         */
        private $debug = true;
       
        /**
         * The server URL
         *
         * @var string
         */
        private $url;
        /**
         * The request id
         *
         * @var integer
         */
        private $id;
        /**
         * If true, notifications are performed instead of requests
         *
         * @var boolean
         */
        private $notification = false;
       
        /**
         * Takes the connection parameters
         *
         * @param string $url
         * @param boolean $debug
         */
        public function __construct($url,$key, $debug = false)
        {
            // server URL
                $this->url = $url;
				// service password
				$this->paswordkey = $key;
                // proxy
                empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
                // debug state
                empty($debug) ? $this->debug = false : $this->debug = true;
                // message id
                $this->id = 1;
        }
       
        /**
         * Sets the notification state of the object. In this state, notifications are performed, instead of requests.
         *
         * @param boolean $notification
         */
        public function setRPCNotification($notification)
        {
            empty($notification) ?
                                                        $this->notification = false
                                                        :
                                                        $this->notification = true;
        }
       
        /**
         * Performs a http request and gets the results as an array
         *
         * @param string $method
         * @param array $params
         * @return array
         */
        public function __call($method, $params)
        {
               
                // check
                if (!is_scalar($method)) {
                    throw new Exception('Method name has no scalar value');
                }
               
                // check
                if (is_array($params)) {
                    // no keys
                        $params = array_values($params);
                } else {
                    throw new Exception('Params must be given as array');
                }
               
                // sets notification or request task
                if ($this->notification) {
                    $currentId = null;
                } else {
                    $currentId = $this->id;
                }
               
      
			$request = $params;
			
            if ($method == "send_transaction" or $method == "get_transaction" or $method == "transfer") {
                $request = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($request), ENT_NOQUOTES));
                $this->debug && $this->debug.='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";

                if (preg_match("/\"destinations\":{/i", $request)) {
                    $request = str_replace("\"destinations\":{", "\"destinations\":[{", $request);
                    $request = str_replace("},\"payment", "}],\"payment", $request);
                }
            } else {
                $request = json_encode($request, JSON_FORCE_OBJECT);
            }
		if ($method == "transfer") {
			$transactionAPI = "transactions/send/advanced";
			// performs the HTTP POST
			$ch = curl_init($this->url.$transactionAPI);
			error_log(print_r($ch,true));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','X-API-KEY: '.$this->paswordkey));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			
			
			
		}
		else{
              // performs the HTTP GET
            $ch = curl_init($this->url.$method);
			//	error_log(print_r($ch,true));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','X-API-KEY: '.$this->paswordkey));
        
			
		}
            $response = json_decode(curl_exec($ch), true);
			$info = curl_getinfo($ch);
			error_log(print_r($info,true));
			error_log(print_r($response,true));
			
            curl_close($ch);
               

                // debug output
                if ($this->debug) {
                    //echo nl2br($debug);
                }
               
                // final checks and return
                if (!$this->notification) {
                    if (!is_array($response)) {
                        echo "Can't connect to wallet. Please try again later.";
                        exit();
                    }

                    if (array_key_exists("error", $response)) {
                        return $response['error']['message'];
                    }
                       
                    return $response;
                } else {
                    return true;
                }
        }
}
