<?php

/*
* Automating a sqli crawler
* Sqli class
* 
* By Rodrigo 'Nasss'
* 
*/

set_time_limit(0);
error_reporting(0);

Class SqliCrawler{
	/**
	 * @var Uri file
	 */
	protected $uri_file;
	/**
	 * @var Words to be used in the pattern matching
	 */
	protected $common_errors;
	/**
	 * @var Output to save
	 */
	protected $log;
	/**
	 * @var Body Response
	 */
	protected $http_response;

	const DEFAULT_TIMEOUT = 10;
	
	public function __construct($uri_file, $log){
		$this->uri_file      = array_filter(preg_split('/\n|\r\n?/', file_get_contents($uri_file)));
		$this->common_errors = '/Mysql_|SQL|mysql_num_rows()|mysql_fetch_assoc()|mysql_result()|mysql_fetch_array()|mysql_numrows()|mysql_preg_match()/';
		$this->log           = $log;
		$this->user_agent    = '';
	}
	
	/**
	 * Get Method
	 *
	 * @param  string $uri
	 * @return sqliCrawler
	 */
	protected function get($uri){
		$ch = curl_init();

		curl_setopt_array($ch, [
			CURLOPT_URL            => sprintf('%s\'', $uri),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER     => ['Connection: close', 'Expect:'],
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0',
			CURLOPT_CONNECTTIMEOUT => self::DEFAULT_TIMEOUT,
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT
		]);

		$this->http_response = curl_exec($ch);
		return $this;
	}

	/**
	 * Checks if the uri match a querystring format
	 * and returns into a generator 
	 *
	 * @return string $uri
	 */
	protected function getUris() {
		foreach ($this->uri_file as $uri)
			if (strpos($uri, '=') !== false) yield $uri;
	}
	
	/**
	 * start method
	 * 
	 * @return sqliCrawler
	 */
	public function crawler(){
		echo 'Automating a sqli crawler.' . PHP_EOL,
		     '[] Sqli crawler module.'    . PHP_EOL; 
		
		foreach($this->getUris() as $uri):
			$this->get($uri);
			$msg = sprintf('[-] %s' . PHP_EOL, $uri);

			if(preg_match($this->common_errors, $this->http_response)):
				$msg = sprintf('[+] %s' . PHP_EOL, $uri);
				file_put_contents($this->log, $msg, FILE_APPEND);
			endif;
			
			echo $msg;
		endforeach;

		return $this;
	}
}
