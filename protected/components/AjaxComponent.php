<?php
namespace app\components;


use Yii;
use yii\base\Component;

class AjaxComponent extends Component
{

	public function init()
	{
	}

	/**
	 * Отправка ответа
	 * @param array $body
	 * @param int $status
	 * @param string $content_type
	 */
	public function sendResponse($body = [], $status = 200, $content_type = 'application/json')
	{
		@ob_clean();
		@header('HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status));
		@header('Content-type: ' . $content_type);

		if ( !is_array($body) )
			echo $body;
		else
			echo json_encode($body, JSON_NUMERIC_CHECK);

		Yii::$app->end();
	}

	/**
	 * Возвращает данные, пришедшие PUT запросом
	 * @return mixed
	 */
	public function getPutVars()
	{
		return json_decode(Yii::$app->request->getRawBody(), true);
	}

	/**
	 * Ответ об успешном завершении операции
	 * @param $data
	 */
	public function success($data = array())
	{
		$this->sendResponse(['success'=>true, 'data'=>$data]);
	}


	/**
	 * Ответ о не успешном завершении операции
	 * @param $data
	 */
	public function fail($data = array())
	{
		\Yii::warning(print_r($data,true));
		$this->sendResponse(['success'=>false, 'data'=>$data]);
	}

	/**
	 * Возвращает текстовое описание HTTP кода ответа
	 * @param $status
	 * @return string
	 */
	private function _getStatusCodeMessage($status)
	{
		$codes = [
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
		];
		return (isset($codes[$status])) ? $codes[$status] : '';
	}
}