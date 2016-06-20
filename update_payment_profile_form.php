<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');

$authorizeNetCore = new AuthorizeNetCore();
$token = rand(0,100000);

if ($_POST['post_token']) {

  $returnArray = $authorizeNetCore->updatePaymentProfileCharge($_POST);

  if ($returnArray['error'] == false) {
    $returnMessage = 'Payment Profile Deleted'; 
  }
  else {
    $errorCode = $returnArray['error_code'];
    $errorMessage = $returnArray['error_message'];
    $returnMessage = "ERROR: code $errorCode - $errorMessage";
  }

}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CIM Create Payment Profile</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>Create CIM Payment Profile</h4>
<form action="update_payment_profile_form.php" method="POST">
<input type="hidden" name="customer_profile_id" value="1200355109">
<input type="hidden" name="payment_profile_id" value="1200255831">
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
<p>New Payment Profile ID: <?= $profileId ?></p>
<p>Auth Code: <?= $authCode ?></p>
<p>Trans ID: <?= $transId ?></p>
<p><?= $returnMessage ?></p>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>