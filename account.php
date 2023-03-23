<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

if(isset($_POST['submit'])) {
	$username = $_SESSION['username'];
	$email = $_POST['email'];
	$password = md5($_POST['password']);
	$newPw = md5($_POST['newPw']);
    $newPwC = md5($_POST['newPwC']);


    if($newPw == $newPwC) {
    $accountCheck = "SELECT * FROM users WHERE email='$email' AND password='$password'";
	$resultCK = mysqli_query($conn, $accountCheck);

    if ($resultCK->num_rows > 0) {
		$row = mysqli_fetch_assoc($resultCK);
		$_SESSION['username'] = $row['username'];
		$_SESSION['userID'] = $row['id'];          //--- $_SESSION['nome.A.piacere'] = $row['id'] (id è il nome effettivo della colonna del db)

        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
		$result = mysqli_query($conn, $sql);
		if ($result->num_rows > 0) {
			$sql = "UPDATE `users` SET `password`='$newPwC' WHERE users.email = '$email'";

			$result = mysqli_query($conn, $sql);
			if ($result) {
				echo "<script>alert('✅ Complimeti! Nuova password, impostata correttamente 🗝👌')</script>";
                $username = $_SESSION['username'];
                $email = "";
                $password = "";
                $newPw = "";
                $newPwC = "";
                $_POST['email'] = "";
                $_POST['password'] = "";
                $_POST['newPw'] = "";
                $_POST['newPwC'] = "";
			} else {
				echo "<script>alert('⛔️ Qualcosa e' andato storto.')</script>";
			}
		} else {
			echo "<script>alert('⛔️ Password attuale non corretta, riprova')</script>";
		} 
	} else {
        echo "<script>alert('⛔️ 👮🏻 Email/Password attuale non corretta, riprova 👀')</script>";
    }
} else {
    echo "<script>alert('⛔️ Le password nuove, non coincidono 👉🏽👈🏽')</script>";
}
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="styleOK.css">
    
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    

    <script src="https://kit.fontawesome.com/3890e69984.js" crossorigin="anonymous"></script>
    <title>My Office</title>
</head>

<body>
    <div class="container-header">
        <div class="header-welcome">
            <!-- <img src="img/man-technologist-medium-light-skin-tone.png" class="logo" style="width: 85px;"> -->
            <div class="titolo">
                <?php
                $nameLogin = $_SESSION['username'];
                echo "<h1 style='font-family: Poppins; font-weight: bold'>" . "Impostazione Account" . "</h1>";
                ?>
            </div>


            <div>
                <a href="contratti_admin.php" style="text-decoration: none;">
                    <button class="css-selector-icon_account" title="Home"><img src='iconFornitures/home.png' style='width: 90px;'></button>
                </a>
            </div>
            <div>
                <a href="" target="_blank" style="text-decoration: none;">
                    <button class="css-selector-icon" title="Calcolo Penali - Energia Reattiva || Questa è una prova, NOT AVAIABLE"><img src='iconFornitures/contatore.png' style='width: 50px; height: 50px'></button>
                </a>
            </div>
            <div>
                <a href="logout.php" style="text-decoration: none;">
                    <button class="css-selector_Logout" title="Chiudi la Sessione"><img src='iconFornitures/logout-icon.svg' style='width: 25px; height: 25px'></button>
                </a>
            </div>
        </div>
    </div>

    <!-- ##################################################################################### -->



    <div class="container">
        <form action="" method="POST" class="login-email" id="formInserimento">


            <a href="" style="text-decoration: none;">
                <button class="css-selector-icon_account" title="Impostazioni account"><img src='iconFornitures/account.png' style='width: 100px;'></button>
            </a>


            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Profilo<br> <?php echo $nameLogin; ?></p>
            <p style="font-size: 1rem; font-weight: small;">Aggiorna Password</P>
            <div class="input-group">
                <input type="text" placeholder="<?php $nameLogin = $_SESSION['username']; echo $nameLogin; ?>" name="r_sociale" value="<?php $nameLogin; ?>" readonly>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Vecchia Password" name="password" value="<?php echo $_POST['password']; ?>" required>
            </div>
            <div class="input-group">
                <input type="text" placeholder="Conferma con la tua Email" name="email" value="<?php echo $_POST['email']; ?>" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Nuova Password" name="newPw" value="<?php echo $_POST['newPw']; ?>" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Conferma Nuova Password" name="newPwC" value="<?php echo $_POST['newPwC']; ?>" required>
            </div>


            <input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->

            <div class="input-group">
                <button name="submit" class="btn">Aggiorna Password</button>
            </div>
        </form>
    </div>

    <!-- ##################################################################################### -->
    <input type="hidden" name="id" value="<?php echo "La sessione equivale a: " . $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->


    <!-- Copyright -->
    <div class="footer">
        <center>
            <img src="https://crm.alessandrodigiusto.it/assets/img/man-technologist-medium-light-skin-tone.png">
            <p><a href="https://alessandrodigiusto.it" target="_blank">
                    &lt;/Dev&gt;
                    with L</a>♥<a href="https://alessandrodigiusto.it" target="_blank">ve: <br>Alessandro Di Giusto</a></p>
        </center>
        <center>
            <p class="copyright"><a href="https://alessandrodigiusto.it/" target="_BLANK"> &copy; <script>
                        document.write(new Date().getFullYear())
                    </script> Copyright All Rights Reserved<br>alessandrodigiusto.it</a></p>
        </center>
    </div>
    <!-- Copyright -->


    </div>
    <!-- End of .container -->
</body>

</html>