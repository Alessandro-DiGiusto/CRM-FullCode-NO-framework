<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
	header("Location: index.php");
}

/* -------------------------------------- */
$x = "Domestico";
$valore = 1;
$domestico = 2;
if (isset($_POST['submit'])) {
	$rSociale = $_POST['r_sociale'];
	$iban = $_POST['iban'];
	$email = ($_POST['email']);
	$cellulare = ($_POST['tel']);
	$stipula = ($_POST['stipula']);
	$stato = 1;
	$agente = ($_SESSION['userID']);

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

	$data_inserimento = $anno . $mese . $giorno;
	
	$sql = "INSERT INTO contratti (r_sociale, iban, email, tel, stipula, insert_date, stato, FK_id_users, via_for, cap_for, comune_for, citta_for, luce_gas, valore, domestico, data_inserimento)
        VALUES ('$rSociale_uc', '$iban', '$email', '$cellulare', '$stipula', '$insertDateDB2', '$stato', '$agente', '$viaFor', '$capFor', '$comuneFor', '$cittaFor_uc', '$x', '$valore', '$domestico', '$data_inserimento');";
	$result = mysqli_query($conn, $sql);

	if ($result) {
		echo "<script>alert('Contratto caricato correttamente.')</script>";
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

	<!--     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet"> -->

	<link rel="stylesheet" type="text/css" href="FIX-welcome-test-style.css">
	<!--     <link rel="stylesheet" type="text/css" href="style.css"> -->

	<title>JLAB Office</title>
</head>

<body>
	<div class="container-header">
		<div class="header-welcome">
			<img src="jlab-logo-alpha.png" class="logo-jlab">
			<div class="titolo">
				<?php
				$asd = $_SESSION['username'];
				echo "<h1>" . $asd . "</h1>";
				?>
			</div>

			<div class="input-group">
				<a href="contratti.php" style="text-decoration: none;">
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
				<span class="progress-label">✔ Privato</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">2</span>
				<span class="progress-label">✔ Domestico</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">3</span>
				<span class="progress-label">✔ Luce & Gas</span>
			</li>
			<li class="step-wizard-item">
				<span class="progress-count">4</span>
				<span class="progress-label">✔ Completato!</span>
			</li>
		</ul>
		<div class="container">
			<form action="" method="POST" class="login-email" id="formInserimento">

				<div class="container">
					<p class="login-text" style="font-size: 2rem; font-weight: 800;">✔ Fatto!<br>I dati sono stati inseriti correttamente</p>
				</div>

				<center>
					<table class="content-table">
						<thead>
							<tr>
								<th>Ragione Sociale</th>
								<th>Iban</th>
								<th>Email</th>
								<th>Cellulare</th>
								<th>Stipula</th>
								<th>Data Inserimento</th>
							</tr>
						</thead>
						<tbody>
							<tr class="active-row">
								<td style="color: black"><?php echo $rSociale_uc; ?></td>
								<td style="color: black"><?php echo $iban; ?></td>
								<td style="color: black"><?php echo $email; ?></td>
								<td style="color: black"><?php echo $cellulare; ?></td>
								<td style="color: black"><?php echo $stipula; ?></td>
								<td style="color: black"><?php echo $insertDateDB2; ?></td>
							</tr>
						</tbody>
					</table>
				</center>

				<input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->

				<div class="container">
					<div class="input-group" style="margin: 3rem">
						<a href="contratti_admin.php" style="text-decoration: none;" <button name="submit" class="btn">Lista Contratti</button></a>
					</div>
					<div class="input-group" style="margin: 3rem">
						<a href="FIX-tipo.php" style="text-decoration: none; background-color: #21D4FD" <button name="submit" class="btn">Nuovo Contratto</button></a>
					</div>
				</div>
			</form>
	</section>

</body>

</html>