<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
* CreateCustomerProfile
*/
class CreateCustomerProfile
{
	private $merchantAuthentication = null;
	private $customerprofile = null;
	private $refId = null;
	private $request = null;
	private $controller = null;
	private $response = null;

	public function createCustomerProfile($merchId, $email, $description)
	{
		$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$this->merchantAuthentication->setName(Constants::getAPILogin());
		$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
		$this->refId = 'ref' . time();

		$this->customerprofile = new AnetAPI\CustomerProfileType();
		$this->customerprofile->setDescription($description);

		$this->customerprofile->setMerchantCustomerId($merchId);
		$this->customerprofile->setEmail($email);

		$this->request = new AnetAPI\CreateCustomerProfileRequest();
		$this->request->setMerchantAuthentication($this->merchantAuthentication);
		$this->request->setRefId($this->refId);
		$this->request->setProfile($this->customerprofile);
		$this->controller = new AnetController\CreateCustomerProfileController($this->request);
		$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

		if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
		{
			$returnArray['error'] = false;
			$returnArray['profile_id'] = $this->response->getCustomerProfileId();
		}
		else
		{
			$returnArray['error'] = true;
			$errorMessages = $this->response->getMessages()->getMessage();
			$returnArray['code'] = $errorMessages[0]->getCode();
			$returnArray['error_message'] = $errorMessages[0]->getText();
		}

		return $returnArray;	
	}
}