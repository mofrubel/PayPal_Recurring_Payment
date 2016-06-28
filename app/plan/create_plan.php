<?php
/*
 * User registration page.
 */
require_once __DIR__ . '/../bootstrap.php';

// Sign up form postback
if($_SERVER['REQUEST_METHOD'] == 'POST') {	
	try {			
		$planId = NULL;        
        $planId = savePlan($_POST['f_interval'],$_POST['frequency'],$_POST['billing_cycle'],$_POST['amount'],$_POST['currency']);
		$userId = addPlan($planId);			
	} catch(\PayPal\Exception\PPConnectionException $ex){
		$errorMessage = $ex->getData() != '' ? parseApiError($ex->getData()) : $ex->getMessage();
	} catch (Exception $ex) {
		$errorMessage = $ex->getMessage();		
	}
	
	if(isset($userId) && $userId != false) {
		signIn($_POST['email']);
		header("Location: ../index.php");
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta content='IE=Edge,chrome=1' http-equiv='X-UA-Compatible'>
    <meta content='width=device-width, initial-scale=1.0' name='viewport'>
    <title>RedQ App</title>   
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js" type="text/javascript"></script>
    <![endif]-->
    <link href="../../public/css/application.css" media="all" rel="stylesheet" type="text/css" />
    <link href="../../public/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
  </head>
  <body>
    <?php include '../navbar.php';?>
    <div class='container' id='content'>
      
      <h2>Create Plan</h2>
	  <p>Add New Subscription Plan for subscriber</p>
      <?php if(isset($errorMessage)) {?>
		<div class="alert fade in alert-error">
			<button class="close" data-dismiss="alert">&times;</button>
			<?php echo $errorMessage;?>
		</div>
		<?php }?>
      <form accept-charset="UTF-8" action="./create_plan.php" autocomplete="off" class="simple_form form-horizontal new_user" id="new_user" method="post" novalidate="novalidate"><div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="&#x2713;" /><input name="authenticity_token" type="hidden" value="vpVuNuIt9fRZzLm0eE0gk4h249k0nZPB/WEXWn9ETwg=" /></div>
        
        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Billing frequency</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" id="f_interval" name="f_interval" size="50" type="text" value=""/>
            </div>
        </div>
        <div class="control-group select required"><label class="select required control-label" for="frequency"><abbr title="required">*</abbr> Billing period</label><div class="controls"><select class="select required" id="frequency" name="frequency"><option value=""></option>
        <option value="Day" selected>Day</option>
        <option value="Week">Week</option>
        <option value="SemiMonth">SemiMonth</option>
        <option value="Month">Month</option>
        <option value="Year">Year</option></select></div></div>

        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="billing_cycle">
                <abbr title="required">*</abbr> Total billing cycles</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" id="billing_cycle" name="billing_cycle" size="50" type="text" value=""/>
            </div>
        </div>

        <div class="control-group amount required">
            <label class="amount required control-label" for="amount">
                <abbr title="required">*</abbr> Amount (per billing cycle)</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" id="amount" name="amount" size="50" type="text" value=""/>
            </div>
        </div>

        <div class="control-group select required"><label class="select required control-label" for="currency"><abbr title="required">*</abbr> Currency</label><div class="controls"><select class="select required" id="currency" name="currency"><option value=""></option>
        <option value="USD" selected>USD</option>
        <option value="GBP">GBP</option>
        <option value="IND">IND</option>
        <option value="BDT">BDT</option>
        </select></div></div>
         
        <div class='form-actions'>
          <input class="btn btn btn-primary" name="commit" type="submit" value="Save" />
        </div>
      </form>

    </div>
	<?php include '../footer.php';?>
    <script src="../../public/js/application.js" type="text/javascript"></script>
  </body>
</html>
