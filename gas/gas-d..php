<?php

include '../config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
	header("Location: index.php");
}

/* -------------------------------------- */
$x = "Domestico";
$punti = 0.5;
$domestico = 1;
if (isset($_POST['submit'])) {
	$rSociale = $_POST['r_sociale'];
	$iban = $_POST['iban'];
	$email = ($_POST['email']);
	$cellulare = ($_POST['tel']);
	$stipula = ($_POST['stipula']);
	$stato = 1;
	$agente = ($_SESSION['userID']);
	$consAnnuoGas = ($_POST['consAnnuoUP-gas']);
	$viaFor = ($_POST['viaFor']);
	$capFor = ($_POST['capFor']);
	$comuneFor = ($_POST['comuneFor']);
	$cittaFor = ($_POST['cittaFor']);
	$index_or_pun_gas = ($_POST['index-pun-gas']);
	$effic = ($_POST['yes_no']);
	/* 	Set first uppercase on fist letter, on username left send on database*/
	$rSociale_uc = ucwords($rSociale);
	$cittaFor_uc = strtoupper($cittaFor);

	$d = new DateTime();
	$insertDateDB2 = $d->format('d-m-Y | \ H:i:s');
	$giorno = $d->format('d');
	$mese = $d->format('m');
	$anno = $d->format('Y');
	$orario = $d->format('H:i:ss');

	$data_inserimento = $anno . $mese . $giorno;

	$gettone = 20;

	$sql = "INSERT INTO contratti (r_sociale, iban, email, tel, stipula, insert_date, stato, FK_id_users, via_for, cap_for, comune_for, citta_for, gas, valore, domestico, gettone, data_inserimento, consAnnuoGas, index_or_pun_gas, effic)
        VALUES ('$rSociale_uc', '$iban', '$email', '$cellulare', '$stipula', '$insertDateDB2', '$stato', '$agente', '$viaFor', '$capFor', '$comuneFor', '$cittaFor_uc', '$x', '$punti', '$domestico', '$gettone', '$data_inserimento', '$consAnnuoGas', '$index_or_pun_gas', '$effic')";

	$result = mysqli_query($conn, $sql);

	if ($result) {
		echo "<script>alert('Contratto caricato correttamente.')</script>";
		$rSociale = "";
		$iban = "";
		$email = "";
		$cellulare = "";
		$stipula = "";
		$stato = "";
		$consAnnuo = "";
		$consAnnuoGas = "";
		$viaFor = "";
		$capFor = "";
		$comuneFor = "";
		$cittaFor = "";
		$index_or_pun_gas = "";
		$index_or_pun = "";
		$effic = "";
		$rSociale_uc = "";
		$cittaFor_uc = "";
		$d = "";
		$insertDateDB2 = "";
		$giorno = "";
		$mese = "";
		$anno = "";
		$orario = "";
		$data_inserimento = "";
		$gettone = "";
		$x = "";
		$punti = "";
		$domestico = "";
		$business = "";
	} else {
		echo "<script>alert('ERRORE: Il contratto è già stato caricato correttamente ')</script>";
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
					<button class="css-selector-icon_account" title="Home"><img src='../iconFornitures/home.png' style='width: 90px;'></button>
				</a>
			</div>
			<div>
				<a href="../tipo.php" style="text-decoration: none;">
					<button class="css-selector-icon_account" title="Nuovo Contratto"><img src='../iconFornitures/add-file.png' style='width: 90px;'></button>
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
			<li class="step-wizard-item">
				<span class="progress-count">1</span>
				<span class="progress-label">✔ Privato</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">2</span>
				<span class="progress-label">✔ Domestico</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">3</span>
				<span class="progress-label">✔ Gas</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">4</span>
				<span class="progress-label">✔ Dati Cliente</span>
			</li>
			<li class="step-wizard-item" id="li-completato">
				<span class="progress-count">5</span>
				<span class="progress-label">✔ Completato!</span>
			</li>
		</ul>
		<p>Contratto caricato correttamente</p>
		<input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"></input>
		<!-- i am taking the id value corresponding to the agent database row -->
	</section>
</body>
</html>