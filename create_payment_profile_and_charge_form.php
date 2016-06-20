<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');

$authorizeNetCore = new AuthorizeNetCore();
$token = rand(0,100000);

if ($_POST['post_token']) {

  $returnArray = $authorizeNetCore->createPaymentProfileCharge($_POST);

  
  // check to see if payment profile was created
  if (!is_null($returnArray['payment_profile_id'])) {
    $profileId = $returnArray['payment_profile_id'];
    $profileReturnMessage = 'Created new payment profile!'; 
  }
  else {
    $profileId = 'ERROR';
    $errorCode = $returnArray['code'];
    $errorText = $returnArray['error_message'];
    $profileReturnMessage = "ERROR: Code $errorCode - $errorText"; 
  }

  // check to see if card for profile was charged
  if (returnArray['trans_error'] == false) {
    $authCode = $returnArray['auth_code'];
    $transId = $returnArray['trans_id'];
    $transactionReturnMessage = ' Successful Transaction!';
  }
  else {
    $transErrorCode = $returnArray['trans_error_code'];
    $transErrorText = $returnArray['trans_error_message'];
    $transactionReturnMessage = " TRANS ERROR: Code $transErrorCode - $transErrorText"; 
  }

}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CIM Create Payment Profile and Charge</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>Create CIM Payment Profile and Charge</h4>
<form action="create_payment_profile_and_charge_form.php" method="POST">
<input type="hidden" name="customerProfileId" value="1200368258">
<input type="hidden" name="merchantCustomerId" value="564564564">
<input type="hidden" name="amount" value="175.95">
<input type="hidden" name="post_token" value="<?= $token ?>">
  <fieldset class="form-group">
    <label for="firstName">First Name</label>
    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name">
  </fieldset>
  <fieldset class="form-group">
    <label for="lastName">Last Name</label>
    <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name">
  </fieldset>
 <fieldset class="form-group">
    <label for="company">Company Name</label>
    <input type="text" class="form-control" id="company" name="company" placeholder="Company Name">
  </fieldset> 
 <fieldset class="form-group">
    <label for="address">Address</label>
    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
  </fieldset> 
 <fieldset class="form-group">
    <label for="city">City</label>
    <input type="text" class="form-control" id="city" name="city" placeholder="City">
  </fieldset> 
 <fieldset class="form-group">
    <label for="state">State</label>
    <input type="text" class="form-control" id="state" name="state" placeholder="State">
  </fieldset> 
 <fieldset class="form-group">
    <label for="zip">Zip</label>
    <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip">
  </fieldset> 
 <fieldset class="form-group">
    <label for="country">Country</label>
    <input type="text" class="form-control" id="country" name="country" placeholder="Country">
  </fieldset> 
 <fieldset class="form-group">
    <label for="phoneNumber">Phone Number</label>
    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number">
  </fieldset> 
 <fieldset class="form-group">
    <label for="cardNumber">Credit Card Number</label>
    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="Credit Card Number">
  </fieldset> 
 <fieldset class="form-group">
    <label for="expirationDate">Expiration Date</label>
    <input type="text" class="form-control" id="expirationDate" name="expirationDate" placeholder="Expiration Date">
  </fieldset> 
 <fieldset class="form-group">
    <label for="cardCode">Card Code</label>
    <input type="text" class="form-control" id="cardCode" name="cardCode" placeholder="Card Code">
  </fieldset> 
   <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div><!-- /col 6 -->

<div class="col-md-6 response">
<h4>Return Output</h4>
<p>Auth Code: <?= $authCode ?> Trans ID: <?= $transId ?></p>
<p><?= $transactionReturnMessage ?></p>
<hr>
<p>New Payment ID: <?= $profileId ?></p>
<p><?= $profileReturnMessage ?></p>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>