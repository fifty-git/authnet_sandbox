<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


/**
* CreatePaymentProfile
*/
class CreatePaymentProfileCharge
{
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $paymentCreditCard = null;
	private $transactionRequestType = null;
	private $billto = null;
	private $paymentprofile = null;
	private $paymentprofilerequest = null;
	private $refId = null;
	private $controller = null;
	private $response = null;
	private $tresponse = null;
	private $paymentprofiles = array();
	private $order = null;
	private $custpaymentprofile = null;
	private $returnArray = array();
	
	public function createPaymentProfileCharge($dataArray)
	{
		if($dataArray) {
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
				$this->returnArray['payment_profile_id'] = $this->response->getCustomerPaymentProfileId();
			}
			else
			{
				$this->returnArray['payment_profile_id'] = null;
				$errorMessages = $this->response->getMessages()->getMessage();
				$this->returnArray['code'] = $errorMessages[0]->getCode();
				$this->returnArray['error_message'] = $errorMessages[0]->getText();

			}
			$this->order = new AnetAPI\OrderType();
			$this->order->setInvoiceNumber($dataArray['invoice_id']);
			$this->order->setDescription($dataArray['order_description']);

			$this->customer = new AnetAPI\CustomerDataType();
			$this->customer->setId($dataArray['merchantCustomerId']);
			$this->customer->setEmail($dataArray['email']);

			$this->custpaymentprofile = new AnetAPI\CustomerProfilePaymentType();
			//$this->custpaymentprofile->setCreateProfile(TRUE);

			$this->transactionRequestType = new AnetAPI\TransactionRequestType();
			$this->transactionRequestType->setTransactionType( "authCaptureTransaction"); 
			$this->transactionRequestType->setAmount($dataArray['amount']);
			$this->transactionRequestType->setPayment($this->paymentCreditCard);
			$this->transactionRequestType->setOrder($this->order);
			$this->transactionRequestType->setPoNumber($dataArray['po_id']);
			$this->transactionRequestType->setCustomer($this->customer);
			$this->transactionRequestType->setBillTo($this->billto);
			$this->transactionRequestType->setProfile($this->custpaymentprofile);

			$this->request = new AnetAPI\CreateTransactionRequest();
			$this->request->setMerchantAuthentication($this->merchantAuthentication);
			$this->request->setRefId($this->refId);
			$this->request->setTransactionRequest( $this->transactionRequestType);

			$this->controller = new AnetController\CreateTransactionController($this->request);
			$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

			if ($this->response != null)
			{
				$this->tresponse = $this->response->getTransactionResponse();
				error_log( print_R($this->tresponse,TRUE) );
				if (($this->tresponse != null) && ($this->tresponse->getResponseCode() == 1) )   
				{
					$this->returnArray['trans_error'] = false;
					$this->returnArray['auth_code'] = $this->tresponse->getAuthCode();
					$this->returnArray['trans_id'] = $this->tresponse->getTransId();
				}
				elseif ($this->tresponse != null)
				{
					$this->errors = $this->tresponse->getErrors(); // returns an array that holds an object
					$this->returnArray['trans_error'] = true;
					$this->returnArray['trans_error_code'] = $this->errors[0]->getErrorCode();
					$this->returnArray['trans_error_message'] = $this->errors[0]->getErrorText();
				}
			}
			else
			{
				$this->returnArray['trans_error'] = true;
				$this->returnArray['trans_error_code'] = false;
			}





			return $this->returnArray;
		}
	}
}