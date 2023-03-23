<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
	header("Location: index.php");
}

/* -------------------------------------- */
$x = "Privato A";
$punti = 1;
$business = 1;
if (isset($_POST['submit'])) {
	$rSociale = $_POST['r_sociale'];
	$iban = $_POST['iban'];
	$email = ($_POST['email']);
	$cellulare = ($_POST['tel']);
	$stipula = ($_POST['stipula']);
	$stato = 1;
	$agente = ($_SESSION['userID']);
	$effic = ($_POST['yes_no']);
	$index_or_pun = ($_POST['index-pun']);
	$consAnnuo = ($_POST['consAnnuoUP']);
	$viaFor = ($_POST['viaFor']);
	$capFor = ($_POST['capFor']);
	$comuneFor = ($_POST['comuneFor']);
	$cittaFor = ($_POST['cittaFor']);

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

	$sql = "SELECT v_index FROM `valori` WHERE 1";
	$v_index = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($v_index);
	$v_index = $row['v_index'];

	$sql = "SELECT v_pun FROM `valori` WHERE 1";
	$v_pun = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($v_pun);
	$v_pun = $row['v_pun'];

	/* ============================== CALCOLO GETTONE LUCE AGENTE ===================== */
	$annuo_media = $consAnnuo / 12;

	if ($index_or_pun == 0) {
		$valore = $annuo_media * $v_index;

		if ($valore < 31) {
			$gettone = 20;
		} elseif ($valore >= 31 && $valore <= 100) {
			$gettone = 60;
		} elseif ($valore >= 101 && $valore <= 500) {
			$gettone = 120;
		} elseif ($valore >= 501 && $valore <= 1000) {
			$gettone = 240;
		} elseif ($valore >= 1001) {
			$gettone = ($valore * 30) / 100;
		}
	} else {
		if ($index_or_pun == 1) {
			$valore = $annuo_media * $v_pun;

			if ($valore < 31) {
				$gettone = 20;
			} elseif ($valore >= 31 && $valore <= 100) {
				$gettone = 60;
			} elseif ($valore >= 101 && $valore <= 500) {
				$gettone = 120;
			} elseif ($valore >= 501 && $valore <= 1000) {
				$gettone = 240;
			} elseif ($valore >= 1001) {
				$gettone = ($valore * 30) / 100;
			}
		}
	}
	/* ============================== FINE CALCOLO LUCE ===================== */

	$sql = "INSERT INTO contratti (r_sociale, iban, email, tel, stipula, insert_date, stato, FK_id_users, via_for, cap_for, comune_for, citta_for, luce, valore, business, data_inserimento, effic, index_or_pun, consAnnuo, gettone)
        VALUES ('$rSociale_uc', '$iban', '$email', '$cellulare', '$stipula', '$insertDateDB2', '$stato', '$agente', '$viaFor', '$capFor', '$comuneFor', '$cittaFor_uc', '$x', '$punti', '$business', '$data_inserimento', '$effic', '$index_or_pun', '$consAnnuo', '$gettone')";
	$result = mysqli_query($conn, $sql);

	if ($result) {
		echo "<script>alert('Contratto caricato correttamente.')</script>";
		$_POST['r_sociale'] = "";    	 /* $rSociale = "";  */
		$rSociale_uc = "";
		$_POST['iban'] = "";
		$iban = "";
		$_POST['email'] = "";   	     /* $email = ""; */
		$_POST['tel'] = "";
		$cellulare = "";
		$_POST['stipula'] = "";
		$stipula = "";
		$_POST['insert_date'] = "";
		$insertDate = "";
		$insertDateDB2 = "";
		$_POST['stato'] = "";
		$stato = "";
		$_POST['FK_id_users'] = "";
		$agente = "";
		$_POST['via_for'] = "";
		$viaFor = "";
		$_POST['cap_for'] = "";
		$capFor = "";
		$_POST['comune_for'] = "";
		$comuneFor = "";
		$_POST['citta_for'] = "";
		$cittaFor = "";
		$cittaFor_uc = "";
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

	

	<link rel="stylesheet" type="text/css" href="styleOK.css">
	<!--     <link rel="stylesheet" type="text/css" href="style.css"> -->

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
				<a href="contratti_admin.php" style="text-decoration: none;">
					<button class="css-selector-icon_account" title="Home"><img src='iconFornitures/home.png' style='width: 90px;'></button>
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
			<li class="step-wizard-item">
				<span class="progress-count">2</span>
				<span class="progress-label">✔ Domestico</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">3</span>
				<span class="progress-label">✔ Altri Usi</span>
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