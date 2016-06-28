<?php

require_once __DIR__ . '/../bootstrap.php';

use \PayPal\Api\Payment;
use \PayPal\Api\PaymentExecution;

if(!isset($_GET['success'],$_GET['paymentId'],$_GET['PayerID']))
{
	echo "Something went wrong!";
	die();
}

if((bool)$_GET['success'] === false)
{
	echo "Something went wrong!";
	die();
}

$paymentId = $_GET['paymentId'];
echo $PayerID = $_GET['PayerID'];
echo $userId = $_GET['userId'];
exit();
updateUserInfo($userId, $PayerID)

$payment = Payment::get($paymentId,getApiContext());

$execute = new PaymentExecution();
$execute->setPayerId($PayerID);

try{
	$result = $payment->execute($execute,getApiContext());
}
catch (Exception $e){
	$data = json_decode($e->getData());
	var_dump($data);
	die();
}

$baseUrl = getBaseUrl();
header("Location: {$baseUrl}");