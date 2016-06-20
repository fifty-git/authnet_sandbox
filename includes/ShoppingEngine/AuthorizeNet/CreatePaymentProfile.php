<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


/**
* CreatePaymentProfile
*/
class CreatePaymentProfile
{
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $paymentCreditCard = null;
	private $billto = null;
	private $paymentprofile = null;
	private $paymentprofilerequest = null;
	private $refId = null;
	private $controller = null;
	private $response = null;
	private $paymentprofiles = array();
	
	public function createPaymentProfile($dataArray)
	{
		$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$this->merchantAuthentication->setName(Constants::getAPILogin());
		$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
		$this->refId = 'ref' . time();

		$this->creditCard = new AnetAPI\CreditCardType();
		$this->creditCard->setCardNumber($dataArray['cardNumber']);
		$this->creditCard->setExpirationDate($dataArray['expirationDate']);
		$this->creditCard->setCardCode($dataArray['cardCode']);
		$this->paymentCreditCard = new AnetAPI\PaymentType();
		$this->paymentCreditCard->setCreditCard($this->creditCard);

		$this->billto = new AnetAPI\CustomerAddressType();
		$this->billto->setFirstName($dataArray['firstName']);
		$this->billto->setLastName($dataArray['lastName']);
		$this->billto->setCompany($dataArray['company']);
		$this->billto->setAddress($dataArray['address']);
		$this->billto->setCity($dataArray['city']);
		$this->billto->setState($dataArray['state']);
		$this->billto->setZip($dataArray['zip']);
		$this->billto->setPhoneNumber($dataArray['phoneNumber']);
		$this->billto->setfaxNumber("");
		$this->billto->setCountry($dataArray['country']);

		$this->paymentprofile = new AnetAPI\CustomerPaymentProfileType();
		$this->paymentprofile->setCustomerType('individual');
		$this->paymentprofile->setBillTo($this->billto);
		$this->paymentprofile->setPayment($this->paymentCreditCard);

		$this->paymentprofiles[] = $this->paymentprofile;

		$this->paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
		$this->paymentprofilerequest->setMerchantAuthentication($this->merchantAuthentication);

		$this->paymentprofilerequest->setCustomerProfileId($dataArray['customerProfileId']);
		$this->paymentprofilerequest->setPaymentProfile( $this->paymentprofile );
		$this->paymentprofilerequest->setValidationMode("liveMode");
		$this->controller = new AnetController\CreateCustomerPaymentProfileController($this->paymentprofilerequest);
		$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

		if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
		{
			$returnArray['payment_profile_id'] = $this->response->getCustomerPaymentProfileId();
		}
		else
		{
			$returnArray['payment_profile_id'] = null;
			$errorMessages = $this->response->getMessages()->getMessage();
			$returnArray['code'] = $errorMessages[0]->getCode();
			$returnArray['error_message'] = $errorMessages[0]->getText();

		}
		return $returnArray;

	}
}