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

	public function payment() {

		//fallback vars
		$fingerPrintVersion = $_GET['fingerPrintVersion'] ?? 1;
		$currency = $_GET['curency'] ?? 132;
		$is3DSec = $_GET['is3DSec'] ?? 1;
		$language = $_GET['language'] ?? 'en';

		//form setup
		$formUrl = 'https://mc.vinti4net.cv/Client_VbV_v2/biz_vbv_clientdata.jsp?';
		$formUrl .= "FingerPrint={$_GET['fingerPrint']}";
		$formUrl .= "&TimeStamp={$_GET['timestamp']}";
		$formUrl .= "&FingerPrintVersion={$fingerPrintVersion}";

		//page setup
		$page = '<html>';
		$page .= '<head>';
		$page .= '  <title>Vinti4 Payment</title>';
		$page .= '</head>';
		$page .= '<body>';
		$page .= "  <form id=\"vintiPaymentForm\" action=\"{$formUrl}\">";
		$page .= "    <input type=\"hidden\" name=\"transactionCode\" value=\"1\" />";
		$page .= "    <input type=\"hidden\" name=\"posID\" value=\"{$this->config['posID']}\" />";
		$page .= "    <input type=\"hidden\" name=\"amount\" value=\"{$_GET['amount']}\" />";
		$page .= "    <input type=\"hidden\" name=\"currency\" value=\"{$currency}\" />";
		$page .= "    <input type=\"hidden\" name=\"is3DSec\" value=\"{$is3DSec}\" />";
		$page .= "    <input type=\"hidden\" name=\"urlMerchantResponse\" value=\"{$_GET['urlMerchantResponse']}\" />";
		$page .= "    <input type=\"hidden\" name=\"fingerPrint\" value=\"{$_GET['fingerPrint']}\" />";
		$page .= "    <input type=\"hidden\" name=\"fingerPrintVersion\" value=\"{$fingerPrintVersion}\" />";
		$page .= "    <input type=\"hidden\" name=\"languageMessages\" value=\"{$language}\" />";
		$page .= "    <input type=\"hidden\" name=\"merchantRef\" value=\"{$_GET['merchantRef']}\" />";
		$page .= "    <input type=\"hidden\" name=\"merchantSession\" value=\"{$_GET['merchantSession']}\" />";
		$page .= "    <input type=\"hidden\" name=\"timestamp\" value=\"{$_GET['timestamp']}\" />";
		$page .= '  </form>';
		$page .= '  Loading...';
		$page .= '  <script>document.getElementById("vintiPaymentForm").submit()</script>';
		$page .= '</body>';
		$page .= '</html>';

		return $page;
	}

	public function response() {
		return var_dump($_GET);
	}

}

?>