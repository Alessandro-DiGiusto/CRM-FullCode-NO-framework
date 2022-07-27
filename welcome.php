<?php 

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="style.css">

    <title>JLAB Office</title>
</head>
<body>
    <?php echo "<h1>Ciao " . $_SESSION['username'] . " !</h1>"; ?>
    <a href="logout.php">Logout</a>

    <div class="container">
		<form action="" method="POST" class="login-email">

			<img src="jlab-logo-alpha.png" class="logo-jlab">

            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Office</p>
			<div class="input-group">
				<input type="text" placeholder="Ragione Sociale" name="username" value="<?php echo $username; ?>" required>
			</div>
			<div class="input-group">
				<input type="email" placeholder="IBAN" name="email" value="<?php echo $email; ?>" required>
			</div>
			<div class="input-group">
				<input type="password" placeholder="Email" name="password" value="<?php echo $_POST['password']; ?>" required>
            </div>
            <div class="input-group">
				<input type="password" placeholder="Cellulare" name="cpassword" value="<?php echo $_POST['cpassword']; ?>" required>
			</div>
			<div class="input-group">
				<button name="submit" class="btn">Carica</button>
			</div>
		</form>
	</div>


</body>
</html>