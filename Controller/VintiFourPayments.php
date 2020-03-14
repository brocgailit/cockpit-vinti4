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

	public function fingerprint() {
		$data = base64_encode(hash("sha512", $this->config['posAutCode'], true));
		$data .= $_GET['timestamp'];
		$data .= $_GET['amount'] * 1000;
		$data .= $_GET['merchantRef'];
		$data .= $_GET['merchantSession'];
		$data .= $this->config['posID'];
		$data .= $_GET['currency'] ?? 132; // default to CV
		$data .= $_GET['transactionCode'] ?? 1; //default to 1. Product (2 = Service, 3 = Recharge)

		if ($_GET['entityCode']) {
			$data .= $_GET['entityCode'];
		}

		if ($_GET['referenceNumber']) {
			$data .= $_GET['referenceNumber'];
		}

		return base64_encode(hash("sha512", $data, true));
	}

	public function response() {
		return var_dump($_GET);
	}

}

?>