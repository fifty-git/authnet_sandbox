<?php

require_once('includes/ShoppingEngine/AuthorizeNet/CreateCustomerProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/CreatePaymentProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/ChargePaymentProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/ChargeCreditCardNewProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/GetCustomerPaymentProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/CreatePaymentProfileCharge.php');
require_once('includes/ShoppingEngine/AuthorizeNet/DeletePaymentProfile.php');
require_once('includes/ShoppingEngine/AuthorizeNet/UpdatePaymentProfileCharge.php');

/**
* 
*/
class AuthorizeNetCore
{
	
	private $newCustProfile = null;

	function __construct()
	{
		$this->newCustProfile = new CreateCustomerProfile();
		$this->newPaymentProfile = new CreatePaymentProfile();
		$this->chargePaymentProfile = new ChargePaymentProfile();
		$this->chargeCardNewProfile = new ChargeCreditCardNewProfile();
		$this->getCustomerPaymentProfile = new GetCustomerPaymentProfile();
		$this->createPaymentProfileCharge = new CreatePaymentProfileCharge();
		$this->deletePaymentProfile = new DeletePaymentProfile();
		$this->updatePaymentProfileCharge = new UpdatePaymentProfileCharge();
	}

	public function createCustomerProfile($merchId, $email, $description)
	{
		$resultArray = $this->newCustProfile->createCustomerProfile($merchId, $email, $description);
		return $resultArray;
	}

	public function createPaymentProfile($dataArray)
	{
		$resultArray = $this->newPaymentProfile->createPaymentProfile($dataArray);
		return $resultArray;
	}

	public function chargePaymentProfile($profileid, $paymentprofileid, $amount)
	{
		$resultArray = $this->chargePaymentProfile->chargePaymentProfile($profileid, $paymentprofileid, $amount);
		return $resultArray;
	}

	/**
	 * chargeCardNewProfile
	 * 
	 * charges a new credit card and creates a new customer and payment profile
	 * @param array - payment profile data
	 * @return array - 
		$authCode
	    $transId
	    $customerId
	    $paymentProfile
	 */
	public function chargeCardNewProfile($dataArray)
	{
		$resultArray = $this->chargeCardNewProfile->chargeCardNewProfile($dataArray);
		return $resultArray;
	}

	public function createPaymentProfileCharge($dataArray)
	{
		$resultArray = $this->createPaymentProfileCharge->createPaymentProfileCharge($dataArray);
		return $resultArray;
	}

	public function getCustomerPaymentProfile($customerProfileId)
	{
		$resultArray = $this->getCustomerPaymentProfile->getCustomerPaymentProfile($customerProfileId);
		return $resultArray;
	}

	public function updatePaymentProfileCharge($dataArray)
	{
		$resultArray = $this->updatePaymentProfileCharge->updatePaymentProfileCharge($dataArray);
		return $resultArray;
	}

	public function deletePaymentProfile($customerProfileid, $paymentProfileId)
	{
		$resultArray = $this->deletePaymentProfile->deletePaymentProfile($customerProfileid, $paymentProfileId);
		return $resultArray;
	}






}