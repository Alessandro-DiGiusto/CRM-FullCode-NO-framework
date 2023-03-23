<?php 

include '../config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

if (isset($_POST['submit'])) {
	$rSociale = $_POST['r_sociale'];
	$iban = $_POST['iban'];
	$email = ($_POST['email']);
	$cellulare = ($_POST['tel']);
	$stipula = ($_POST['stipula']);
	$insertDate = ($_POST['insert_date']);
	$stato = 1;
	$agente = ($_SESSION['userID']);
    $scelta = ($_POST['btn-scelta']);

	/* 	Set first uppercase on fist letter, on username left send on database*/
	$rSociale_uc = ucwords($rSociale);

		$sql = "INSERT INTO contratti (r_sociale, iban, email, tel, stipula, insert_date, stato, FK_id_users)
				VALUES ('$rSociale_uc', '$iban', '$email', '$cellulare', '$stipula', '$insertDate', '$stato', '$agente')";
		$result = mysqli_query($conn, $sql);
		if ($result) {
		echo "<script>alert('Contratto caricato correttamente.')</script>";
		$_POST['r_sociale'] = "";    	 $rSociale = "";
		$_POST['iban'] = "";     		 $iban = "";
		$_POST['email'] = "";   	     $email = "";
		$_POST['tel'] = "";   	         $cellulare = "";
		$_POST['stipula'] = "";   	     $stipula = "";
		$_POST['insert_date'] = "";   	 $insertDate = "";
		$_POST['stato'] = "";   	     $stato = "";
		$_POST['FK_id_users'] = "";   	 $agente = "";
	} else {
		echo "<script>alert('ERRORE: Il contratto è già stato caricato correttamente')</script>";
		}
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="../styleOK.css">
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
            <a href="../contratti_admin.php" style="text-decoration: none;">
                <button class="css-selector-icon_account" title="Home"><img src='../iconFornitures/home.png'></button>
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
                <span class="progress-label">✔ Privato</span>
            </li>
            <li class="step-wizard-item current-item">
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
		<form action="../domestic/domestico.php" method="POST" class="login-email" id="formInserimento">
            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Tipologia <?php echo $scelta ?></p>
            <div class="input-group">
                
				<button class="btn" name="btn-scelta" value="1">Domestico</button>
                
			</div>
            <div class="input-group">
                <a href="./domestic/altriusi.php" style="text-decoration: none;">
				<button class="btn" formaction="./domestic/altriusi.php" name="btn-scelta" value="2">Altri Usi</button>
                </a>
			</div>
			<input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
		</form>

	</div>
    </section>
</body>
</html>