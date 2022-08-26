<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

$idSessione = $_SESSION['userID'];

$select = "SELECT r_sociale, iban, email, tel, stipula, insert_date, stato, luce, gas, luce_gas
           FROM contratti
           WHERE contratti.FK_id_users = '$idSessione'; ";
                    
           $result_select = mysqli_query($conn, $select);

            // per stampare eventuali errori
            if (!$result_select) {
                echo "Errore query della select" . mysqli_error($conn);
            }
/* ------------------------------------------------------------------------ QUERY CONTEGGIO CONTRATTI IN TOTALE ------------- */
            $totContratti = "SELECT *
                             FROM contratti 
                             WHERE contratti.FK_id_users = '$idSessione'";

            $queryContratti = mysqli_query($conn, $totContratti);
            $nContratti = mysqli_num_rows($queryContratti);
            $a = 5;
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="FIX-welcome-test-style.css">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
<!--     <link rel="stylesheet" type="text/css" href="contratti.css"> -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>JLAB Office</title>
</head>

<body>
    <div class="container-header">
        <div class="header-welcome">
            <img src="jlab-logo-alpha.png" class="logo-jlab">
            <div class="titolo">
                <?php
                $asd = $_SESSION['username'];
                echo "<h1>Ciaooo " . $asd  . " !</h1>";
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
            <li class="step-wizard-item current-item">
                <span>1</span>
                <span>Privato Domestico</span>
            </li>
            <li class="step-wizard-item">
                <span>2</span>
                <span>Privato Altri Usi</span>
            </li>
            <li class="step-wizard-item">
                <span><?php echo $a ?></span>
                <span>Business</span>
            </li>
            <li class="step-wizard-item">
                <span>4</span>
                <span>Dati Cliente</span>
            </li>
            <li class="step-wizard-item" id="li-completato">
                <span>5</span>
                <span>Completato!</span>
            </li>
        </ul>
        <div class="container">
            <p class="login-text" style="font-size: 2rem; font-weight: 800;">Totale Inseriti: <?php echo $nContratti ?></p>

            <!-- <h2 id="h2-titolo">I Tuoi Inseriti: id="h3-titolo"> contratti in totale.</p>
            </h2> -->
        </div>
        <!-- <div class="container"> -->
        <table class="w3-table-all w3-center">
                <tr>
                    <th>Ragione Sociale</th>
                    <th>IBAN</th>
                    <th>Email</th>
                    <th>Cellulare</th>
                    <th>
                        <center>Luce
                    </th>
                    <th>
                        <center>Gas
                    </th>
                    <th>
                        <center>Luce & Gas
                    </th>
                    <th>Stipula</th>
                    <th>Data Inserimento</th>
                    <th class="w3-center">Stato</th>
                </tr>

                <?php
                while ($row = mysqli_fetch_assoc($result_select)) {
                    echo "<tr>" . "<td>" . $row['r_sociale'];
                    echo "<td>" . $row['iban'];
                    echo "<td>" . $row['email'];
                    echo "<td>" . $row['tel'];
                    echo "<td><center>" . "<div id=td-color>" . $row['luce'];
                    echo "<td><center>" . "<div id=td-color2>" . $row['gas'];
                    echo "<td><center>" . "<div id=td-color3>" . $row['luce_gas'];
                    echo "<td>" . $row['stipula'];
                    echo "<td>" . $row['insert_date']; //data inserimento
                    echo "<td><center>" . $row['stato'] . "</tr>";
                }
                ?>
                <style type="text/css">
                    #td-color {
                        color: limegreen;
                        background-color: limegreen;
                    }

                    #td-color2 {
                        color: red;
                        background-color: red;
                    }

                    #td-color3 {
                        color: indigo;
                        background-color: indigo;
                    }
                </style>
            </table>
        <!-- </div> -->
        <div class="container">
        <div class="input-group">
                <a href="FIX-tipo-privato.php" style="text-decoration: none;"
                <button class="btn" name="btn-scelta" value="1">Privato</button>
                </a>
            </div>

            <div class="input-group">
                <a href="FIX-tipo-azienda.php" style="text-decoration: none;"
                <button class="btn" name="btn-scelta" value="2">Azienda</button>
                </a>
            </div>
        </div>


            <input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
        <!-- </div> -->
    </section>
</body>

</html>