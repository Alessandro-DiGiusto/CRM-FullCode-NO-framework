<?php 

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

if (isset($_POST['submit'])) {
	$_SESSION['staminchia'] = $_POST['r_sociale'];
	$agente = ($_SESSION['userID']);

	/* 	Set first uppercase on fist letter, on username left send on database*/
	$rSociale_uc = ucwords($rSociale);

}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet"> -->

    <link rel="stylesheet" type="text/css" href="FIX-welcome-test-style.css">
<!--     <link rel="stylesheet" type="text/css" href="style.css"> -->

<title >JLAB Office</title>
</head>
<body>
<div class="container-header">
<div class="header-welcome">
<img src="jlab-logo-alpha.png" class="logo-jlab">
        <div class="titolo">
        <?php 
			$asd = $_SESSION['username'];
			echo "<h1>Ciaooo " . $asd  . " !</h1>"; 
		?>
        </div>

		<div class="input-group">
			<a href="contratti2.php" style="text-decoration: none;">
			<button class="css-selector" class="white-font">Lista contratti</button>
			</a>
		</div>
		<div class="input-group">
			<a href="logout.php" style="text-decoration: none;">
			<button class="css-selector_Logout" class="white-font">Esci</button>
			</a>
		</div>
    </div>
</div>
 <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span class="progress-count">1</span>
                <span class="progress-label">✔ Privato<br>✔ Domestico<br>✔ Luce</span>
            </li>
            <li class="step-wizard-item current-item">
                <span class="progress-count">2</span>
                <span class="progress-label">Dati Cliente</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label"><?php echo $rSociale_diprima ?></span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">4</span>
                <span class="progress-label">Ultimi Dati</span>
            </li>
            <li class="step-wizard-item" id="li-completato">
                <span class="progress-count">5</span>
                <span class="progress-label">Completato!</span>
            </li>
        </ul>
        <div class="container">

    </section>

<form action="" method="POST" class="login-email" id="formInserimento">

 <script type="text/javascript" language="JavaScript">

function hidediv(name) {
document.getElementById(name).style.visibility = 'hidden';
}

function showdiv(name) {
document.getElementById(name).style.visibility = 'visible';
}


</script>
<body onload="hidediv('b');showdiv('a')">

<div id="a">
<div class="input-group">
<input type="text" placeholder="ragione sociale" name="r_sociale" >
</div>
<a href="prova2.php"
<button name="submit" class="btn" id="btn" onclick="hidediv('a');showdiv('b')">Invia Dati</button></a>
</a>
</div>



<div id="b" class="input-group">
<input type="button" onclick="hidediv('b');showdiv('a')" value="carica">
				<!-- 	<button onclick="hidediv('b');showdiv('a')" class="btn">Avanti</button> -->
				
</div>
</form>
<!--     <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span class="progress-count">1</span>
                <span class="progress-label">Billing Info</span>
            </li>
            <li class="step-wizard-item current-item">
                <span class="progress-count">2</span>
                <span class="progress-label">Payment Method</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">Checkout</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">4</span>
                <span class="progress-label">alessandro</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">5</span>
                <span class="progress-label">Success</span>
            </li>
        </ul>
    </section> -->

</body>
</html>