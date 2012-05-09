<?php
class google extends command {

	public function __construct(){
		$this->name = 'google';
		$this->public = true;
	}
	
	public function help(){
		return "Uso: !google <criterio de búsqueda> . Devuelve los 3 primeros resultados que arroja google.";
	}
	
	
	
	/**
	 * 
	 * The following function was taken from 
	 * http://w-shadow.com/blog/2009/01/05/get-google-search-results-with-php-google-ajax-api-and-the-seo-perspective/
	 * 
	 * google_search_api()
	 * Query Google AJAX Search API
	 *
	 * @param array $args URL arguments. For most endpoints only "q" (query) is required.
	 * @param string $referer Referer to use in the HTTP header (must be valid).
	 * @param string $endpoint API endpoint. Defaults to 'web' (web search).
	 * @return object or NULL on failure
	 */
	protected function google_search_api($args, $referer = 'http://www.linux-mx./search/', $endpoint = 'web'){
	    $url = "http://ajax.googleapis.com/ajax/services/search/".$endpoint;
	 
	    if ( !array_key_exists('v', $args) ){
	        $args['v'] = '1.0';
	    }
	 
	    $url .= '?'.http_build_query($args, '', '&');
	 
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // note that the referer *must* be set
	    curl_setopt($ch, CURLOPT_REFERER, $referer);
	    $body = curl_exec($ch);
	    curl_close($ch);
	    //decode and return the response
	    return json_decode($body);
	}	
	
	
	public function process($args){		
		$this->output = "";
		$args = trim($args);
		$args = str_replace(" ", "+",$args);
				
		$rez = $this->google_search_api( array('q' => $args) );
		 
		$results = $rez->responseData->results;
		if ( empty($results)){
			$this->output = 'No results, sorry.';
		} else {
			$count = 0;
			foreach ($results as $result){
				$this->output .= $result->url . "\n";
				$count++;
				if ( $count >= 3 ) break;
			}
		}		
	}
}


		 