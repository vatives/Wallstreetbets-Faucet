<?php

ini_set('max_execution_time', 20);
require_once 'classes/jsonRPCClient.php';
require_once 'classes/recaptcha.php';
require_once 'config.php';
$et=md5(time()); // 32-bit etag value calculated based on current time in seconds since year 1970.
 header("ETag: \"".$et."\"",true);
?><!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title><?php echo $faucetTitle; ?></title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='shortcut icon' href='https://wallstreetbetsbros.com/wp-content/uploads/2021/01/cropped-stsmall507x507-pad600x600f8f8f8-1-192x192.jpg'>
    <link rel='icon' type='image/icon' href='images/favicon.ico'>

    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='/css/style.css'>

   

    <!--<script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'G-DJ857XFYY0-2', 'auto');
        ga('send', 'pageview');
    </script>-->
</head>

<body>

<div class='container'>

    <div id='login-form'>


        <h3><a href='./'><img src='<?php echo $logo; ?>' height='256'></a><br/><br/> <?php echo $faucetSubtitle; ?></h3>


        <fieldset>
			<footer class='clearfix'>
                    <a href="https://github.com/cryptodeveloperbro/WallStreetBetsCoin/releases/tag/2.0">Don't have a wallet? Get it here.</a>
                </footer>
            <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
        
            <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
            <br/>


            <?php

            $bitcoin = new jsonRPCClient('http://127.0.0.1:8070/',$walletAPIPassword);

            $balance = $bitcoin->balance();
			//error_log("balance = ".$balance);
			
            $balanceDisponible = $balance['unlocked'];
            $lockedBalance = $balance['locked'];
            $dividirEntre = 100000000;
            $totalBCN = ($balanceDisponible + $lockedBalance) / $dividirEntre;


            $recaptcha = new Recaptcha($keys);
            //Available Balance
            $balanceDisponibleFaucet = number_format(round($balanceDisponible / $dividirEntre, 12), 12, '.', '');
            ?>

            <form action='request.php' method='POST'>

                <?php if (isset($_GET['msg'])) {
                    $mensaje = $_GET['msg'];

                    if ($mensaje == 'captcha') {
                        ?>
                        <div id='alert' class='alert alert-error radius'>
                            Invalid captcha, enter the correct one.
                        </div>
                    <?php } else if ($mensaje == 'wallet') { ?>

                        <div id='alert' class='alert alert-error radius'>
                           Enter the correct WSB address.
                        </div>
                    <?php } else if ($mensaje == 'success') { ?>

                        <div class='alert alert-success radius'>
                            You Won <?php echo $_GET['amount']; ?> WSB.<br/><br/>
                            Will receive <?php echo $_GET['amount'] - 0.0001; ?> WSBs. (network fee  0.0001)<br/>
                            <a target='_blank'
                               href=http://explorer.wallstreetbetsbros.com/?hash=<?php echo $_GET['txid']; ?>#blockchain_transaction'>Check it out on the Blockchain.</a>
                        </div>
                    <?php } else if ($mensaje == 'paymentID') { ?>

                        <div id='alert' class='alert alert-error radius'>
                            Check your payment ID. <br>It must consist of 64 characters without special characters.
                        </div>
                    <?php } else if ($mensaje == 'notYet') { ?>

                        <div id='alert' class='alert alert-warning radius'>
                            WSB Coins are sent once every <?php echo $rewardEvery ?> hours. Come back later.
                        </div>
                    <?php } else if ($mensaje == 'dry') { ?>

                        <div id='alert' class='alert alert-warning radius'>
                           There are no WSB Coins now. Not at this time. Try again.
                        </div>
                    <?php } elseif ('erro_banco' == $mensaje) { ?>
                        <div id='alert' class='alert alert-warning radius'>
                            Database error, contact your administrator.
                        </div>
                    <?php }?>

                <?php } ?>
                <div class='alert alert-info radius'>
                    Balance: <?php echo $balanceDisponibleFaucet ?> WSB<br>
                    <?php

                    $link = new PDO('mysql:host=' . $hostDB . ';dbname=' . $database, $userDB, $passwordDB);

                    $query = 'SELECT SUM(payout_amount) / 100000000 FROM `payouts`;';

                    $result = $link->query($query);
                    $dato = $result->fetchColumn();

                    $query2 = 'SELECT COUNT(*) FROM `payouts`;';

                    $result2 = $link->query($query2);
                    $dato2 = $result2->fetchColumn();

                    ?>

                    Total Payed out <?php echo $dato; ?> WSB in <?php echo $dato2; ?> payments.
                </div>

                <?php if ($balanceDisponibleFaucet < 1.0) { ?>
                    <div class='alert alert-warning radius'>
                        The wallet is empty or the balance is less than the gain. <br> Come back later, &ndash; we can receive more donations.
                    </div>
                <?php } elseif (!$link) {
                    die('Erro na conexao com o banco de dados' . mysql_error());
                } else { ?>

                    <input type='text' name='wallet' required placeholder='WSB Wallet Address'>

                    <input type='text' name='paymentid' placeholder='Payment ID (Optional)'>
                    <br/>
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                 
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                    <br/>
                    <?php
                    echo $recaptcha->render();
                    ?>

                    <center><input type='submit' value='Get Free Coins!'></center>
                    <br>
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                   
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                <?php } ?>
                <br>
                <?php /*
           <div class='table-responsive'>
            <table class='table table-bordered table-condensed'>
              <thead>
                <tr>
                  <th><h6><b>Cleared Sites</b><br> <small>Sites that have their wallets allowed to request more than 1 time but only with a different payment id.</small></h6></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clearedAddresses as $key => $item) {
                  echo '<tr>
                  <th>'.$key.'</th>
                  </tr>';

                }?>
              </tbody>
            </table>
          </div>
*/ ?>

                <div class='table-responsive'>
                    <h6><b>Last 10 transactions</b></h6>
                    <table class='table table-bordered table-condensed'>
                        <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $deposits = ($bitcoin->transactions());

                        $transfers = array_reverse(($deposits['transactions']), true);
                        $contador = 0;
                        foreach ($transfers as $deposit) {
                           // if ($deposit['output'] == '') {
                                if ($contador < 11 && abs($deposit['transfers'][0]['amount'] / $dividirEntre) > 1 ) {
                                    $time = $deposit['timestamp'];
                                    echo '<tr>';
                                    echo '<th><script> var unix = new Date( "' . gmdate('Y-m-d H:i:s T', $time) . '").toLocaleString("en-US"); document.write(unix); </script> </th>';
									
									// echo '<th><script> var unix = new Date( ' . gmdate('m/d/Y H:i:s', $time) . ').toLocaleString("en-US", {timeZone: "Europe/Prague"}); document.write(unix); </script> </th>';
									
                                    echo '<th>' . round($deposit['transfers'][0]['amount'] / $dividirEntre, 12) . '</th>';
                                    echo '</tr>';
                                    $contador++;
                                }
                          //  }


                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <p style='font-size:0.8em;'>Donate WallStreetBets Coins to support this faucet.
                    <br>Faucet WSB Wallet:<br> <span style='font-size:.7em;color:darkblue'><?php echo $faucetAddress; ?></span>
                    <br>&#169; 2021 Faucet by drate</p></center>
                <footer class='clearfix'>
                    <a href="https://wallstreetbetsbros.com/">wallstreetbetsbros.com</a>
                </footer>
            </form>

        </fieldset>
    </div> <!-- end login-form -->

</div>
 <script>var isAdBlockActive = true;</script>
    <script src='/js/advertisement.js'></script>
    <script>
       // if (isAdBlockActive) {
       //     window.location = './adblocker.php'
      //  }
    </script>
<script src='//code.jquery.com/jquery-1.11.3.min.js'></script>
<?php if (isset($_GET['msg'])) { ?>
    <script>
        setTimeout(function () {
            $('#alert').fadeOut(3000, function () {
            });
        }, 10000);
    </script>
<?php } ?>
</body>
</html>
