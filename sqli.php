<?php

/*
* Automating a sqli crawler
* Sqli class
* 
* By Rodrigo 'Nasss'
* 
*/

set_time_limit(0);
error_reporting(E_ALL);

Class sqliCrawler{
	/**
	 * @var string
	 */
	var $uri_file;
	/**
	 * @var string|regex
	 */
	var $common_messages;
	/**
	 * @var string
	 */
	var $log;
	/**
	 * @var string
	 */
	var $user_agent;
	/**
	 * @var int
	 */
	var $timeout;
	/**
	 * @var string
	 */
	var $http_response;
	/**
	 * @var array
	 */
	var $uris;
	
	function __construct($uri_file, $log){
		$this->uri_file        = $uri_file;
		$this->common_messages = '/Mysql_|SQL|mysql_num_rows()|mysql_fetch_assoc()|mysql_result()|mysql_fetch_array()|mysql_numrows()|mysql_preg_match()/';
		$this->log             = $log;
		$this->user_agent      = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0';
		$this->timeout         = 10;
		$this->uris            = array();
	}
	
	function get($uri){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close', 'Expect:'));
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		$this->http_response = curl_exec($ch);
	}
	
	/**
	 * Parse uri callback method | add ' string
	 */
	function add_string($item){
		if(strstr($item, '=')):
			$this->uris[] = $item."'";
		endif;
	}
	
	/**
	 * start method
	 */
	function crawler(){
		echo "Automating a sqli crawler.\n[] Sqli crawler module.\n"; 
		
		// Prepare uris with (') to test in $this->uris
		$tmp = array_filter(explode("\n", file_get_contents($this->uri_file)));
		array_walk($tmp, array($this, 'add_string'));
		
		foreach($this->uris as $uri):
			$this->get($uri);
			$msg = sprintf("[-] %s\n", $uri);
			if(preg_match($this->common_messages, $this->http_response)):
				$msg = sprintf("[+] %s\n", $uri);
				file_put_contents($this->log, $msg, FILE_APPEND);
			endif;
			
			echo $msg;
		endforeach;
	}
}
