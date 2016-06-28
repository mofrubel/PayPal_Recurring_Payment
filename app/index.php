<?php 
require_once 'bootstrap.php';
session_start();
$message = isset($_GET['message']) ? $_GET['message'] : NULL;

if(isset($_GET['success'],$_GET['paymentId'],$_GET['PayerID']))
{
  $paymentId = $_GET['paymentId'];
  $PayerID = $_GET['PayerID'];
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta content="IE=Edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Paypal SDK App</title>    
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js" type="text/javascript"></script>
    <![endif]-->
    <link href="../public/css/application.css" media="all" rel="stylesheet" type="text/css">
    <link href="../public/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
  </head>
  <body style="zoom: 1;">
    <?php include './navbar.php';?>
    
    <?php 
          $i=0;
          $plans = getPlans();
          $plan_details_arr = array();
          foreach($plans as $plans_value){
            $planDetails_json = getPlanDetails($plans_value['plan_id']);
            $planDetails[] = json_decode($planDetails_json);
          }
          /*echo "<pre>";
          print_r($planDetails);
          exit();*/
    ?>

    <div class="container" id="content">   
      <?php if(isset($message)) {?>
		<div class="alert fade in alert-success">
			<button class="close" data-dismiss="alert">&times;</button>
			<?php echo $message;?>
		</div>
	  <?php }?>	   
      <div class="row pizza-row">
        <?php foreach($planDetails as $planDetails_value){?>
          <div class="span2">
            <div class="image">
              <img alt="Pizza 0" src="../public/images/000000000000000000000000000000<?=$i++?>">
            </div>
            <div class="details">            
              <?=$planDetails_value->payment_definitions[0]->amount->value?> <?=$planDetails_value->payment_definitions[0]->amount->currency?> per <?=$planDetails_value->payment_definitions[0]->frequency?>
              <div><a href="./plan/plan_confirmation.php?plan=<?=$planDetails_value->id?>" class="btn btn-small" data-disable-with="Procesing.." data-method="post" rel="nofollow">Subscribe</a></div>
            </div>
          </div>
        <?php }?>        
      </div>
	  <br/><br/><br/>
      <div class="row">
		  <!-- <div class="span6 offset3">
	   	    <p>This is a sample application which showcases the new PayPal REST APIs. The app uses mock data to demonstrate how you can use the REST APIs for</p>
			<ul>
				<li>Saving credit card information with PayPal for later use.</li>
				<li>Making payments using a saved credit card.</li>
				<li>Making payments using PayPal.</li>
			</ul>
			</div> -->
      </div>
    </div>
    <?php include './footer.php';?>
    <script src="../public/js/application.js" type="text/javascript"></script>
</body></html>
