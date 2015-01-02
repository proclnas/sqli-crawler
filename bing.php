<?php
 
/*
* Automating a sqli crawler
* Bing scan class
*
* By Rodrigo 'Nasss'
*
*/
 
set_time_limit(0);
error_reporting(E_ALL);
 
Class BingScan {
	/**
	 * @var string dork | dork to search on bing engine
	 */
	 var $dork;
	 /**
	  * @var int timeout | timeout connection
	  */
	 var $timeout;
	 /**
	  * @var string user_agent | user_agent behavior
	  */
	 var $user_agent;
	 /**
	  * @var string log | result
	  */
	 var $log;
	 /**
	  * @var string http_response | body response
	  */
	 var $http_response;
	 /**
	  * @var array http_info | info about request
	  */
	 var $http_info;
	 /**
	  * @var int limit_page | page limit to search
	  */
	 var $limit_page;
	 
	 function __construct($dork, $log){
		 $this->dork       = urlencode($dork);
		 $this->user_agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0';
		 $this->limit_page = 411; // May be more
		 $this->regex      = "#href=\"(.*?)\">#"; // Simple , would be more specific
		 $this->log        = $log;
		 $this->uri_list   = array(); // To check repeated results
		 $this->timeout    = 10;
	 }
	 
	 /**
	  * Save content
	  */
	 function save_content(){
		 foreach($this->uri_list as $uri):
			file_put_contents($this->log, $uri."\n", FILE_APPEND);
		 endforeach;
	 }
	 
	 /**
	  * Get request on bing
	  * @param string $uri
	  */
	 function get($uri){
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $uri);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Connection: close'));
		 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		 $this->http_response = curl_exec($ch);
		 $this->http_info     = curl_getinfo($ch);
	 }
	 
	 /**
	  * Parse uris after get request
	  */
	 function parse_uris(){
		if(preg_match_all($this->regex, $this->http_response, $matches)):
			foreach($matches[1] as $uri):
				if($uri && strstr($uri, 'http://') && !preg_match('/msn|microsoft|php-brasil|facebook|4shared|bing|imasters|
										phpbrasil|php.net|yahoo|scriptbrasil|under-linux/', $uri) && !in_array($uri, $this->uri_list)):	
										
					$uri = strstr($uri, '"', true);
					$this->uri_list[] = $uri;
				endif;
			endforeach;
		endif;
	 }
	 
	 /**
	  * Init Crawler
	  */
	 function scan(){
		echo "Automating a sqli crawler.\n[] Bing scan module."; 
		 
		$pointer = 1;
		$label   = urldecode($this->dork);
		
		while($pointer <= $this->limit_page):
			   
			echo "\rDork: {$label} Page: {$pointer}/411";
			$uri  = "http://www.bing.com/search?q={$this->dork}&go=&filt=all&first={$pointer}";
			$this->get($uri);
		   
			// Parse uris from $this->http_response  / Aqui separamos as urls do nosso resultado ($this->http_response)
			$this->parse_uris();
		   
			$pointer += 10;
				
		endwhile;
		
		$this->save_content();
	 }
}
