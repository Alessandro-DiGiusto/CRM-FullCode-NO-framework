<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

$idSessione = $_SESSION['userID'];

$select = "SELECT r_sociale, iban, email, tel, stipula, insert_date, stato
           FROM contratti
           ";
                    
           $result_select = mysqli_query($conn, $select);

            // per stampare eventuali errori
            if (!$result_select) {
                echo "Errore query della select" . mysqli_error($conn);
            }
/* ------------------------------------------------------------------------ QUERY CONTEGGIO CONTRATTI IN TOTALE ------------- */
            $totContratti = "SELECT *
                             FROM contratti 
                             ";

            $queryContratti = mysqli_query($conn, $totContratti);
            $nContratti = mysqli_num_rows($queryContratti);

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
            <?php echo "<h1 id=h1-titolo>Ciao " . $_SESSION['username'] . " !</h1><br>"; ?>
            <?php echo "<h1 id=h1-titolo>Administrator " . "</h1>"; ?>
            </div>

            <div class="welcome_btn">
                <div class="input-group">
		    	<a href="welcome_admin.php" style="text-decoration: none;">
		    	    <button class="css-selector" class="white-font">Carica</button>
		    	</a>
		        </div>

                <div class="input-group">
			    <a href="logout.php" style="text-decoration: none;">
			        <button class="css-selector_Logout" class="white-font">Esci</button>
		      	</a>
		        </div>
            </div>
        </div>
    </div>

    <h2 id="h2-titolo">Inseriti Della rete: <?php echo $nContratti?> <p id="h3-titolo"> contratti in totale.</p> </h2>

    <table class="w3-table-all w3-center">
            <tr>
                <th>Ragione Sociale</th>
                <th>IBAN</th>
                <th>Email</th>
                <th>Cellulare</th>
                <th>Stipula</th>
                <th>Data Inserimento</th>
                <th class="w3-center">Stato</th>
            </tr>

    <?php

            while ($row = mysqli_fetch_assoc($result_select)) {
                        echo "<tr>" . "<td>" . $row['r_sociale'] ;
                        echo "<td>" . $row['iban'];
                        echo "<td>" . $row['email'];
                        echo "<td>" . $row['tel'];
                        echo "<td>" . $row['stipula'];
                        echo "<td>" . $row['insert_date']; //data inserimento
                        echo "<td><center>" . $row['stato'] . "</tr>";
                    }


    ?>
        </table> 
    
        <input  type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>">

<!-- ---------------------------------------------    AL MOMENTO QUESTO NON MI SERVE  -->
<!--     <div class="container_main">
        <div class="container-lista">
        <canvas id="oilChart" width="600" height="400"></canvas>
        </div>
    </div> -->
</body>
</html>



