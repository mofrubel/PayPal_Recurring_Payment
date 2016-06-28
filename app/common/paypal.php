<?php

// Wrapper methods for all PayPal integration

use PayPal\Api\PaymentExecution;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Agreement;
use PayPal\Api\ShippingAddress;

/*
* Save Subscription plans
*/
function savePlan($f_interval,$frequency,$billing_cycle,$amount,$currency){
	$plan = new Plan();
	$plan->setName('Subscription Package for Daily')
    ->setDescription('Template creation.')
    ->setType('fixed');

    $paymentDefinition = new PaymentDefinition();
    $paymentDefinition->setName('Regular Payments')
    ->setType('REGULAR')
    ->setFrequency($frequency)
    ->setFrequencyInterval($f_interval)
    ->setCycles($billing_cycle)
    ->setAmount(new Currency(array('value' => $amount, 'currency' => $currency)));

	$merchantPreferences = new MerchantPreferences();
	$baseUrl = getBaseUrl();
	$merchantPreferences->setReturnUrl("$baseUrl/ExecuteAgreement.php?success=true")
	    ->setCancelUrl("$baseUrl/ExecuteAgreement.php?success=false")
	    ->setAutoBillAmount("yes")
	    ->setInitialFailAmountAction("CONTINUE")
	    ->setMaxFailAttempts("0")
	    ->setSetupFee(new Currency(array('value' => 1, 'currency' => 'USD')));

	$plan->setPaymentDefinitions(array($paymentDefinition));
	$plan->setMerchantPreferences($merchantPreferences);

	// ## Create Plan
	try {
	    $output = $plan->create(getApiContext());
	    return $output->getId();
	} catch (Exception $ex) {
	    $data = json_decode($ex->getData());
		var_dump($data);
		die();
	}
}
function getPlanDetails($planId) {	
    $plan = Plan::get($planId, getApiContext());
    return $plan;
}

/**
 * Save a paypal account info with paypal
 * 
 * This helps you avoid the hassle of securely storing paypal account info
 * card information on your site. PayPal provides a paypal account info
 * id that you can use for charging future payments.
 * 
 * @param array $params	paypal account info parameters
 */

function savePayPal($params,$userId) {	
	$payer = new Payer();
	$payer->setPaymentMethod("paypal");

	$amount = new Amount();
	$amount->setTotal(0)
		->setCurrency('USD');

	$transaction = new Transaction();
	$transaction->setAmount($amount)
				->setDescription("this is a test transaction")
				->setInvoiceNumber(uniqid());

	$baseUrl = getBaseUrl();
	$redirectUrl = new RedirectUrls();
	$redirectUrl->setReturnUrl($baseUrl."common/pay.php?success=true&userId=".$userId)
				->setCancelUrl($baseUrl."common/pay.php?success=false&userId".$userId);

	$payment = new Payment();
	$payment->setIntent("sale")
			->setPayer($payer)
			->setRedirectUrls($redirectUrl)
			->setTransactions([$transaction]);

	try{
		$payment->create(getApiContext());
	}
	catch (Exception $e){
		$data = json_decode($e->getData());
		var_dump($data);
		die();
	}
	$apporal_url = $payment->getApprovalLink();
	print_r($apporal_url);exit();
	header("Location: {$apporal_url}");

	/*$agreement = new Agreement();
	$agreement->setName('Base Agreement')
    ->setDescription('Basic Agreement')
    ->setStartDate('2019-06-17T9:45:04Z');

    $plan = new Plan();
	$plan->setId($planId);
	$agreement->setPlan($plan);

	$payer = new Payer();
	$payer->setPaymentMethod('paypal');
	$agreement->setPayer($payer);*/
	
	/*$agreement = $agreement->create(getApiContext());	
	$approvalUrl = $agreement->getApprovalLink();*/	
	/*header("Location: {$approvalUrl}");*/
}

/**
 * 
 * @param string $payerId id obtained from 
 * a previous create API call.
 */
function getPayPal($cardId) {
	return CreditCard::get($cardId, getApiContext());
}
/**
 * Save a credit card with paypal
 * 
 * This helps you avoid the hassle of securely storing credit
 * card information on your site. PayPal provides a credit card
 * id that you can use for charging future payments.
 * 
 * @param array $params	credit card parameters
 */

