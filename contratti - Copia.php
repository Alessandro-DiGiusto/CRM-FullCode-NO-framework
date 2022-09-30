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


/* ----------------------------------- QUERY CONTEGGIO CONTRATTI BUSINESS IN TOTALE ------------- */
            $querySum = "SELECT SUM(business) AS business FROM contratti where domestico = '0';";
            $result = mysqli_query($conn, $querySum); 
            $row = mysqli_fetch_assoc($result); 
            $sumCtrBus = $row['business'];
/* ----------------------------------- QUERY CONTEGGIO CONTRATTI DOMESTICI IN TOTALE ------------- */
            $querySum = "SELECT SUM(domestico) AS domestico FROM contratti where business = '0';";
            $result = mysqli_query($conn, $querySum); 
            $row = mysqli_fetch_assoc($result); 
            $sumCtrDom = $row['domestico'];

/* --------------------------- QUERY CONTEGGIO CONTRATTI basso valore 0,5 Punti ------------- */
            $querySum = "SELECT SUM(valore) AS valoreB FROM contratti where business = '0';";
            $result = mysqli_query($conn, $querySum); 
            $row = mysqli_fetch_assoc($result); 
            $totPBV = $row['valoreB'];
/* --------------------------- QUERY CONTEGGIO CONTRATTI ALTO valore 1 Punto ------------- */
            $querySum = "SELECT SUM(valore) AS valoreA FROM contratti where domestico = '0';";
            $result = mysqli_query($conn, $querySum); 
            $row = mysqli_fetch_assoc($result); 
            $totPAV = $row['valoreA'];

/* -----------------------------------  QUERY CONTEGGIO CONTRATTI IN TOTALE ------------- */
            $totContratti = $sumCtrBus + $sumCtrDom;
            $totPunteggio = $totPAV + $totPBV;



/* --------------------------- Logica raggiungimendo obiettivi ------------- */
function conteggio($totPunteggio, $totPAV, $totPBV, $totContratti){    
    if($totPunteggio < 9){
        echo $totPAV . "/7" . " Alto Valore " . "\n" . $totPBV . "/2" . " Basso Valore";
    } else {
        if($totPunteggio >= 9 && $totPunteggio < 12){
            $sett = ($totPunteggio * 70)/100;
            $settRound = ceil($sett);
            $tren = ($totPunteggio * 30)/100;
            $trenRound = floor($tren);
            $x=0;
            if($totPAV >= $settRound){
                echo "350€ premio raggiunto";
            } else {
                $x=350;
                echo $totPAV . "/" . $settRound . " Alto Valore " . "\n" . $totPBV . "/" . $trenRound . " Basso Valore"; 
            }
    
        } else {
            if($totPunteggio >= 12 && $totPunteggio < 17){
                $sett = ($totPunteggio * 70)/100;
                $settRound = ceil($sett);
                $tren = ($totPunteggio * 30)/100;
                $trenRound = floor($tren);
                if($totPAV >= $settRound){
                    echo "520€ premio raggiunto";
                } else {echo "premio da 520 ancora non raggiunto" . $totPAV . "/" . $settRound . " Alto Valore " . "\n" . $totPBV . "/" . $trenRound . " Basso Valore"; }
            } else {
                if($totPunteggio >= 17 && $totPunteggio < 21 ){
                $sett = ($totPunteggio * 70)/100;
                $settRound = ceil($sett);
                $tren = ($totPunteggio * 30)/100;
                $trenRound = floor($tren);
                if($totPAV >= $settRound && $totPBV >= $trenRound){
                    echo "520€ premio raggiunto";
                    } else {echo "premio da 520 ancora non raggiunto sett -> " . $settRound . "tren --> " . $trenRound;}
                } else {
                    echo "ne hai fatti piu di 21? bravo!";
                }
            }
        }
    }
}


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
                <span><?php echo $totContratti ?></span>
                <span>Totale Contratti</span>
            </li>
            <li class="step-wizard-item">
                <span><?php echo $sumCtrBus ?></span>
                <span>Business</span>
            </li>
            <li class="step-wizard-item">
                <span><?php echo $sumCtrDom ?></span>
                <span>Domestici</span>
            </li>
            <li class="step-wizard-item">
                <span><?php conteggio($totPunteggio, $totPAV, $totPBV, $totContratti); ?></span>
                <span><?php 
                function premio($x){
                    if($x > 1){
                        echo "$x euro";
                    } else {}
                } ?></span>
            </li>
            <li class="step-wizard-item" id="li-completato">
                <span>5</span>
                <span>l'ape maya</span>
            </li>
        </ul>
        <!-- <div class="container"> -->
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Alto Valore: <?php echo $totPAV . "/7" ?></p>
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Basso Valore: <?php echo $totPBV . "/2"?></p>


            <!-- <h2 id="h2-titolo">I Tuoi Inseriti: id="h3-titolo"> contratti in totale.</p>
            </h2> -->
        <!-- </div> -->
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
                        color: white;
                        background-color: limegreen;
                    }

                    #td-color2 {
                        color: white;
                        background-color: red;
                    }

                    #td-color3 {
                        color: white;
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