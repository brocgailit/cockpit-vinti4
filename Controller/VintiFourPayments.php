<?php

namespace VintiFour\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use \LimeExtra\Controller;
use VintiFour\Controller\Endpoint;

class VintiFourPayments extends Controller {
	private $client;
	public $config;

	public function __construct($options) {
		parent::__construct($options);
		$this->config = $this->app['config']['vinti4'];
		$this->client = new Client([
			'base_uri' => "https://mc.vinti4net.cv/Client_VbV_v2/biz_vbv_clientdata.jsp"
		]);
	}
    
	public function index() {
		return 'Authorization Required';
	}

	public function payments() {
		$data = base64_encode(hash("sha512", $this->config['posAutCode'], true));
		$data .= $_POST['timestamp'];
		$data .= $_POST['amount'] * 1000;
		$data .= $_POST['merchantRef'];
		$data .= $_POST['merchantSession'];
		$data .= $this->config['posID'];
		$data .= $_POST['currency'] ?? 132; // default to CV
		$data .= $_POST['transactionCode'] ?? 1; //default to 1. Product (2 = Service, 3 = Recharge)

		if ($_POST['entityCode']) {
			$data .= $_POST['entityCode'];
		}

		if ($_POST['referenceNumber']) {
			$data .= $_POST['referenceNumber'];
		}

		$fingerprint = base64_encode(hash("sha512", $data, true));


		try {
			$res = $this->client->request('POST', '', [
				'form_params' => [
					'transactionCode' => $_POST['transactionCode'] ?? 1,
					'posID' => $this->config['posID'],
					'amount' => $_POST['amount'],
					'currency' => $_POST['currency'] ?? 132,
					'is3DSec' => 1,
					'urlMerchantResponse' => $this->config['urlMerchantResponse'],
					'fingerPrint' => $fingerprint,
					'fingerPrintVersion' => 1,
					'languageMessages' => 'en',
					'merchantRef' => $_POST['merchantRef'],
					'merchantSession' => $_POST['merchantSession'],
					'timestamp' => $_POST['timestamp'],
					'posAutCode' => $this->config['posAutCode'],
				]
			]);
			return $res->getBody();
		} catch(ClientException $e) {
				$response = $e->getResponse();
				return $response->getBody()->getContents();
		}
	}

	public function response() {
		return var_dump($_GET);
	}

}

?>