<?php 

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

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
			echo "<h1>Ciaooo " . $asd . " !</h1>"; 
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
                <span class="progress-label">✔ Privato</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">2</span>
                <span class="progress-label">✔ Domestico</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">✔ Luce</span>
            </li>
            <li class="step-wizard-item current-item">
                <span class="progress-count">4</span>
                <span class="progress-label">Dati Cliente</span>
            </li>
            <li class="step-wizard-item" id="li-completato">
                <span class="progress-count">5</span>
                <span class="progress-label">Completato!</span>
            </li>
        </ul>
</section>
<div class="container">
	<form action="FIX-tipo-privato-domestico1.1.php" method="POST" class="login-email" id="formInserimento">
        <div class="container">
			<p class="login-text" style="font-size: 2rem; font-weight: 800;">Dati Cliente</p>
            <div class="input-group">
				<input type="text" placeholder="Ragione Sociale" name="r_sociale" value="<?php echo $_POST['r_sociale']; ?>" required>
			</div>
            <div class="input-group">
				<input type="text" placeholder="Iban" name="iban" value="<?php echo $_POST['iban']; ?>" required>
			</div>
			<div class="input-group">
				<input type="email" placeholder="email" name="email" value="<?php echo $_POST['email']; ?>" required>
            </div>
            <div class="input-group">
				<input type="tel" placeholder="Cellulare" name="tel" value="<?php echo $_POST['tel']; ?>" required>
			</div>
		</div>

        <div class="container">
		    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Sede Fornitura</p>
			<div class="input-group">
				<input type="text" placeholder="Via e numero civico" name="viaFor" value="<?php echo $_POST['viaFor']; ?>" required>
			</div>
			<div class="input-group">
				<input type="number" placeholder="CAP" name="capFor" value="<?php echo $_POST['capFor']; ?>" required>
            </div>
			<div class="input-group">
				<input type="text" placeholder="Comune" name="comuneFor" value="<?php echo $_POST['comuneFor']; ?>" required>
			</div>
            <div class="input-group">
				<input type="text" placeholder="Città" name="cittaFor" value="<?php echo $_POST['cittaFor']; ?>" required>
			</div>
        </div>

		<div class="container">
			<input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->

			<div class="input-group">
			<p class="login-text" style="font-size: 2rem; font-weight: 800;">Stipula</p>
				<input type="date" name="stipula" value="<?php echo $_POST['stipula']; ?>" required>
			</div>
		</div>

			<div class="input-group">
				<button name="submit" class="btn">Carica</button>
			</div>

	</form>
</div>




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