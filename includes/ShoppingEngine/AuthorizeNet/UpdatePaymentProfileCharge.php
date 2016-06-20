<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


/**
* UpdatePaymentProfileCharge
*/
class UpdatePaymentProfileCharge
{
	
	private $merchantAuthentication = null;
	private $creditCard = null;
	private $paymentCreditCard = null;
	private $paymentprofile = null;
	private $transactionRequestType = null;
	private $request = null;
	private $getRequest = null;
	private $transRequest = null;
	private $refId = null;
	private $order = null;
	private $errors = null;
	private $customer = null;
	private $controller = null;
	private $transController = null;
	private $response = null;
	private $tresponse = null;
	private $transResponse = null;
	private $errorMessages = null;
	private $returnArray = array();

	public function updatePaymentProfileCharge($dataArray)
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



			// ###############################################
			// do payment transaction ########################	
			$this->order = new AnetAPI\OrderType();
			$this->order->setInvoiceNumber($dataArray['invoice_id']);
			$this->order->setDescription($dataArray['order_description']);

			$this->customer = new AnetAPI\CustomerDataType();
			$this->customer->setId($dataArray['customer_profile_id']);
			$this->customer->setEmail($dataArray['email']);

			$this->transactionRequestType = new AnetAPI\TransactionRequestType();
			$this->transactionRequestType->setTransactionType("authCaptureTransaction"); 
			$this->transactionRequestType->setAmount($dataArray['amount']);
			$this->transactionRequestType->setPayment($this->paymentCreditCard);
			$this->transactionRequestType->setOrder($this->order);
			$this->transactionRequestType->setPoNumber($dataArray['po_id']);
			$this->transactionRequestType->setCustomer($this->customer);
			$this->transactionRequestType->setBillTo($this->billto);

			$this->transRequest = new AnetAPI\CreateTransactionRequest();
			$this->transRequest->setMerchantAuthentication($this->merchantAuthentication);
			$this->transRequest->setRefId($this->refId);
			$this->transRequest->setTransactionRequest($this->transactionRequestType);

			$this->transController = new AnetController\CreateTransactionController($this->transRequest);
			$this->transResponse = $this->transController->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

			if ($this->transResponse != null)
			{
				$this->tresponse = $this->transResponse->getTransactionResponse();
				//error_log( print_R($this->tresponse,TRUE) );
				if (($this->tresponse != null) && ($this->tresponse->getResponseCode() == 1) )   
				{
					$this->returnArray['trans_error'] = false;
					$this->returnArray['auth_code'] = $this->tresponse->getAuthCode();
					$this->returnArray['trans_id'] = $this->tresponse->getTransId();
					// a good transaction has run, proceed to update profile
				}
				elseif ($this->tresponse != null)
				{
					$this->errors = $this->tresponse->getErrors(); // returns an array that holds an object
					$this->returnArray['trans_error'] = true;
					$this->returnArray['trans_error_code'] = $this->errors[0]->getErrorCode();
					$this->returnArray['trans_error_message'] = $this->errors[0]->getErrorText();
					return $this->returnArray;
				}
			}
			else
			{
				$this->returnArray['trans_error'] = true;
				$this->returnArray['trans_error_code'] = false;
				return $this->returnArray;
			}




			// ################################################################
			// Update Profile
			$this->paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
			$this->paymentprofile->setCustomerPaymentProfileId($dataArray['payment_profile_id']);
			$this->paymentprofile->setBillTo($this->billto);
			$this->paymentprofile->setPayment($this->paymentCreditCard);

			$this->request->setPaymentProfile($this->paymentprofile );

			$this->controller = new AnetController\UpdateCustomerPaymentProfileController($this->request);
			$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
			if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
			{
				 // Update only returns success or fail, if success
				 // confirm the update by doing a GetCustomerPaymentProfile
				$this->getRequest = new AnetAPI\GetCustomerPaymentProfileRequest();
				$this->getRequest->setMerchantAuthentication($this->merchantAuthentication);
				$this->getRequest->setRefId($this->refId);
				$this->getRequest->setCustomerProfileId($dataArray['customer_profile_id']);
				$this->getRequest->setCustomerPaymentProfileId($dataArray['payment_profile_id']);

				$this->controller = new AnetController\GetCustomerPaymentProfileController($this->getRequest);
				$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
				if(($this->response != null)){
					if ($this->response->getMessages()->getResultCode() == "Ok")
					{
						$this->returnArray['paymentProfileId'] = $this->response->getPaymentProfile()->getCustomerPaymentProfileId();
					}
					else
					{
						$this->returnArray['profile_error'] = true;
						$this->returnArray['profile_error_code'] = $this->errorMessages[0]->getCode();
						$this->returnArray['profile_error_message'] .= $this->response->getMessages()->getMessage();
					}
				}
				else{
					$this->returnArray['profile_error'] = true;
					$this->returnArray['profile_error_message'] .= "NULL Response Error";
				}

			}
			else
			{
				$this->returnArray['profile_error'] = true;
				$this->returnArray['profile_error_message'] .= "Update Customer Payment Profile: ERROR Invalid response\n";
			}

			
			return $this->returnArray;

		}

	}










}