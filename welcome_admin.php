<?php 

include 'config.php';

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
	$stato = "INSERITO";
	$agente = ($_SESSION['userID']);

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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="style.css">

    <title>JLAB Office</title>
</head>
<body>
    <div class="header-welcome">
        <?php 
			$asd = $_SESSION['username'];
			echo "<h1>" . $asd . "</h1>"; 
		?>
		<div class="input-group">
			<a href="contratti_admin.php" style="text-decoration: none;">
			<button class="css-selector" class="white-font">Lista contratti</button>
			</a>
		</div>
		<div class="input-group">
			<a href="logout.php" style="text-decoration: none;">
			<button class="css-selector_Logout" class="white-font">Esci</button>
			</a>
		</div>
    </div>

    <div class="container">
		<form action="" method="POST" class="login-email" id="formInserimento">

			<img src="jlab-logo-alpha.png" class="logo-jlab">

            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Carica</p>
			<div class="input-group">
				<input type="text" placeholder="Ragione Sociale" name="r_sociale" value="<?php echo $_POST['r_sociale']; ?>" required>
			</div>
			<div class="input-group">
				<input type="text" placeholder="IBAN" name="iban" value="<?php echo $_POST['iban']; ?>" required>
			</div>
			<div class="input-group">
				<input type="email" placeholder="email" name="email" value="<?php echo $_POST['email']; ?>" required>
            </div>
            <div class="input-group">
				<input type="tel" placeholder="Cellulare" name="tel" value="<?php echo $_POST['tel']; ?>" required>
			</div>
			<div class="input-group">
				Stipula
				<input type="date" name="stipula" value="<?php echo $_POST['stipula']; ?>" required>
			</div>

			<input  type="hidden" name="insert_date" value="<?php $d = new DateTime(); $dataInserimento = $d->format('H:i:s | \ d-m-Y'); echo $dataInserimento;?>">

			<input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->

			<div class="input-group">
				<button name="submit" class="btn">Carica</button>
			</div>
		</form>
	</div>


</body>
</html>