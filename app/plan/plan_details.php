<?php
/*
 * Order listing page. We rely on the local database
 * to retrieve the order history of this buyer. 
 */
require_once __DIR__ . '/../bootstrap.php';

$planDetails = null;
try {
    if (isset($_REQUEST['plan_id']) && trim($_REQUEST['plan_id']) != '') {
        $planDetails = getPlanDetails($_REQUEST['plan_id']);
    }
} catch (\PayPal\Exception\PPConnectionException $ex) {
    $message = parseApiError($ex->getData());
    $messageType = "error";
} catch (Exception $ex) {
    $message = $ex->getMessage();
    $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta content='IE=Edge,chrome=1' http-equiv='X-UA-Compatible'>
    <meta content='width=device-width, initial-scale=1.0' name='viewport'>
    <title>RedQ Team</title>
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js" type="text/javascript"></script>
    <![endif]-->
    <link href="../../public/css/application.css" media="all" rel="stylesheet"
          type="text/css" />
    <link href="../../public/images/favicon.ico" rel="shortcut icon"
          type="image/vnd.microsoft.icon" />
</head>
<body>
<?php include '../navbar.php';?>
<div class='container' id='content'>
    <?php if(isset($message) && isset($messageType)) {?>
        <div class="alert fade in alert-<?php echo $messageType;?>">
            <button class="close" data-dismiss="alert">&times;</button>
            <?php echo $message;?>
        </div>
    <?php }?>
    <br />
    <a href="plans.php" >&#10094; Back to Plans</a>
    <h2>Plan Details</h2>
		
        <?php
            if (defined("JSON_PRETTY_PRINT")) {
                $plan_details = json_decode($planDetails->toJSON(JSON_PRETTY_PRINT));
                /*echo "<pre>";
                print_r($plan_details);*/
            }
            else {
                $plan_details = json_decode($planDetails->toJSON());
                echo "<pre>";
                print_r($plan_details);
            } ?> 
		<div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Billing frequency</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" type="text" value="<?=$plan_details->payment_definitions[0]->frequency_interval?>" readonly/>
            </div>
        </div>
        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Billing period</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" value="<?=$plan_details->payment_definitions[0]->frequency?>" type="text" readonly/>
            </div>
        </div>
        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Total billing cycles</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" value="<?=$plan_details->payment_definitions[0]->cycles?>" type="text" readonly/>
            </div>
        </div>
        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Amount (per billing cycle)</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" value="<?=$plan_details->payment_definitions[0]->amount->value?>" type="text" readonly/>
            </div>
        </div>
        <div class="control-group f_interval required">
            <label class="f_interval required control-label" for="user_email">
                <abbr title="required">*</abbr> Currency</label>
            <div class="controls">
            <input autofocus="autofocus" class="string email required" value="<?=$plan_details->payment_definitions[0]->amount->currency?>" type="text" readonly/>
            </div>
        </div>
        
      
</div>
<?php include '../footer.php';?>
<script src="../../public/js/application.js" type="text/javascript"></script>
</body>
</html>
