<?php

require_once __DIR__ . '/db.php';

/**
 * Create a new order
 * @param string $userId Buyer's user id
 * @param string $paymentId payment id returned by paypal
 * @param string $state state of this order
 * @param string $amount payment amount in DD.DD format
 * @param string $description a description about this payment
 * @throws Exception
 */
function addPlan($planId) {
	$conn = getConnection();
	$query = sprintf("INSERT INTO %s(plan_id, created_time) 
			VALUES('%s', NOW())",
			PLANS_TABLE,
			mysql_real_escape_string($planId));
	$result = mysql_query($query, getConnection());
	if(!$result) {
		$errMsg = "Error creating new plan: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}
	$orderId = mysql_insert_id($conn);
	mysql_close($conn);
	
	return $orderId;
}

/**
 * Update a previously created order.
 * 
 * @param int $orderId
 * @param string $state
 * @param string $paymentId
 * @throws Exception
 * @return number
 */
function updatePlan($orderId, $state, $planId=NULL) {
	$conn = getConnection();
	$args = array(PLANS_TABLE, mysql_real_escape_string($state));
	 $updates = array("state='%s'");
	
	if($paymentId != NULL) {
		$args[] = mysql_real_escape_string($paymentId);
		$updates[] = "plan_id='%s'";
	}
	$args[] = $orderId;
		
	$query = vsprintf("UPDATE %s SET " . implode(', ', $updates) . " WHERE plan_id='%s'", $args);
	$result = mysql_query($query, getConnection());
	if(!$result) {
		$errMsg = "Error updating order plan: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}
	$isUpdated = mysql_affected_rows($conn);
	mysql_close($conn);
	
	return $isUpdated;
}

/**
 * Retrieve orders created by this buyer
 * @param string $email
 * @throws Exception
 * @return array
 */
function getPlans() {
	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s ORDER BY created_time DESC",
			PLANS_TABLE);
	$result = mysql_query($query, $conn);	
	if(!$result) {
		$errMsg = "Error retrieving plans: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}
	
	$rows = array();	
	while(($row = mysql_fetch_assoc($result))) {
		$rows[] = $row;
	}	
	mysql_close($conn);
	return $rows;
}


function getPlan_info($orderId) {
	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE order_id='%d'",
			PLANS_TABLE,
			mysql_real_escape_string($orderId));
	$result = mysql_query($query, $conn);
	if(!$result) {
		$errMsg = "Error retrieving plan: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$row = mysql_fetch_assoc($result);
	mysql_close($conn);
	return $row;
}
?>