<?php
 
/*
* Automating a sqli crawler
* Bing scan class
*
* By Rodrigo 'Nasss'
*
*/
 
set_time_limit(0);
error_reporting(0);
 
Class BingScan {
	/**
	 * @var string dork to search on bing engine
	 */
	 protected $dork;
	 /**
	  * @var string result
	  */
	 protected $log;
	 /**
	  * @var string body response
	  */
	 protected $http_response;
	 /**
	  * @var array info about request
	  */
	 protected $http_info;
	 /**
	  * @var int page limit to search
	  */
	 protected $limit_page;

	 // Default timeout
	 CONST DEFAULT_TIMEOUT = 10;
	 
	 /**
	  * Init
	  * 
	  * @param string $dork
	  * @param string $log
	  * @param int    $limit_page
	  */
	 public function __construct($dork, $log, $limit_page = 411){
		 $this->dork       = $dork;
		 $this->limit_page = $limit_page;
		 $this->regex      = "#href=\"(.*?)\">#"; // Simple , would be more specific
		 $this->log        = $log;
		 $this->uri_list   = [];
	 }
	 
	 /**
	  * Get request on bing
	  *
	  * @param  string $uri
	  * @return BingScan
	  */
	 protected function get($uri){
		$ch  = curl_init();
		curl_setopt_array($ch, [
		 	CURLOPT_URL            => $uri,
		 	CURLOPT_RETURNTRANSFER => TRUE,
		 	CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0',
		 	CURLOPT_HTTPHEADER     => ['Expect:', 'Connection: close', 'Content-type: application/x-www-form-urlencoded'],
		 	CURLOPT_CONNECTTIMEOUT => self::DEFAULT_TIMEOUT
		]);

		$this->http_response = curl_exec($ch);
		$this->http_info     = curl_getinfo($ch);

		return $this;
	 }
	 
	 /**
	  * Parse uris after get request
	  *
	  * @return BingScan
	  */
	 protected function parse_uris() {
		if(preg_match_all($this->regex, $this->http_response, $matches)):
			foreach($matches[1] as $uri):
				if($uri && strstr($uri, 'http://') && !preg_match('/msn|microsoft|php-brasil|facebook|4shared|bing|imasters|
										phpbrasil|php.net|yahoo|scriptbrasil|under-linux/', $uri) && !in_array($uri, $this->uri_list)):
					file_put_contents($this->log, strstr($uri, '"', true) . PHP_EOL, FILE_APPEND);
					$this->uri_list[] = $uri;
				endif;
			endforeach;
		endif;

		return $this;
	 }
	 
	 /**
	  * Init Crawler
	  *
	  * @return BingScan
	  */
	 public function scan(){
		echo 'Automating a sqli crawler.' . PHP_EOL,
		     '[] Bing scan module.' . PHP_EOL; 
		 
		$pointer = 1;
		
		while($pointer <= $this->limit_page):
			echo "\rDork: {$this->dork} Page: {$pointer}/411";

			$this->get("http://www.bing.com/search?q=".urlencode($this->dork)."&go=&filt=all&first={$pointer}")
			     ->parse_uris();
		   
			$pointer += 10;
		endwhile;

		return $this;
	 }
}
