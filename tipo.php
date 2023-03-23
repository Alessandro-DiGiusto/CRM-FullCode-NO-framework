<?php 

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");

		$_POST['r_sociale'] = "";    	 /* $rSociale = "";  */$rSociale_uc = "";
		$_POST['iban'] = "";     		 $iban = "";
		$_POST['email'] = "";   	     $email = "";
		$_POST['tel'] = "";   	         $cellulare = "";
		$_POST['stipula'] = "";   	     $stipula = "";
		$_POST['insert_date'] = "";   	 $insertDate = ""; $insertDateDB2 = "";
		$_POST['stato'] = "";   	     $stato = "";
		$_POST['FK_id_users'] = "";   	 $agente = "";
		$_POST['via_for'] = "";   	     $viaFor = "";
		$_POST['cap_for'] = "";   	     $capFor = "";
		$_POST['comune_for'] = "";   	 $comuneFor = "";
		$_POST['citta_for'] = "";   	 $cittaFor = ""; $cittaFor_uc = "";

} else {
    $_POST['r_sociale'] = "";    	 /* $rSociale = "";  */$rSociale_uc = "";
    $_POST['iban'] = "";     		 $iban = "";
    $_POST['email'] = "";   	     $email = "";
    $_POST['tel'] = "";   	         $cellulare = "";
    $_POST['stipula'] = "";   	     $stipula = "";
    $_POST['insert_date'] = "";   	 $insertDate = ""; $insertDateDB2 = "";
    $_POST['stato'] = "";   	     $stato = "";
    $_POST['FK_id_users'] = "";   	 $agente = "";
    $_POST['via_for'] = "";   	     $viaFor = "";
    $_POST['cap_for'] = "";   	     $capFor = "";
    $_POST['comune_for'] = "";   	 $comuneFor = "";
    $_POST['citta_for'] = "";   	 $cittaFor = ""; $cittaFor_uc = "";
}

/* -------------------------------------- */


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <link rel="stylesheet" type="text/css" href="styleOK.css">
<!--     <link rel="stylesheet" type="text/css" href="styleOK.css"> -->

    <title>My Office</title>
</head>
<body>
<div class="container-header">
<div class="header-welcome">
<!-- <img src="img/man-technologist-medium-light-skin-tone.png" class="logo" style="width: 85px;"> -->
        <div class="titolo">
        <?php 
			$nameLogin = $_SESSION['username'];
			echo "<h1>" . $nameLogin  . "  </h1>"; 
		?>
        </div>

        <div>
            <a href="./contratti_admin.php" style="text-decoration: none;">
                <button class="css-selector-icon_account" title="Home"><img src='./iconFornitures/home.png'></button>
            </a>
        </div>
		<div class="input-group">
			<a href="../logout.php" style="text-decoration: none;">
			<button class="css-selector_Logout" class="white-font">Esci</button>
			</a>
		</div>
    </div>
</div>
 <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class="step-wizard-item current-item">
                <span class="progress-count">1</span>
                <span class="progress-label">Tipo Fornitura</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">2</span>
                <span class="progress-label">Tipologia</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">Contratto</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">4</span>
                <span class="progress-label">Dati Cliente</span>
            </li>
            <li class="step-wizard-item" id="li-completato">
                <span class="progress-count">5</span>
                <span class="progress-label">Completato!</span>
            </li>
        </ul>
        <div class="container">
		<form action="add/privato.php" method="POST" class="login-email" id="formInserimento">
            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Tipo Fornitura</p>

            <div class="input-group">
                <!-- <a href="add/privato.php" style="text-decoration: none;"> -->
				<button class="btn" name="btn-scelta" value="1">Privato</button>
                <!-- </a> -->
			</div>

            <div class="input-group">
                <a href="add/azienda.php" style="text-decoration: none;">
				<button class="btn" formaction="add/azienda.php" name="btn-scelta" value="2">Azienda</button>
                </a>
			</div>

			<input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
		</form>
	</div>
    </section>
</body>
</html>