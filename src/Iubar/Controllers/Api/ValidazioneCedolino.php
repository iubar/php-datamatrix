<?php

namespace Iubar\Controllers\Api;

use Iubar\Core\JsonAbstractController;
use Iubar\Core\ResponseCode;
use Iubar\Services\CedolinoService;

class ValidazioneCedolino extends JsonAbstractController {

	public function post(){
		try {
			$json = $this->getJsonDecodedFromPost();
			$this->responseStatus(ResponseCode::SUCCESS, CedolinoService::check($json));
		} catch (\RuntimeException $e) {
		    $this->responseStatus(ResponseCode::BAD_REQUEST, [], $e->getMessage());
		} catch (\Exception $e) {
			$this->handleException($e);
		}
	}

}