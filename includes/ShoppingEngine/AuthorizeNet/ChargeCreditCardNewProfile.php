<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
* ChargeCreditCardNewProfile
*/
class ChargeCreditCardNewProfile
{
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $paymentOne = null;
	private $order = null;
	private $transactionRequestType = null;
	private $billto = null;
	private $paymentprofile = null;
	private $customer = null;
	private $custpaymentprofile = null;

	private $refId = null;
	private $request = null;
	private $controller = null;
	private $response = null;
	private $tresponse = null;	
	private $cresponse = null;	
	private $cmessages = null;	
	private $message = null;	
	private $errors = null;	
	private $returnArray = array();	

	public function chargeCardNewProfile($dataArray)
	{
		$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$this->merchantAuthentication->setName(Constants::getAPILogin());
		$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
		$this->refId = 'ref' . time();

		$this->creditCard = new AnetAPI\CreditCardType();
		$this->creditCard->setCardNumber($dataArray['cardNumber']);
		$this->creditCard->setExpirationDate($dataArray['expirationDate']);
		$this->creditCard->setCardCode($dataArray['cardCode']);

		$this->paymentOne = new AnetAPI\PaymentType();
		$this->paymentOne->setCreditCard($this->creditCard);

		$this->order = new AnetAPI\OrderType();
		$this->order->setInvoiceNumber($dataArray['invoice_id']);
		$this->order->setDescription($dataArray['order_description']);

		$this->custpaymentprofile = new AnetAPI\CustomerProfilePaymentType();
		// this flag tells authnet to create a new customer and payment profile
		$this->custpaymentprofile->setCreateProfile(TRUE);

		$this->customer = new AnetAPI\CustomerDataType();
		$this->customer->setId($dataArray['merchantCustomerId']);
		$this->customer->setEmail($dataArray['email']);

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

		$this->transactionRequestType = new AnetAPI\TransactionRequestType();
		$this->transactionRequestType->setTransactionType( "authCaptureTransaction"); 
		$this->transactionRequestType->setAmount($dataArray['amount']);
		$this->transactionRequestType->setPayment($this->paymentOne);
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
			$this->cresponse = $this->response->getProfileResponse();
			$this->cmessages = $this->cresponse->getMessages();

			error_log( print_R($this->cmessages,TRUE) );

			// get profile creation response #################################################
			if (($this->cresponse != null) && ($this->cmessages->getResultCode() == 'OK') )   
			{
				$this->returnArray['profile_error'] = false;
				$this->returnArray['customer_id'] = $this->cresponse->getCustomerProfileId();
				$this->returnArray['payment_id_array'] = $this->cresponse->getCustomerPaymentProfileIdList();
			}
			elseif (($this->cresponse != null) && ($this->cmessages->getResultCode() == 'Error') )   
			{
				$this->returnArray['profile_error'] = true;
				$this->message = $this->cmessages->getMessage();
				$this->returnArray['profile_error_code'] = $this->message[0]->getCode();
				$this->returnArray['profile_error_message'] = $this->message[0]->getText();
			}

			// get transaction response ######################################################
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
			$this->returnArray['error'] = true;
			$this->returnArray['error_code'] = false;
		}
		return $this->returnArray;

	}


}