<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* ---------------------------------------------------------------------- */
$idSessione = $_SESSION['userID'];
$meseCorrente = new DateTime();
$oraCorrente = $meseCorrente->format('H:i:s');
$dataOra = $meseCorrente->format('d-m-Y');

$giorno = $meseCorrente->format('d'); 
$mese = $meseCorrente->format('m');
$anno = $meseCorrente->format('Y');

/* -----------------------------DAL-------------------------------------- */

if ($giorno >= 16) {
    $use_date_F = 16 . "/$mese/$anno";
    $nextMese = $mese + 1;
    if($mese == 13){
        $mese = "01";
    }
    if (strlen($nextMese) == 1) {   // se il mese è tra 1 al 9, aggiunge uno 0 d'avanti.
        $length = 2;
        $nextMese = str_pad($nextMese, $length, "0", STR_PAD_LEFT);
    }
    $use_date_L = 15 . "/$nextMese/$anno";
    $dalMeseElettrico = $anno . $mese . 16;
    $alMeseElettrico = $anno . $nextMese . 15;
} else {
    $mese_mod = $mese - 1; // es. 8
    if (strlen($mese_mod) == 1) {   // se il mese è tra 1 al 9, aggiunge uno 0 d'avanti.
        $length = 2;
        $mese_mod = str_pad($mese_mod, $length, "0", STR_PAD_LEFT);
    }

    $dalMeseElettrico = $anno . $mese_mod . 16;
    $use_date_F = 16 . "/$mese_mod/$anno";  // Date fixed to show on page

    /* -----------------------------AL--------------------------------------- */

    $alMeseElettrico = $anno . $mese . 15;
    $use_date_L = 15 . "/$mese/$anno";  // Date fixed to show on page

}


/* ---------------------------------------------------------------------- */

if ($_POST['selectDal'] == NULL && $_POST['selectAl'] == NULL) {
    $dal = $dalMeseElettrico;
    $al = $alMeseElettrico;
} else {
    $selectDal = $_POST['selectDal']; // 2022-09-07 
    $selectAl = $_POST['selectAl'];
    $dal = str_replace("-", "", $selectDal);   // 20220907 
    $al = str_replace("-", "", $selectAl);
}

$select = "SELECT * FROM contratti
           WHERE contratti.FK_id_users = '$idSessione'
           AND contratti.data_inserimento BETWEEN '$dal' AND '$al'
           ORDER BY data_inserimento DESC;";

$result_select = mysqli_query($conn, $select);

// per stampare eventuali errori
if (!$result_select) {
    echo "Errore query della select" . mysqli_error($conn);
}

/* ----------------------------------- QUERY CONTEGGIO CONTRATTI BUSINESS IN TOTALE ------------- */
$querySum = "SELECT SUM(business) AS business FROM contratti where domestico = '0' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrBus = $row['business'];
if ($sumCtrBus == NULL) {
    $sumCtrBus = "0";
}

/* ----------------------------------- QUERY CONTEGGIO CONTRATTI DOMESTICI IN TOTALE ------------- */
$querySum = "SELECT SUM(domestico) AS domestico FROM contratti where business = '0' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione'";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrDom = $row['domestico'];
if ($sumCtrDom == NULL) {
    $sumCtrDom = "0";
}

/* --------------------------- QUERY CONTEGGIO CONTRATTI basso valore 0,5 Punti ------------- */
$querySum = "SELECT SUM(valore) AS valoreB FROM contratti where business = '0' AND stato = '5' AND contratti.data_inserimento BETWEEN '$dalMeseElettrico' AND '$alMeseElettrico' AND contratti.FK_id_users = '$idSessione';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPBV = $row['valoreB'];
if ($totPBV == NULL) {
    $totPBV = "0";
}

