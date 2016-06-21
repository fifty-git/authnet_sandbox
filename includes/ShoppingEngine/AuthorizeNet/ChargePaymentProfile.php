<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
* ChargePaymentProfile
*/
class ChargePaymentProfile
{
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $profileToCharge = null;
	private $paymentprofile = null;
	private $transactionRequestType = null;
	private $refId = null;
	private $request = null;
	private $controller = null;
	private $response = null;
	private $tresponse = null;
	private $returnArray = array();

	public function chargePaymentProfile($profileid, $paymentprofileid, $amount)
	{
		$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$this->merchantAuthentication->setName(Constants::getAPILogin());
		$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
		$this->refId = 'ref' . time();

		$this->profileToCharge = new AnetAPI\CustomerProfilePaymentType();
		$this->profileToCharge->setCustomerProfileId($profileid);
		$this->paymentProfile = new AnetAPI\PaymentProfileType();
		$this->paymentProfile->setPaymentProfileId($paymentprofileid);
		$this->profileToCharge->setPaymentProfile($this->paymentProfile);

		$this->transactionRequestType = new AnetAPI\TransactionRequestType();
		$this->transactionRequestType->setTransactionType("authCaptureTransaction"); 
		$this->transactionRequestType->setAmount($amount);
		$this->transactionRequestType->setProfile($this->profileToCharge);

		$this->request = new AnetAPI\CreateTransactionRequest();
		$this->request->setMerchantAuthentication($this->merchantAuthentication);
		$this->request->setRefId($this->refId);
		$this->request->setTransactionRequest($this->transactionRequestType);
		$this->controller = new AnetController\CreateTransactionController($this->request);

		$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
		if ($this->response != null)
		{
			$this->tresponse = $this->response->getTransactionResponse();
			if (($this->tresponse != null) && ($this->tresponse->getResponseCode() == 1) )   
			{
				$this->returnArray['error'] = false;
				$this->returnArray['auth_code'] = $this->tresponse->getAuthCode();
				$this->returnArray['trans_id'] = $this->tresponse->getTransId();
			}
			elseif ($this->tresponse != null)
			{
					$this->errors = $this->tresponse->getErrors(); // returns an array that holds an object
					$this->returnArray['error'] = true;
					$this->returnArray['error_code'] = $this->errors[0]->getErrorCode();
					$this->returnArray['error_message'] = $this->errors[0]->getErrorText();
			}
		}
		else
		{
				$this->returnArray['error'] = true;
				$this->returnArray['error_code'] = false;
		}
		return $this->returnArray;


	}
}