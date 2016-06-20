<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
* DeletePaymentProfile
*/
class DeletePaymentProfile
{
	
	private $merchantAuthentication = null;
	private $request = null;
	private $controller = null;
	private $response = null;
	private $errorMessages = null;
	private $returnArray = array();

	public function deletePaymentProfile($customerProfileid, $paymentProfileId)
	{
		if($customerProfileid) {
			$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
			$this->merchantAuthentication->setName(Constants::getAPILogin());
			$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
			$this->refId = 'ref' . time();

			$this->request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
			$this->request->setMerchantAuthentication($this->merchantAuthentication);
			$this->request->setCustomerProfileId($customerProfileid);
			$this->request->setCustomerPaymentProfileId($paymentProfileId);
			$this->controller = new AnetController\DeleteCustomerPaymentProfileController($this->request);
			$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
			error_log( print_R($this->response,TRUE) );
			if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
			{
				$this->returnArray['error'] = false;
			}
			else
			{
				$this->errorMessages = $this->response->getMessages()->getMessage();
				$this->returnArray['error'] = true;
				$this->returnArray['error_code'] = $this->errorMessages[0]->getCode();
				$this->returnArray['error_message'] = $this->errorMessages[0]->getText();
			}

			return $this->returnArray;

		}

	}
}