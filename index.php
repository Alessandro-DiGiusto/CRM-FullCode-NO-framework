<?php

include 'config.php';

session_start();

error_reporting(0);
$_SESSION['id'] = "";

if (isset($_SESSION['username'])) {
	header("Location: contratti_admin.php");
}

if (isset($_POST['submit'])) {
	$email = $_POST['email'];
	$password = md5($_POST['password']);

	$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
	$result = mysqli_query($conn, $sql);

	if ($result->num_rows > 0) {
		$row = mysqli_fetch_assoc($result);
		$_SESSION['username'] = $row['username'];
		$_SESSION['userID'] = $row['id'];          //--- $_SESSION['nome.A.piacere'] = $row['id'] (id è il nome effettivo della colonna del db)

		if ($email === 'silvia@gmail.com') {
			header("Location: contratti_admin.php");
		} else {
			header("Location: contratti.php");
		}
	} else {
		echo "<script>alert('Email o Password errate. Riprova.')</script>";
	}
}
$idSessione = $_SESSION['userID'];
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="style.css">

	<title style="color: #0000">Accedi</title>
	<link rel="icon" href="" type="image/x-icon">

</head>

<body>
	<div class="container">
		<form action="" method="POST" class="login-email">
			<img src="img/man-technologist-medium-light-skin-tone.png" class="logo-site">

			<p class="login-text" style="font-size: 2rem; font-weight: 800;">MY OFFICE</p>

			<div class="input-group">
				<input type="email" placeholder="Email" name="email" value="<?php echo $_POST['email']; ?>" required>
			</div>

			<div class="input-group">
				<input type="password" placeholder="Password" name="password" value="<?php echo $_POST['password']; ?>" required>
			</div>
			<div class="input-group">
				<button name="submit" class="btn">Accedi</button>
			</div>

			<center>
				<p class="login-register-text">Problemi di accesso?
				<a href="mailto:me@alessandrodigiusto.it?cc=developer@alessandrodigiusto.it&subject=Pagina%20di%20Login%20CRM%20-%20Help%20Desk&body=Salve%2C%20sto%20riscontrando%20un%20problema%20nella%20pagina%20di%20Login%20del%20CRM%0D%0AL'indirizzo%20%C3%A8%20crm.alessandrodigiusto.it%0D%0A%0D%0AIl%20mio%20nome%20%C3%A8%3A%20____(INSERIRE%20IL%20PROPRIO%20NOME%20COGNOME)___________%0D%0A%0D%0AGrazie%20mille%20e%20Buon%20lavoro!%20%E2%99%A5%0D%0A">Contatta il Supporto Tecnico.</a></p>
			</center>
		</form>
	</div>



	<!-- Copyright -->
	<div class="footer">

		<!-- <img src="https://crm.alessandrodigiusto.it/assets/img/man-technologist-medium-light-skin-tone.png"> -->
		
		<p>
			<center>
			<a href="https://alessandrodigiusto.it" target="_blank" class="copyright">
				&lt;/Dev&gt; with L♥ve<br>Alessandro Di Giusto
			</a>
			</center>
		</p>

		<p class="copyright">
			<center>
			<a href="https://alessandrodigiusto.it/" target="_BLANK"> &copy;
				Copyright All Rights Reserved<br>alessandrodigiusto.it
			</a>
			<center>
		</p>

	</div>
	<!-- Copyright -->

</body>
</html>