function saveCard($params) {
	
	$card = new CreditCard();
	$card->setType($params['type']);
	$card->setNumber($params['number']);
	$card->setExpireMonth($params['expire_month']);
	$card->setExpireYear($params['expire_year']);
	$card->setCvv2($params['cvv2']);
	
	$card->create(getApiContext());
	return $card->getId();
}

/**
 * 
 * @param string $cardId credit card id obtained from 
 * a previous create API call.
 */
function getCreditCard($cardId) {
	return CreditCard::get($cardId, getApiContext());
}
/**
 * Create a payment using a previously obtained
 * credit card id. The corresponding credit
 * card is used as the funding instrument.
 * 
 * @param string $creditCardId credit card id
 * @param string $total Payment amount with 2 decimal points
 * @param string $currency 3 letter ISO code for currency
 * @param string $paymentDesc
 */
function makePaymentUsingCC($creditCardId, $total, $currency, $paymentDesc) {
		
	$ccToken = new CreditCardToken();
	$ccToken->setCreditCardId($creditCardId);	
	
	$fi = new FundingInstrument();
	$fi->setCreditCardToken($ccToken);
	
	$payer = new Payer();
	$payer->setPaymentMethod("credit_card");
	$payer->setFundingInstruments(array($fi));	
	
	// Specify the payment amount.
	$amount = new Amount();
	$amount->setCurrency($currency);
	$amount->setTotal($total);
	// ###Transaction
	// A transaction defines the contract of a
	// payment - what is the payment for and who
	// is fulfilling it. Transaction is created with
	// a `Payee` and `Amount` types
	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription($paymentDesc);
	
	$payment = new Payment();
	$payment->setIntent("sale");
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	$payment->create(getApiContext());
	return $payment;
}

/**
 * Create a payment using the buyer's paypal
 * account as the funding instrument. Your app
 * will have to redirect the buyer to the paypal 
 * website, obtain their consent to the payment
 * and subsequently execute the payment using
 * the execute API call. 
 * 
 * @param string $total	payment amount in DDD.DD format
 * @param string $currency	3 letter ISO currency code such as 'USD'
 * @param string $paymentDesc	A description about the payment
 * @param string $returnUrl	The url to which the buyer must be redirected
 * 				to on successful completion of payment
 * @param string $cancelUrl	The url to which the buyer must be redirected
 * 				to if the payment is cancelled
 * @return \PayPal\Api\Payment
 */

function makePaymentUsingPayPal($total, $currency, $paymentDesc, $returnUrl, $cancelUrl) {
	
	$payer = new Payer();
	$payer->setPaymentMethod("paypal");
	
	// Specify the payment amount.
	$amount = new Amount();
	$amount->setCurrency($currency);
	$amount->setTotal($total);
	
	// ###Transaction
	// A transaction defines the contract of a
	// payment - what is the payment for and who
	// is fulfilling it. Transaction is created with
	// a `Payee` and `Amount` types
	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription($paymentDesc);
	
	$redirectUrls = new RedirectUrls();
	$redirectUrls->setReturnUrl($returnUrl);
	$redirectUrls->setCancelUrl($cancelUrl);
	
	$payment = new Payment();
	$payment->setRedirectUrls($redirectUrls);
	$payment->setIntent("sale");
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));
	
	$payment->create(getApiContext());
  return $payment;
}


/**
 * Completes the payment once buyer approval has been
 * obtained. Used only when the payment method is 'paypal'
 * 
 * @param string $paymentId id of a previously created
 * 		payment that has its payment method set to 'paypal'
 * 		and has been approved by the buyer.
 * 
 * @param string $payerId PayerId as returned by PayPal post
 * 		buyer approval.
 */
function executePayment($paymentId, $payerId) {	
	$payment = getPaymentDetails($paymentId);
	$paymentExecution = new PaymentExecution();
	$paymentExecution->setPayerId($payerId);	
	$payment = $payment->execute($paymentExecution, getApiContext());	
	
	return $payment;
}

/**
 * Retrieves the payment information based on PaymentID from Paypal APIs
 *
 * @param $paymentId
 *
 * @return Payment
 */
function getPaymentDetails($paymentId) {
    $payment = Payment::get($paymentId, getApiContext());
    return $payment;
}
