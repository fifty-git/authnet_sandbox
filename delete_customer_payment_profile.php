<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');
$authorizeNetCore = new AuthorizeNetCore();

$token = rand(0,100000);

if ($_POST['post_token']) {

  $profileId = $_POST['merchantCustomerId'];
  $paymentProfileId = $_POST['paymentProfileId'];

  $returnArray = $authorizeNetCore->deletePaymentProfile($profileId, $paymentProfileId);

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
	<title>CIM Delete a Payment Profile</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>CIM Delete a Payment Profile</h4>
<form action="delete_customer_payment_profile.php" method="POST">
<input type="hidden" name="merchantCustomerId" value="1200356561">
<input type="hidden" name="paymentProfileId" value="1200253244">
<input type="hidden" name="post_token" value="<?= $token ?>">
<p>Delete a payment profile</p>
   <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div><!-- /col 6 -->

<div class="col-md-6 response">
<h4>Return Output</h4>
<?= $returnMessage ?>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>