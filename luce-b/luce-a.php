<?php 

include '../config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <link rel="stylesheet" type="text/css" href="../styleOK.css">
<!--     <link rel="stylesheet" type="text/css" href="styleOK.css"> -->

<title>My office</title>
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
                <span class="progress-label">✔ Azienda</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">2</span>
                <span class="progress-label">✔ Altri Usi</span>
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
	<form action="luce-a..php" method="POST" class="login-email" id="formInserimento">
        <div class="container">
			<p class="login-text" style="font-size: 2rem; font-weight: 800;">Dati Cliente</p>
            <div class="input-group">
				<input type="text" placeholder="Ragione Sociale" name="r_sociale" value="<?php echo $_POST['r_sociale']; ?>" required>
			</div>
            <div class="input-group">
				<input type="text" placeholder="Iban" name="iban" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_POST['iban']; ?>" required>
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
				<input type="text" placeholder="Via e numero civico" name="viaFor" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_POST['viaFor']; ?>" required>
			</div>
			<div class="input-group">
				<input type="number" placeholder="CAP" name="capFor" value="<?php echo $_POST['capFor']; ?>" required>
            </div>
			<div class="input-group">
				<input type="text" placeholder="Comune" name="comuneFor" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_POST['comuneFor']; ?>" required>
			</div>
            <div class="input-group">
				<input type="text" placeholder="Provincia" name="cittaFor" maxlength="2" oninput="this.value = this.value.toUpperCase()" value="<?php echo $_POST['cittaFor']; ?>" required>
			</div>
        </div>

        <div class="container">
                    <div class="container">
                    <label for="yes_no_radio">Il Cliente ha stipulato   <br>anche efficentamento?   </label>
                    <div class="input-groupl">
                        <p>No</p>
                        <input type="radio" name="yes_no" value="0" checked></input>
                        <p>Si</p>
                        <input type="radio" name="yes_no" value="1"></input>
                    </div>
                    </div>
                    
                    <div class="container">
                        <label for="index_pun_radio">Listino applicato?   </label>
                        <div class="input-groupl">
                            <p>Index
                                <input type="radio" name="index-pun" value="0" checked></input>
                            </p>
                            <p>PUN
                                <input class="input-groupl" type="radio" name="index-pun" value="1"></input>
                            </p>
                        </div>
                    </div>
            </div>   
            
            <div class="container">
                <div class="input-group"><p>Luce</p>
                    <input class="input-group" type="number" name="consAnnuoUP" placeholder="Consumo Annuo">
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
</body>
</html>