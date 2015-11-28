<?php

/*
 * Main file - Automating a sqli Crawler
 * By Rodrigo 'Nasss'
 * 
 * http://n4sss.blogspot.com.br | http://janissaries.org
 * 
 * Require: php5-curl
 */
 
set_time_limit(0);
error_reporting(0);

// Bing / sqli class
require 'bing.php';
require 'sqli.php';

function help(){
	
	return 'Automating a sqli crawler.
	
	Bing scan:
	php main.php -d dork -o uri_results
	
	Params:
	
	-d dork        | dork to search into bing engine
	-o uri_results | results from bing
	
	+-------------------------------------------------------------+
	
	Sqli scan:
	php main.php -f uri_file -o output
	
	Params:
	
	-f uri_file | File with uris to check sql message errors.
	-o output   | Result with possible vulns.
	
By Rodrigo "Nas"
- http://janissaries.org
- http://n4sss.blogspot.com.br' . PHP_EOL;

}

/**
 * To launch bing scan: php main.php -d (DORK) -o (OUTPUT)
 * To launch sqli scan: php main.php -f (URI FILE TO SQLI SCAN) -o (OUTPUT)
 */
$opt = getopt("d:o:f:");

// Bing scan
if(isset($opt['d'], $opt['o'])):
	if(isset($opt['f'])) exit(help());

	$dork   = $opt['d'];
	$output = $opt['o'];

	$bing = new BingScan($dork, $output);
	$bing->scan();
	exit;
elseif(isset($opt['f'], $opt['o'])):
	if(isset($opt['d'])) exit(help());

	$uri_file = $opt['f'];
	$output   = $opt['o'];

	if (!file_exists($uri_file)) exit("File {$uri_file} not found! Exiting..." . PHP_EOL);

	$sqli = new SqliCrawler($uri_file, $output);
	$sqli->crawler();
	exit;
endif;

exit(help());
