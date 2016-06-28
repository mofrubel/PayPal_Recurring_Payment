<?php 
/*
 * Order listing page. We rely on the local database
 * to retrieve the order history of this buyer. 
 */
require_once __DIR__ . '/../bootstrap.php';

try {
	$plans = getPlans();
} catch (Exception $ex) {
	// Don't overwrite any message that was already set
	if(!isset($message)) {
		$message = $ex->getMessage();
		$messageType = "error";
	}
	$plans = array();
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
		<h2>Orders</h2>
		<a href="create_plan.php">Create Plan</a>
		<table class='table'>
			<thead>
				<tr>
					<th>Plan No.</th>					
					<th>PlanID</th>
				</tr>
			</thead>			
			<tbody>
				<?php $i=1;?>
				<?php foreach($plans as $plan_value) {?>
				<tr>
					<td><strong><?=$i++;?></strong></td>
					<td><a href="plan_details.php?plan_id=<?php echo $plan_value['plan_id'];?>"><?php echo $plan_value['plan_id'];?></a></td>															
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<?php include '../footer.php';?>
	<script src="../../public/js/application.js" type="text/javascript"></script>
</body>
</html>
