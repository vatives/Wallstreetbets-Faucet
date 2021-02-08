<?php
require_once 'classes/recaptcha.php';
require_once 'classes/jsonRPCClient.php';
require_once 'config.php';

$link = new PDO('mysql:host=' . $hostDB . ';dbname=' . $database, $userDB, $passwordDB);

function randomize($min, $max)
{
    $range = $max - $min;
    $num = $min + $range * mt_rand(0, 32767) / 32767;

    return round($num, 3);
}

//Instantiate the Recaptcha class as $recaptcha
$recaptcha = new Recaptcha($keys);
if ($recaptcha->set()) {
    if ($recaptcha->verify($_POST['g-recaptcha-response'])) {
	  // if (TRUE) {
        //Checking address and payment ID characters
        $wallet = $str = trim(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['wallet']));
        $paymentidPost = $str = trim(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['paymentid']));
        //Getting user IP
        $direccionIP = $_SERVER['REMOTE_ADDR'];


        if (empty($wallet) OR (strlen($wallet) < 95)) {
            header('Location: ./?msg=wallet');
            exit();
        }

        if (empty($paymentidPost)) {
            $paymentID = '';
        } else {
            if ((strlen($paymentidPost) > 64) OR (strlen($paymentidPost) < 64)) {
                header('Location: ./?msg=paymentID');
                exit();
            } else {
                $paymentID = $paymentidPost;
            }
        }

        $queryCheck = "SELECT `id` FROM `payouts` WHERE `timestamp` > NOW() - INTERVAL $rewardEvery HOUR AND ( `ip_address` = '$direccionIP' OR `payout_address` = '$wallet' )";
        $resultCheck = $link->query($queryCheck);
        $count = 0;
        foreach ($resultCheck->fetchAll(PDO::FETCH_ASSOC) as $cou) {
            $count++;
        }
	//	if ($wallet == 'WSBCofTYqpDJVuPyySexZYEQyH1vgp4eLE1XJUKnUHfggeWGZzPpEzwESJdFCMDR8ZUypz1HqdNinFGBW1M68Jx9auuYHaY2cdG')
	//	{
	//		$count = 0;
	//	}
        if ($count) {
            header('Location: ./?msg=notYet');
            exit();
        }

        $bitcoin = new jsonRPCClient('http://127.0.0.1:8070/',$walletAPIPassword);
        $balance = $bitcoin->balance();
		error_log("balance");
		error_log(print_r($balance,true));
        $balanceDisponible = $balance['unlocked'];
		error_log(print_r($balanceDisponible,true));
        $transactionFee = 10000;
        $dividirEntre = 100000000;

        $hasta = number_format(round($balanceDisponible / $dividirEntre, 12), 2, '.', '');

        if ($hasta > $maxReward) {
            $hasta = $maxReward;
        }
        if ($hasta < ((float) $minReward + 0.1)) {
            header('Location: ./?msg=dry');
            exit();
        }

        $aleatorio = randomize($minReward, $hasta);

        $cantidadEnviar = ($aleatorio * $dividirEntre) - $transactionFee;


        $destination = array('amount' => $cantidadEnviar, 'address' => $wallet);
        $date = new DateTime();
        $timestampUnix = $date->getTimestamp() + 5;
        $peticion = array(
            'destinations' => $destination,
            'paymentId' => $paymentID,
            'fee' => $transactionFee,
            'mixin' => 0, 
            'unlockTime' => 0
        );
		error_log(print_r($peticion,true));
        $transferencia = $bitcoin->transfer($peticion);
		error_log(print_r($transferencia,true));
        if ($transferencia == 'Bad address') {
            header('Location: ./?msg=wallet');
            exit();
        }

        if (array_key_exists('transactionHash', $transferencia)) {
			$transactionID = $transferencia["transactionHash"];
            $query = "INSERT INTO `payouts` (`payout_amount`,`ip_address`,`payout_address`,`payment_id`,`transaction_id`,`timestamp`) VALUES ('$cantidadEnviar','$direccionIP','$wallet','$paymentID','$transactionID',NOW());";
			error_log($query);
            if ($link->exec($query)) {
                header('Location: ./?msg=success&txid=' . $transactionID . '&amount=' . $aleatorio);
            } else {
                header('Location: ./?msg=erro_banco');
            }

            exit();
        }


    } else {
        header('Location: ./?msg=captcha');
        exit();
    }
} else {
    header('Location: ./?msg=captcha');
    exit();
}

exit();
