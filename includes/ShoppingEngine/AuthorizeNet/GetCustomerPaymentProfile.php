<?php
require 'includes/ShoppingEngine/AuthorizeNet/vendor/autoload.php';
require_once('includes/ShoppingEngine/AuthorizeNet/Constants.php');
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


/**
* GetCustomerPaymentProfile
*/
class GetCustomerPaymentProfile
{
	private $merchantAuthentication = null;
	private $profileSelected = null;
	private $paymentProfilesSelected = null;
	private $creditCard = null;
	private $billTo = null;
	private $payment = null;
	private $request = null;
	private $controller = null;
	private $response = null;
	private $returnArray = array();

	public function getCustomerPaymentProfile($profileId)
	{
		if($profileId) {
			$this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
			$this->merchantAuthentication->setName(Constants::getAPILogin());
			$this->merchantAuthentication->setTransactionKey(Constants::getTransKey());
			$this->refId = 'ref' . time();


			$this->request = new AnetAPI\GetCustomerProfileRequest();
			$this->request->setMerchantAuthentication($this->merchantAuthentication);
			$this->request->setCustomerProfileId($profileId);
			$this->request->setRefId($this->refId);

			$this->controller = new AnetController\GetCustomerProfileController($this->request);
			$this->response = $this->controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
			if (($this->response != null) && ($this->response->getMessages()->getResultCode() == "Ok") )
			{
				$this->profileSelected = $this->response->getProfile();
				$this->paymentProfilesSelected = $this->profileSelected->getPaymentProfiles();

				if($this->paymentProfilesSelected != null) 
				{
					$this->returnArray['has_profile'] = true;

					foreach ($this->paymentProfilesSelected as $profile) {
						
						$paymentProfileId = $profile->getCustomerPaymentProfileId(); // send payment profile ID
						$this->billTo = $profile->getBillto();	
						$this->payment = $profile->getPayment();	
						$this->creditCard = $this->payment->getCreditCard();

						$cardNumber = $this->creditCard->getCardNumber(); // send card number XXXX1234
						$expirationDate = $this->creditCard->getExpirationDate(); // send expiration date
						$this->returnArray[] = array (
							'payment_profile_id' => $profile->getCustomerPaymentProfileId(),
							'card_number' => $this->creditCard->getCardNumber(),
							'exp_date' => $this->creditCard->getExpirationDate(),
							'b_first_name' => $this->billTo->getFirstName(),
							'b_last_name' => $this->billTo->getLastName(),
							'b_company' => $this->billTo->getCompany(),
							'b_address' => $this->billTo->getAddress(),
							'b_city' => $this->billTo->getCity(),
							'b_state' => $this->billTo->getState(),
							'b_zip' => $this->billTo->getZip(),
							'b_country' => $this->billTo->getCountry(),
							'b_phoneNumber' => $this->billTo->getPhoneNumber()
							);

					}	
				}
			}
			else
			{
				$this->returnArray['has_profile'] = false;
			}

			return $this->returnArray;
		}
	}
	
}