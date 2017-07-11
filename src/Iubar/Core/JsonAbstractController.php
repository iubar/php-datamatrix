<?php

namespace Iubar\Core;

use Iubar\Core\ResponseCode;

abstract class JsonAbstractController extends AbstractController {

	public function __construct(){
		parent::__construct();
		$this->app->response->header('Access-Control-Allow-Origin', '*');
		$this->app->response->header('Content-Type', 'application/json; charset=utf-8');
	}

	protected function getJsonDecodedFromPost(){
	    return json_decode($this->app->request()->getBody(), true); // make it a PHP associative array
	}

	protected function responseStatus($code, array $json_array = array(), $message = null){
	    if ($message === null){
	        if ($code === ResponseCode::SUCCESS){
	            $message = 'Request executed successfully';
	        } else if ($code === ResponseCode::INTERNAL_SERVER_ERROR){
	            $message = 'Runtime error';
	        } else if ($code === ResponseCode::BAD_REQUEST){
	            $message = 'Bad request';
	        }else{
	            $message = 'No description';
	        }
	    }

	    $response_array = array();
	    $response_array['code'] = $code;
	    $response_array['response'] = $message;

	    if ( $json_array !== null && count($json_array) > 0){
	        $response_array['data'] = $json_array;
	    }

	    $result = json_encode($response_array, JSON_PRETTY_PRINT);
	    if( $result === false ) {
	        $error = $this->getJsonEncodeError();
	        throw new \Exception($error . " (error code " . json_last_error() . ")");
	    } else {
	        $this->app->response->setStatus($code);
	        $this->app->response->write($result);
	    }
	}

	private function getJsonEncodeError(){
	    $error = null;
	    switch (json_last_error()) {
	        case JSON_ERROR_NONE:
	            $error = ' - No errors';
	            break;
	        case JSON_ERROR_DEPTH:
	            $error = ' - Maximum stack depth exceeded';
	            break;
	        case JSON_ERROR_STATE_MISMATCH:
	            $error = ' - Underflow or the modes mismatch';
	            break;
	        case JSON_ERROR_CTRL_CHAR:
	            $error = ' - Unexpected control character found';
	            break;
	        case JSON_ERROR_SYNTAX:
	            $error = ' - Syntax error, malformed JSON';
	            break;
	        case JSON_ERROR_UTF8:
	            $error = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
	            break;
	        default:
	            $error = ' - Unknown error';
	            break;
	    }
	    return $error;
	}

	protected function handleException($e){
		$this->responseStatus(ResponseCode::INTERNAL_SERVER_ERROR, array(), $e->getMessage()); // Senza questo statement, Slim restituirebbe un messaggio di errore in formato html
	}


}
