<?php 

include 'config.php';

session_start();

error_reporting(0);

if (isset($_SESSION['username'])) {
    header("Location: welcome.php");
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

		if($email === 'silvia@gmail.com'){
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

	<title id="hidden-title">Login JLAB</title>
</head>
<body>
	<div class="container">
		<form action="" method="POST" class="login-email">

			<img src="jlab-logo-alpha.png" class="logo-jlab">

			<p class="login-text" style="font-size: 2rem; font-weight: 800;">OFFICE</p>

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
			<p class="login-register-text">Sei un nuovo agente?<a href="register.php"> Registrati Qui.</a></p>
			</center>  
			

		</form>
	</div>
</body>
</html>