/* --------------------------- QUERY CONTEGGIO CONTRATTI ALTO valore 1 Punto ------------- */
$querySum = "SELECT SUM(valore) AS valoreA FROM contratti where domestico = '0' AND stato = '5' AND contratti.data_inserimento BETWEEN '$dalMeseElettrico' AND '$alMeseElettrico' AND contratti.FK_id_users = '$idSessione';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPAV = $row['valoreA'];
if ($totPAV == NULL) {
    $totPAV = "0";
}

/* --------------------------- N contratti Inseriti ------------- */ 
$insertSum = "SELECT * FROM contratti where stato = '1' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$resultInsert = mysqli_query($conn, $insertSum);
$totInsert = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Lavorazione ------------- */
$insertSum = "SELECT * FROM contratti where stato = '2' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$resultInsert = mysqli_query($conn, $insertSum);
$totLavor = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Sospeso ------------- */
$insertSum = "SELECT * FROM contratti where stato = '3' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$resultInsert = mysqli_query($conn, $insertSum);
$totSospe = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti KO ------------- */
$insertSum = "SELECT * FROM contratti where stato = '4' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$resultInsert = mysqli_query($conn, $insertSum);
$totKO = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Approvato ------------- */
$insertSum = "SELECT * FROM contratti where stato = '5' AND contratti.data_inserimento BETWEEN '$dal' AND '$al' AND contratti.FK_id_users = '$idSessione';";
$resultInsert = mysqli_query($conn, $insertSum);
$totAppro = mysqli_num_rows($resultInsert);

/* -----------------------------------  QUERY CONTEGGIO CONTRATTI IN TOTALE ------------- */
$totContratti = $sumCtrBus + $sumCtrDom;
$totPunteggio = $totPAV + $totPBV;

