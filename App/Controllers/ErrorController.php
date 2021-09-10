<?php

namespace App\Controllers;

use Core\Controller;
use Core\Exception\HTTPException;
use Core\View;
use Throwable;

class ErrorController extends Controller
{

	private function page(int $code, string $msg)
	{
		return View::render("error.phtml", compact('code', 'msg'));
	}

	private function api(int $code, string $msg)
	{
		return [
			'success' => false,
			'code' => $code,
			'message' => $msg
		];
	}

	public function handleException(Throwable $e)
	{
		if ($e::class !== HTTPException::class) {
			if ($_ENV['ENVIRONMENT'] === "dev") {
				// We are in a dev environment and this is not an HTTP exception, re-throw the exception.
				throw $e;
			} else {
				// This is a production environment and something has gone wrong.
				// Log the error as it has been caught.
				$class = $e::class;
				$msg = $e->getMessage();
				$trace = $e->getTraceAsString();
				error_log("$class: $msg in $trace");

				// Re-throw the exception as a HTTPException.
				$e = new HTTPException(500);
			}
		}

		$code = $e->getCode();
		$msg = $e->getMessage();

		$this->response->responseCode = $code;

		if ($this->response->type === "HTML") {
			return $this->page($code, $msg);
		} else {
			return $this->api($code, $msg);
		}
	}
}
