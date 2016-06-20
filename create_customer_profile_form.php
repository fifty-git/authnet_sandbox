<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1); 

require_once('includes/ShoppingEngine/Core/AuthorizeNetCore.php');

$authorizeNetCore = new AuthorizeNetCore();
$token = rand(0,100000);

if ($_POST['post_token']) {

  $merchId = $_POST['merchantCustomerId'];
  $email = $_POST['email'];
  $description = $_POST['description'];
  

  $returnArray = $authorizeNetCore->createCustomerProfile($merchId, $email, $description);

  if (!$returnArray['error']) {
    $profileId = $returnArray['profile_id'];
    $returnMessage = 'Created new profile ID!'; 
  }
  else {
    $profileId = null;
    $errorCode = $returnArray['code'];
    $errorText = $returnArray['error_message'];
    $returnMessage = "ERROR: Code $errorCode - $errorText"; 
  }

}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CIM Create Customer Profile</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
.container { max-width: 950px; }
</style>
</head>
<body>
<div class="container">

<div class="row">
<div class="col-md-6">
<h4>Create CIM Customer Profile</h4>
<form action="create_customer_profile_form.php" method="POST">
<input type="hidden" name="merchantCustomerId" value="<?= $token ?>">
<input type="hidden" name="post_token" value="<?= $token ?>">
<input type="hidden" name="description" value="created at log in">
  <fieldset class="form-group">
    <label for="firstName">First Name</label>
    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name">
  </fieldset>
  <fieldset class="form-group">
    <label for="lastName">Last Name</label>
    <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name">
  </fieldset>
  <fieldset class="form-group">
    <label for="email">Email address</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
  </fieldset>
   <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div><!-- /col 6 -->

<div class="col-md-6 response">
<h4>Return Output</h4>
<p><?= $profileId ?></p>
<p><?= $returnMessage ?></p>
</div><!-- /col 6 -->
</div><!-- /row -->
</div><!-- /container -->





<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>