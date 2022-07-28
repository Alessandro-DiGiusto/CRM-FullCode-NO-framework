<?php 

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

if (isset($_POST['submit'])) {
	$rSociale = $_POST['rSociale'];
	$iban = $_POST['iban'];
	$email = ($_POST['email']);
	$cellulare = ($_POST['cellulare']);
	$agente = ($_SESSION['userID']);

    

			$sql = "INSERT INTO contratti (r_sociale, iban, email, tel, FK_id_users)
					VALUES ('$rSociale', '$iban', '$email', '$cellulare', '$agente')";
			$result = mysqli_query($conn, $sql);
			if ($result) {
				echo "<script>alert('Contratto caricato correttamente.')</script>";
                $_POST['rSociale'] = "";
                $_POST['iban'] = "";
				$_POST['email'] = "";
				$_POST['cellulare'] = "";
			} else {
				echo "<script>alert('Qualcosa e' andato storto.')</script>";
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
        <?php echo "<h1>Ciao " . $_SESSION['username'] . " !</h1>"; ?>

		<div class="css-selector">
			<a href="contratti.php" class="white-font">Lista contratti</a>
		</div>

<!-- 		<div class="css-selector_INVERSO">
			<a href="#" class="white-font">Inserisci</a>
		</div> -->

		<div class="css-selector_Logout">
			<a href="logout.php" class="white-font">Logout</a>
		</div>

    </div>

<!-- 	<div class="container">
			<h1>Caricati</h1>				meglio cancellarlo, era una prova
	</div>
 -->
    <div class="container">
		<form action="" method="POST" class="login-email">

			<img src="jlab-logo-alpha.png" class="logo-jlab">

            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Carica</p>
			<div class="input-group">
				<input type="text" placeholder="Ragione Sociale" name="rSociale" value="<?php echo $_POST['rSociale']; ?>" required>
			</div>
			<div class="input-group">
				<input type="text" placeholder="IBAN" name="iban" value="<?php echo $_POST['iban']; ?>" required>
			</div>
			<div class="input-group">
				<input type="text" placeholder="email" name="email" value="<?php echo $_POST['email']; ?>" required>
            </div>
            <div class="input-group">
				<input type="text" placeholder="Cellulare" name="cellulare" value="<?php echo $_POST['cellulare']; ?>" required>
			</div>

<!-- 			<div class="input-group">
				<input type="text" placeholder="5 valore" name="rSociale" value="<?php echo $rSociale; ?>" required>
			</div>
			<div class="input-group">
				<input type="text" placeholder="6 valore" name="iban" value="<?php echo $iban; ?>" required>
			</div>
			<div class="input-group">
				<input type="text" placeholder="7 valore" name="email" value="<?php echo $_POST['email']; ?>" required>
            </div>
            <div class="input-group">
				<input type="text" placeholder="8 valore" name="cellulare" value="<?php echo $_POST['cellulare']; ?>" required>
			</div> -->

			<input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->

			<div class="input-group">
				<button name="submit" class="btn" onclick="reset">Carica</button>
			</div>

            <div class="input-group">
                <input type="reset" value="Svuota">
			</div>
		</form>
	</div>


</body>
</html>