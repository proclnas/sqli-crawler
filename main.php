<?php

/*
 * Main file - Automating a sqli Crawler
 * By Rodrigo 'Nasss'
 * 
 * http://n4sss.blogspot.com.br | http://janissaries.org
 * 
 */
 
set_time_limit(0);
error_reporting(E_ALL);

// Bing / sqli class
require 'bing.php';
require 'sqli.php';

function help($argv){
	
	$banner = "Automating a sqli crawler.
	Use:
	
	Bing scan:
	php {$argv[0]} -d dork -o uri_results
	
	Params:
	
	-d dork                 | dork to search into bing engine
	-o uri_results          | results from bing
	
	+-------------------------------------------------------------+
	
	Sqli scan:
	php {$argv[0]} -f uri_file -o output
	
	Params:
	
	-f uri_file            | File with uris to check sql message errors.
	-o output              | Result with possible vulns.
	
	By Rodrigo 'N4sss' | http://janissaries.org | http://n4sss.blogspot.com.br";
	
	return $banner;
}

// (-d (DORK) | -o (OUTPUT)) (-f (URI FILE TO SQLI SCAN))
$opt = getopt("d:o:f:");

// Bing scan
if(isset($opt['d'], $opt['o'])):
	if(isset($opt['f'])) exit(help($argv));
	$dork   = $opt['d'];
	$output = $opt['o'];
	$bing   = new BingScan($dork, $output);
	$bing->scan();
// Sqli scan
elseif(isset($opt['f'], $opt['o'])):
	if(isset($opt['d'])) exit(help($argv));
	$uri_file = $opt['f'];
	$output   = $opt['o'];
	$sqli     = new sqliCrawler($uri_file, $output);
	$sqli->crawler();
else:
	exit(help($argv));
endif;