/* --------------------------- Logica raggiungimendo obiettivi ------------- */
function conteggio($totPunteggio, $totPAV, $totPBV, $totContratti)
{
    if ($totPunteggio < 10) {
        $pavMancanti = 7 - $totPAV;
        $pbvMancanti = (3 - $totPBV) * 2;
        if ($pbvMancanti <= 0) {
            $pbvMancanti = "0";
        }
        echo '<span style="font-color: blue"> Per il BONUS da 250€<br> fai altri:</span>' . '<span style="font-weight: bold">'.$pavMancanti.'<span>' . " Ctr Altri Usi<br>" . $pbvMancanti . " Ctr Domestici";
    } else {
        if ($totPunteggio >= 10 && $totPunteggio < 16) {
            $x = 0;
            if ($totPAV >= 7 && $totPBV >= 3) {
                echo "250€ Premio Raggiunto!<br>";
                $pavMancanti = 12 - $totPAV;
                $pbvMancanti = (4 - $totPBV) * 2;
                if ($pbvMancanti <= 0) {
                    $pbvMancanti = "0";
                }
                echo '<span style="font-size: 13px; color: blue"> Per il BONUS da 450€ fai altri:<br>' . $pavMancanti . " Ctr Altri Usi<br>" . $pbvMancanti . " Ctr Domestici" . '</span>';
            } else {
                echo "Premio da 250€ ancora non raggiunto" . $totPAV . "/" . 7 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
            }
        } else {
            if ($totPunteggio >= 16 && $totPunteggio < 20) {
                if ($totPAV >= 12 && $totPBV >= 4) {
                    echo "450€ Premio Raggiunto!<br>";
                    $pavMancanti = 14 - $totPAV;
                    $pbvMancanti = (6 - $totPBV) * 2;
                    if ($pbvMancanti <= 0) {
                        $pbvMancanti = "0";
                    }
                    echo "Per il BONUS da 650€<br> fai altri:" . $pavMancanti . " Ctr Altri Usi<br>" . $pbvMancanti . " Ctr Domestici";
                } else {
                    echo "premio da 450€<br>ancora non raggiunto<br>" . $totPAV . "/" . 12 . " Alto Valore " . "\n" . $totPBV . "/" . 4 . " Basso Valore";
                }
            } else {
                if ($totPunteggio >= 20) {
                    if ($totPAV >= 14 && $totPBV >= 6) {
                        echo "650€ Premio Raggiunto!";
                    } else {
                        echo "premio da 650€<br>ancora non raggiunto<br>" . $totPAV . "/" . 14 . " Alto Valore " . "\n" . $totPBV . "/" . 6 . " Basso Valore";
                    }
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
                echo "<h1 style='font-family: Poppins; font-weight: bold'>" . $asd . "</h1>";
                ?>
            </div>


            <div>
                <a href="FIX-tipo.php" style="text-decoration: none;">
                    <button class="css-selector-icon"><img src='iconFornitures/upload-icon2.svg' style='width: 50px; height: 50px'></button>
                </a>
            </div>
            <div>
                <a href="https://docs.google.com/spreadsheets/d/14Pjb45YLyWX_dxqyHvoiqH9qwmHfPUSi/edit#gid=1386498951" target="_blank" style="text-decoration: none;">
                    <button class="css-selector-icon"><img src='iconFornitures/contatore.png' style='width: 50px; height: 50px'></button>
                </a>
            </div>
            <div>
                <a href="logout.php" style="text-decoration: none;">
                    <button class="css-selector_Logout"><img src='iconFornitures/logout-icon.svg' style='width: 25px; height: 25px'></button>
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
                <span><div class="container-premio"><?php conteggio($totPunteggio, $totPAV, $totPBV, $totContratti); ?></div></span>
            </li>
        </ul>
    </section>

    <center>
        <div id="p-month">
            <p>Mese elettrico corrente: <br>Dal <?php echo "$use_date_F"; ?> AL <?php echo "$use_date_L"; ?></p>
            <p>Stai visualizzando i contratti: <br>Dal <?php echo "$selectDal"; ?> AL <?php echo "$selectAl"; ?></p>
        </div>
        <table class="content-table" id="gradient-table">
            <thead>
                <tr>
                    <th>ID Ctr</th>
                    <th>Ragione Sociale</th>
                    <th>Iban</th>
                    <th>Email</th>
                    <th>Cell</th>
                    <th>Luce</th>
                    <th>Gas</th>
                    <th>Luce & Gas</th>
                    <th>Effic.</th>
                    <th>Stipula</th>
                    <th>Data Inserimento</th>
                    <th>Con Ann.</th>
                    <th>Gettone</th>
                    <th>Stato</th>
                </tr>
            </thead>

            <?php
            $i = 1;
            if (mysqli_num_rows($result_select) > 0) {
                while ($row = mysqli_fetch_assoc($result_select)) { ?>
                    <tbody>
                        <tr class="active-row">
                            <td><?php echo $row['id_delContratto'] ?></td>
                            <td><?php echo $row['r_sociale'] ?></td>
                            <td><?php echo $row['iban'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['tel'] ?></td>
                            <td>
                                <center>
                                    <div>
                                        <?php
                                        if ($row['luce'] == "Domestico") {
                                            echo "<img src='iconFornitures/luce_domestico.svg' style='width: 50px; height: 50px' >";
                                        } elseif ($row['luce'] == "Altri Usi") {
                                            echo "<img src='iconFornitures/luce_business.svg' style='width: 50px; height: 50px' >";
                                        }
                                        ?>
                            </td>
                            <td>
                                <center>
                                    <div><?php
                                            if ($row['gas'] == "Domestico") {
                                                echo "<img src='iconFornitures/gas_domestico.svg' style='width: 50px; height: 50px' >";
                                            } elseif ($row['gas'] == "Altri Usi") {
                                                echo "<img src='iconFornitures/gas_business.svg' style='width: 50px; height: 50px' >";
                                            }
                                            ?>
                            </td>
                            <td>
                                <center>
                                    <div><?php
                                            if ($row['luce_gas'] == "Domestico") {
                                                echo "<img src='iconFornitures/luceGas_Dom.svg' style='width: 50px; height: 50px' >";
                                            } elseif ($row['luce_gas'] == "Altri Usi") {
                                                echo "<img src='iconFornitures/luceGas_Business.svg' style='width: 50px; height: 50px' >";
                                            }
                                            ?>
                            </td>
                            <td>
                                <?php
                                if($row['effic'] == "0"){
                                    echo "NO";
                                } else {
                                    echo "SI";
                                }
                                ?>
                            </td>
                            <td><?php echo $row['stipula'] ?></td>
                            <td><?php echo $row['insert_date'] ?></td>
                            <td><?php echo $row['consAnnuo'] . " Kwh" ?></td>
                            <td><?php echo $row['gettone'] . "€" ?></td>
                            <td><?php echo "<center>";
                                if ($row['stato'] == "1") {
                                    echo "Inserito";
                                } else {
                                    if ($row['stato'] == "2") {
                                        echo "Lavorazione";
                                    } else {
                                        if ($row['stato'] == "3") {
                                            echo  "Sospeso";
                                        } else {
                                            if ($row['stato'] == "4") {
                                                echo "KO";
                                            } else {
                                                if ($row['stato'] == "5") {
                                                    echo "<div id=td-color_approved>" . "✔" . "</div>";
                                                }
                                            }
                                        }
                                    }
                                } ?>
                            </td>
                        </tr>
                <?php }
            } ?>

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

                    #td-color_approved {
                        color: #009a58;
                        /* background-color: white; */
                        font-size: 25px;
                        font-style: bold;
                        font-weight: bolder;
                        animation: mymove 0.5s infinite;
                    }

                    @keyframes mymove {
                        50% {
                            font-style: italic;
                        }
                    }
                </style>
                    </tbody>
        </table>
    </center>

    <section class="step-wizard">
            <ul class="step-wizard-list">
                <li class="step-wizard-item current-item">
                    <span><?php if ($totInsert != 0) {
                            ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                            }
                                                                            echo $totInsert ?></span>
                            <span>Inseriti</span>
                </li>
                <li class="step-wizard-item">
                    <span><?php if ($totLavor != 0) {
                            ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                            }
                                                                            echo $totLavor ?></span>
                            <span>Lavorazione</span>
                </li>
                <li class="step-wizard-item">
                    <span><?php if ($totSospe != 0) {
                            ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                            }
                                                                            echo $totSospe ?></span>
                            <span>Sospesi</span>
                </li>
                <li class="step-wizard-item">
                    <span><?php if ($totKO != 0) {
                            ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                            }
                                                                            echo $totKO ?></span>
                            <span>KO</span>
                </li>
                <li class="step-wizard-item">
                    <span><?php if ($totAppro != 0) {
                            ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                            }
                                                                            echo $totAppro ?></span>
                            <span>Approvati</span>
                </li>
            </ul>
        </section>

        <div class="container">
            <div class="container_statoUP-header">
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Punti Business: <?php echo $totPAV ?></p>
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Punti Domestico: <?php echo $totPBV ?></p>
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
            </div>
            <form action="" method="POST" class="login-email" id="formInserimento">
                <div class="container_statoUP-header">
                    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Ordina view Contratti</p>
                    <div class="input-group">
                        <center>Dal
                            <input type="date" name="selectDal" value="<?php echo $_POST['selectDal']; ?>">
                    </div>
                    <div class="input-group">
                        <center>Al
                            <input type="date" name="selectAl" value="<?php echo $_POST['selectAl']; ?>">
                    </div>
                    <div class="input-group">
                        <center>
                            <input type="submit" value="Cerca" class="btn">
                    </div>
                    <div class="input-group">
                        <center><a href="contratti_admin.php" style="text-decoration: none;">
                                <input type="button" value="Mese Elettrico" class="btn" style="background-color: #58A332; width: 50%;"></a>
                    </div>
                </div>
            </form>
        </div>
        <input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
    
</body>

</html>