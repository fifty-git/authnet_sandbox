<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


/**
* UpdatePaymentProfile
*/
class UpdatePaymentProfile
{
	
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $paymentCreditCard = null;
	private $paymentprofile = null;
	private $request = null;
	private $getRequest = null;
	private $refId = null;
	private $controller = null;
	private $response = null;
	private $errorMessages = null;
	private $returnArray = array();

	public function updatePaymentProfile($dataArray)
	{
		if($dataArray) {
			$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
			$this->merchantAuthentication->setName(Constants::getAPILogin());
			$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
			$this->refId = 'ref' . time();

			//Set profile ids of profile to be updated
			$this->request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
			$this->request->setMerchantAuthentication($this->merchantAuthentication);
			$this->request->setCustomerProfileId($dataArray['customer_profile_id']);
			$this->controller = new AnetController\GetCustomerProfileController($this->request);	

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

			$this->paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
			$this->paymentprofile->setCustomerPaymentProfileId($dataArray['payment_profile_id']);
			$this->paymentprofile->setBillTo($this->billto);
			$this->paymentprofile->setPayment($this->paymentCreditCard);

			$this->request->setPaymentProfile($this->paymentprofile );

			$this->controller = new AnetController\UpdateCustomerPaymentProfileController($request);
			$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
			if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
			{
				//echo "Update Customer Payment Profile SUCCESS: " . "\n";

				 // Update only returns success or fail, if success
				 // confirm the update by doing a GetCustomerPaymentProfile
				$this->getRequest = new AnetAPI\GetCustomerPaymentProfileRequest();
				$this->getRequest->setMerchantAuthentication($this->merchantAuthentication);
				$this->getRequest->setRefId($this->refId);
				$this->getRequest->setCustomerProfileId($dataArray['customer_profile_id']);
				$this->getRequest->setCustomerPaymentProfileId($dataArray['payment_profile_id']);

				$this->controller = new AnetController\GetCustomerPaymentProfileController($this->getRequest);
				$this->response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
				if(($this->response != null)){
					if ($this->response->getMessages()->getResultCode() == "Ok")
					{
						echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
						echo "Customer Payment Profile Id: " . $this->response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
						echo "Customer Payment Profile Billing Address: " . $this->response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
					}
					else
					{
						echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
						$this->errorMessages = $this->response->getMessages()->getMessage();
						echo "Response : " . $this->errorMessages[0]->getCode() . "  " .$this->errorMessages[0]->getText() . "\n";
					}
				}
				else{
					echo "NULL Response Error";
				}

			}
			else
			{
				echo "Update Customer Payment Profile: ERROR Invalid response\n";
				$this->errorMessages = $this->response->getMessages()->getMessage();
				echo "Response : " . $this->errorMessages[0]->getCode() . "  " .$this->errorMessages[0]->getText() . "\n";
			}


		}

	}










}