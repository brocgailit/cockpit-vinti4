<?php

namespace VintiFour\Controller;

use GuzzleHttp\Client;
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
		$data .= $_POST['currency'] || 132; // default to CV
		$data .= $_POST['transactionCode'] || 1; //default to 1. Product (2 = Service, 3 = Recharge)

		if ($_POST['entityCode']) {
			$data .= $_POST['entityCode'];
		}

		if ($_POST['referenceNumber']) {
			$data .= $_POST['referenceNumber'];
		}

		return base64_encode(hash("sha512", $data, true));
	}

	public function response() {
		return 'Why hello there!';
	}

}

?>