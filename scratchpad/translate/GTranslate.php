<?php

/**
* GTranslate - A class to comunicate with Google Translate Service
*               Google Translate API Wrapper
*               More info about Google service can be found on http://code.google.com/apis/ajaxlanguage/documentation/reference.html
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @author Jose da Silva <jose@josedasilva.net>
* @since 2009/01/09
* @version 0.7
* @licence LGPL v3
*
* <code>
* <?
* require_once("GTranslate.php");
* try{
*	$gt = new Gtranslate;
*	echo $gt->english_to_german("hello world");
* } catch (GTranslateException $ge)
* {
*	echo $ge->getMessage();
* }
* ?>
* </code>
*/


/**
* Exception class for GTranslated Exceptions
*/

class GTranslateException extends Exception
{
	public function __construct($string) {
		parent::__construct($string, 0);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

class GTranslate
{
	/**
	* Google Translate Api endpoint
	* @access private
	* @var String 
	*/
	private $url = "http://ajax.googleapis.com/ajax/services/language/translate";
	
        /**
        * Google Translate Api Version
        * @access private
        * @var String 
        */	
	private $api_version = "1.0";

        /**
        * Comunication Transport Method
 	* Available: http / curl
        * @access private
        * @var String 
        */
	private $request_type = "http";

        /**
        * Path to available languages file
        * @access private
        * @var String 
        */
	private $available_languages_file 	= "languages.ini";
	
        /**
        * Holder to the parse of the ini file
        * @access private
        * @var Array
        */
	private $available_languages = array();

        /**
        * Constructor sets up {@link $available_languages}
        */
	public function __construct()
	{
		$this->available_languages = parse_ini_file("languages.ini");
	}

        /**
        * URL Formater to use on request
        * @access private
        * @param array $lang_pair
	* @param array $string
	* "returns String $url
        */

	private function urlFormat($lang_pair,$string)
	{
		$parameters = array(
			"v" => $this->api_version,
			"q" => $string,
			"langpair"=> implode("|",$lang_pair)
		);

		$url  = $this->url."?";

		foreach($parameters as $k=>$p)
		{
			$url 	.=	$k."=".urlencode($p)."&";
		}
		return $url;
	}

	
        /**
        * Query the Google endpoint 
        * @access private
        * @param array $lang_pair
        * @param array $string
        * returns String $response
        */

	public function query($lang_pair,$string)
	{
		$query_url = $this->urlFormat($lang_pair,$string);
		$response = $this->{"request".ucwords($this->request_type)}($query_url);
		return $response;
	}

        /**
        * Query Wrapper for Http Transport 
        * @access private
        * @param String $url
        * returns String $response
        */

	private function requestHttp($url)
	{
		return GTranslate::evalResponse(json_decode(file_get_contents($url)));
	}

        /**     
        * Query Wrapper for Curl Transport 
        * @access private
        * @param String $url
        * returns String $response
        */

	private function requestCurl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER["HTTP_REFERER"]);
		$body = curl_exec($ch);
		curl_close($ch);		
		return GTranslate::evalResponse(json_decode($body));
	}

        /**     
        * Response Evaluator, validates the response
	* Throws an exception on error 
        * @access private
        * @param String $json_response
        * returns String $response
        */

	private function evalResponse($json_response)
	{
		switch($json_response->responseStatus)
		{
			case 200:
				return $json_response->responseData->translatedText;
				break;
			default:
				throw new GTranslateException("Unable to perform Translation:".$json_response->responseDetails);
			break;
		}
	}


        /**     
        * Validates if the language pair is valid
        * Throws an exception on error 
        * @access private
        * @param Array $languages
        * returns Array $response Array with formated languages pair
        */

	private function isValidLanguage($languages)
	{
		$language_list 	= $this->available_languages;

		$languages 		= 	array_map( "strtolower", $languages );
		$language_list_v  	= 	array_map( "strtolower", array_values($language_list) );
		$language_list_k 	= 	array_map( "strtolower", array_keys($language_list) );
		$valid_languages 	= 	false;
		if( TRUE == in_array($languages[0],$language_list_v) AND TRUE == in_array($languages[1],$language_list_v) )
		{
			$valid_languages 	= 	true;	
		}

		if( FALSE === $valid_languages AND TRUE == in_array($languages[0],$language_list_k) AND TRUE == in_array($languages[1],$language_list_k) )
		{
			$languages 	= 	array($language_list[strtoupper($languages[0])],$language_list[strtoupper($languages[1])]);
			$valid_languages        =       true;
		}

		if( FALSE === $valid_languages )
		{
			throw new GTranslateException("Unsupported languages (".$languages[0].",".$languages[1].")");
		}

		return $languages;
	}

        /**     
        * Magic method to understande translation comman
	* Evaluates methods like language_to_language
        * @access public
	* @param String $name
        * @param Array $args
        * returns String $response Translated Text
        */


	public function __call($name,$args)
	{
		$languages_list 	= 	explode("_to_",strtolower($name));
		$languages = $this->isValidLanguage($languages_list);

		$string 	= 	$args[0];
		return $this->query($languages,$string);
	}
}

?>
