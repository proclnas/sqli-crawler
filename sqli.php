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
     * @var array Uri file
     */
    protected $uri_file;
    
    /**
     * @var string Words to be used in the pattern matching
     */
    protected $common_errors;
    
    /**
     * @var string Output to save
     */
    protected $log;
    
    /**
     * @var string Body Response
     */
    protected $http_response;
    
    /**
     * @var string Context user-agent
     */
    protected $user_agent;
    
    const DEFAULT_TIMEOUT = 10;

    public function __construct($uri_file, $log){
        $this->uri_file      = array_filter(preg_split('/\n|\r\n?/', file_get_contents($uri_file)));
        $this->common_errors = '/Mysql_|SQL|mysql_num_rows()|mysql_fetch_assoc()|mysql_result()|mysql_fetch_array()|mysql_numrows()|mysql_preg_match()/';
        $this->log           = $log;
        $this->user_agent    = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0';
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
            CURLOPT_URL            => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Connection: close', 'Expect:'],
            CURLOPT_USERAGENT      => $this->user_agent,
            CURLOPT_CONNECTTIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT
        ]);
        
        $this->http_response = curl_exec($ch);
        return $this;
    }

    /**
    * Checks if the uri match a query-string format
    * and returns a generator.
    *
    * - Added sqli payload to all query params
    *
    * @return string $uri
    */
    protected function getUris() {
        foreach ($this->uri_file as $uri) {
            if (strpos($uri, '=') !== false) {
                if (strpos($uri, '&') !== false) {
                    $query       = '';
                    $uri_map     = parse_url($uri);
                    $query_array = explode('&', $uri_map['query']);
                    
                    foreach ($query_array as $i => $fragment) {
                        if ($i + 1 < count($query_array)) {
                            $query .= sprintf("%s'&", $fragment);
                        } else $query .= sprintf("%s'", $fragment);
                    }
                    
                    $host = isset($uri_map['port']) ? sprintf('%s:%s', $uri_map['host'], $uri_map['port'])
                                                    : $uri_map['host'];
                    
                    $uri = sprintf(
                        '%s://%s%s?%s',
                        $uri_map['scheme'],
                        $host,
                        $uri_map['path'],
                        $query
                    );
                }
            }
            
            yield $uri;
        }
    }

    /**
     * start method
     *
     * @return sqliCrawler
     */
    public function crawler(){
        echo 'Automating a sqli crawler.' . PHP_EOL,
             '[] Sqli crawler module.' . PHP_EOL;
        
        foreach($this->getUris() as $uri) {
            $this->get($uri);
            $msg = sprintf('[-] %s' . PHP_EOL, $uri);
            
            if (preg_match($this->common_errors, $this->http_response)) {
                $msg = sprintf('[+] %s' . PHP_EOL, $uri);
                file_put_contents(
                    $this->log,
                    $msg,
                    FILE_APPEND
                );
            }
            
            echo $msg;
        }
        
        return $this;
    }
}
