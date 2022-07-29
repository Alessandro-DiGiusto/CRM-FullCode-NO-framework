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

    <link rel="stylesheet" type="text/css" href="contratti.css">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">


    <title>JLAB Office</title>
</head>
<body>

    <div class="header-welcome">
        <div class="header-welcome-cose">
            <div class="titolo">
            <?php echo "<h1 id=h1-titolo>Ciao " . $_SESSION['username'] . " !</h1>"; ?>
            </div>


            <div class="welcome-btn">
                <div class="css-selector_INVERSO">
                <a href="welcome.php" class="white-font">Carica</a>
                </div>
            </div>

            <div class="css-selector_Logout">
            <a href="logout.php" class="white-font">Logout</a>
            </div>
            </div>
        </div>
    </div>

    <h2 id=h2-titolo>I Tuoi Inseriti</h2>
    <div class="w3-container">
    

        <table class="w3-table-all">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th class="w3-center">Points</th>
            </tr>
            <tr>
                <td>Jill</td>
                <td>Smith</td>
                <td class="w3-center">50</td>
            </tr>
            <tr>
                <td>Eve</td>
                <td>Jackson</td>
                <td class="w3-center">94</td>
            </tr>
            <tr>
                <td>Adam</td>
                <td>Johnson</td>
                <td class="w3-center">67</td>
            </tr>
        </table>

    <div class="container_main">
        <div class="container-lista">
            <h1>I tuoi inseriti</h1>
            <table>
                <tr>
                    <th class="th-space">Ragione Sociale</th>
                    <th>Iban</th>
                    <th>Email</th>
                </tr>
                <tr>
                    <td class="td-space">Pizzeria ok S.r.l.s</td>
                    <td>IT7685CC3486985</td>
                    <td>pizza@gustosa.com</td>
                </tr>
                <tr>
                    <td>C.C. Etnapolis S.p.A</td>
                    <td>IT998711111CC8575K12</td>
                    <td>info@etnapolis.it</td>
                </tr>
            </table>
        </div>
    </div>



</body>
</html>