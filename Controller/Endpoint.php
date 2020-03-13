<?php

namespace VintiFour\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

class Endpoint {
	public $config;
	private $client;

	public function __construct($config) {
		$this->config = $config;
		$this->client = new Client([
			'base_uri' => "https://mc.vinti4net.cv/Client_VbV_v2/biz_vbv_clientdata.jsp"
		]);
	}

	public function query($endpoint = '', $options = []) {
		$res = $this->client->request('GET', $endpoint, [
			'query' => Psr7\build_query($options)
		]);
		return json_decode($res->getBody(), true);
	}

	public function post($endpoint = '', $data) {
		try {
			$vendor = $this->config['vendorCode'];
			$date = gmdate('Y-m-d H:i:s');
			$message = strlen($vendor) . $vendor . strlen($date) . $date;
			$hash = hash_hmac('md5', $message, $this->config['secretKey']);
			$res = $this->client->request('POST', $endpoint, [
				'json' => $data,
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'X-Avangate-Authentication' => "code='{$vendor}' date='{$date}' hash='{$hash}'"
				]
			]);
			return json_decode($res->getBody(), true);
		} catch(ClientException $e) {
			$response = $e->getResponse();
			return $response->getBody()->getContents();
		}
	}

	public function renderResponse($res, $return_fn) {

		/* $status = $res->requestStatus;

		if ( !$status->success ) {
			return $status;		
		} */

		return $return_fn($res);
	}

}

?>