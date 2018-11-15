<?php

namespace App\Helpers;

use \Psr\Http\Message\ServerRequestInterface as Request;

class RequestParams
{
	protected $request;
	protected $paramData;

	public function __construct(Request $request)
	{
		$this->request = $request;

		if( $this->request->isGet() ){
			$this->getParamData = $this->request->getQueryParams();
			$this->paramData = $this->request->getQueryParams();
		}

		if( $this->request->isPost() ){
			$this->postParamData = $this->request->getParsedBody();
			$this->paramData = $this->request->getParsedBody();
		}

		if( $this->request->isPut() ){
			$this->putParamData = $this->request->getParsedBody();
			$this->paramData = $this->request->getParsedBody();
		}
	}

	public function get($name)
	{
		return $this->getParamData[$name];
	}

	public function post($name)
	{
		return $this->postParamData[$name];
	}

	public function put($name)
	{
		return $this->putParamData[$name];
	}

	public function all($name = ""){
		if($name){
			return $this->paramData[$name];
		}

		return $this->paramData;
	}

	public function __call($name, $arguments) {
        return call_user_func(array($this->request, $name), $arguments);
    }

    public function getRequest()
    {
    	return $this->request;
    }
}