<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');

$authorizeNetCore = new AuthorizeNetCore();
$token = rand(0,100000);

if ($_POST['post_token']) {

  $profileid = $_POST['merchantCustomerId'];
  $paymentprofileid = $_POST['paymentProfileId'];
  $amount = $_POST['amount'];

  $returnArray = $authorizeNetCore->chargePaymentProfile($profileid, $paymentprofileid, $amount);

  if (!$returnArray['error']) {
    $authCode = $returnArray['auth_code'];
    $transId = $returnArray['trans_id'];
    $returnMessage = 'Successful Transaction!'; 
  }
  else {
    $authCode = 'error';
    $transId = 'error';
    $errorCode = $returnArray['error_code'];
    if($errorCode == 2) {
      $returnMessage = "ERROR: Code $errorCode - There was an error in the transaction"; 
    }
    elseif($errorCode == 4) {
      $returnMessage = "ERROR: Code $errorCode - transaction held for review"; 
    }
    elseif($errorCode == false) {
      $returnMessage = "ERROR: error in running transaction"; 
    }
  }

}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CIM Charge Payment Profile</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>CIM Charge Payment Profile</h4>
<form action="charge_payment_profile.php" method="POST">
<input type="hidden" name="merchantCustomerId" value="1200368258">
<input type="hidden" name="paymentProfileId" value="1200261460">
<input type="hidden" name="amount" value="100">
<input type="hidden" name="post_token" value="<?= $token ?>">
<p>Submit to Bill to a Payment Profile: $100.00</p>
   <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div><!-- /col 6 -->

<div class="col-md-6 response">
<h4>Return Output</h4>
<p>Auth Code: <?= $authCode ?> Trans ID: <?= $transId ?></p>
<p><?= $returnMessage ?></p>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>