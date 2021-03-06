<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');

$authorizeNetCore = new AuthorizeNetCore();
$token = rand(0,100000);

if ($_POST['post_token']) {

  $returnArray = $authorizeNetCore->chargeCardNewProfile($_POST);

  // profile creation responses
  if ($returnArray['profile_error'] == false) {
    $customerId = $returnArray['customer_id']; // authnet customer ID
    $paymentProfile = $returnArray['payment_id_array'][0]; // authnet payment profile ID
    $profileReturnMessage = 'Customer and Payment profiles created!';
  }
  else {
    $customerId = 'error';
    $paymentProfile = 'error';
    $profileErrorCode = $returnArray['profile_error_code'];
    $profileErrorMessage = $returnArray['profile_error_message'];
    $profileReturnMessage = "ERROR - PROFILE NOT CREATED<br>code $profileErrorCode : $profileErrorMessage"; 
  }

  // transaction responses
  if ($returnArray['trans_error'] == false) {
    $authCode = $returnArray['auth_code'];
    $transId = $returnArray['trans_id'];
    $transactionReturnMessage = 'Successful Transaction!'; 
  }
  else {
    $authCode = 'error';
    $transId = 'error';
    $errorCode = $returnArray['trans_error_code'];
    $errorMessage = $returnArray['trans_error_message'];
    $transactionReturnMessage = "ERROR - NO TRANSACTION<br>code $errorCode : $errorMessage"; 
  }

}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CIM Charge and Create Customer/Payment Profile</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>CIM Charge and Create Customer/Payment Profile</h4>
<form action="charge_create_profile_form.php" method="POST">
<input type="hidden" name="merchantCustomerId" value="3434353534">
<input type="hidden" name="post_token" value="<?= $token ?>">
<input type="hidden" name="order_description" value="test order five">
<input type="hidden" name="description" value="created at checkout">
<input type="hidden" name="amount" value="250">
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
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
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
<p>Customer ID: <?= $customerId ?> Payment ID: <?= $paymentProfile ?></p>
<p><?= $profileReturnMessage ?></p>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>