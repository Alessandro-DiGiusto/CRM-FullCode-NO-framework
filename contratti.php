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
    if ($mese == 13) {
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


if ($_POST['selectDal'] == NULL && $_POST['selectAl'] == NULL) {
    $dal = $dalMeseElettrico;
    $al = $alMeseElettrico;
    $selectDal = $use_date_F;
    $selectAl = $use_date_L;
} else {
    $selectDal = $_POST['selectDal']; // 2022-09-07 
    $selectAl = $_POST['selectAl'];
    $dal = str_replace("-", "", $selectDal);   // 20220907 
    $al = str_replace("-", "", $selectAl);
}

$select = "SELECT * FROM contratti
           WHERE contratti.FK_id_users = '$idSessione'
           AND contratti.data_inserimento BETWEEN '$dal' AND '$al'
           ORDER BY id_delContratto desc;";

$result_select = mysqli_query($conn, $select);
$VARconn = $conn;

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

/* -----------------------------------  AGGIORNA PUNTI TOTALE IN CLASSIFICA ------------------ */
$QRY_tot_P = "UPDATE `users` SET `punti_tot` = '$totPunteggio' WHERE `id` = $idSessione";
$UP_tot_P = mysqli_query($conn, $QRY_tot_P);

/* ----------------------------------- AGGIORNA ULTIMO ACCESSO ------------------ */
$ultimo_accesso = new DateTime();
$ultimo_accessos = $ultimo_accesso->format('d-m-Y H:i:s');

$QRY_last_login = "UPDATE `users` SET `last_login` = '$ultimo_accessos' WHERE `id` = '$idSessione'";
$result_last_login = mysqli_query($conn, $QRY_last_login);

/* -----------------------------------  QUERY CALCOLO TOTALE GETTONATO ------------------ */
$sqlCalc = "SELECT SUM(gettone) AS gettoneA
            FROM contratti
            where contratti.stato = '5' 
            and contratti.FK_id_users = '$idSessione' 
            and contratti.data_inserimento BETWEEN '$dal' AND '$al'";
$moneyCalc = mysqli_query($conn, $sqlCalc);
$totGettonato = mysqli_fetch_assoc($moneyCalc);

/* --------------------------- Logica raggiungimendo obiettivi -------------------------- */
function conteggio($totPunteggio, $totPAV, $totPBV)
{
    include 'config.php';
    if ($totPunteggio < 10) {
        $sql = "UPDATE users SET bonus = '0' WHERE id = $_SESSION[userID];";
        $conn = mysqli_connect($server, $user, $pass, $database);
        mysqli_query($conn, $sql);
        mysqli_close($conn);

        $pavMancanti = 7 - $totPAV;
        $pavMancantiMax = (10 - $totPAV) - $totPBV;
        $pdpLast = (3 - $totPBV) * 2;
        if ($pavMancantiMax < 1) {
            $pavMancantiMax = "0";
        }
        if ($pdpLast <= 0) {
            $pdpLast = "0";
        }
        if ($pavMancanti <= 0) {
            $pavMancanti = "0";
        }
        echo '<span style="font-color: blue">Ottieni 250€ di premio<br>raggiungendo gli obiettivi! <i class="fa-solid fa-arrow-right fa-sync fa-flip" style="--fa-flip-x: 1; --fa-flip-y: 0;" ></i></span>';
        echo '<span style="font-weight: bold">' . $pavMancanti . '<span>' . " Ctr Altri Usi<br>e " . $pdpLast . " Domestici<br>oppure<br>solo ";

        echo $pavMancantiMax . " Ctr Altri Usi";
    } else {
        if ($totPunteggio >= 10 && $totPunteggio < 16) {
            if ($totPAV >= 7) {
                /* echo "250€ Premio Raggiunto!<br>"; */
                /* echo "<div class="css-selector-icon_account">" . "<img src="iconFornitures/add-file.png" style="width: 90px;">" . "</div>"; */

                echo "<img src='iconFornitures/trophy250.png" . "' alt='img'>";
                $sql = "UPDATE users SET bonus = '250' WHERE id = $_SESSION[userID];";
                $conn = mysqli_connect($server, $user, $pass, $database);
                mysqli_query($conn, $sql);
                mysqli_close($conn);

                $pavMancanti = 12 - $totPAV;
                $pavMancantiMax = 16 - $totPunteggio;
                $pdpLast = (4 - $totPBV) * 2;
                if ($pdpLast <= 0) {
                    $pdpLast = "0";
                }
                if ($pavMancanti <= 0) {
                    $pavMancanti = "0";
                }
                echo '<div class="container-premio"><span style="font-color: blue">Ottieni 450€ con:<br></span></div>';
                echo '<span style="font-weight: bold">' . $pavMancanti . '<span>' . " Ctr Altri Usi<br>Fai " . $pdpLast . " Domestici<br>oppure<br>solo " . $pavMancantiMax . " Ctr Altri Usi";
            } else {
                $pavMancanti = 7 - $totPAV;
                $pavMancantiMax = (10 - $totPAV);
                $pdpLast = (10 - $totPunteggio) * 2;
                if ($pdpLast <= 0) {
                    $pdpLast = "0";
                }
                if ($pavMancanti <= 0) {
                    $pavMancanti = "0";
                }
                echo '<div class="container-premio"><span style="font-color: blue">Premio da 250€<br>ancora non raggiunto <i class="fa-solid fa-arrow-right fa-sync fa-flip" style="--fa-flip-x: 1; --fa-flip-y: 0;" ></i></span></div>';
                echo '<span style="font-weight: bold">' . "Fai " .  $pavMancanti . " Ctr Altri Usi<br>" . $totPBV . "/3" . " Ctr Domestico";
            }
        } else {
            if ($totPunteggio >= 16 && $totPunteggio < 20) {
                if ($totPAV >= 12) {
                    echo "<img src='iconFornitures/trophy450.png" . "' alt='img'>";
                    $sql = "UPDATE users SET bonus = '450' WHERE id = $_SESSION[userID];";
                    $conn = mysqli_connect($server, $user, $pass, $database);
                    mysqli_query($conn, $sql);
                    mysqli_close($conn);

                    $pavMancanti = 14 - $totPAV;
                    $pavMancantiMax = (20 - $totPAV);
                    $pdpLast = (20 - $totPunteggio) * 2;
                    if ($pdpLast <= 0) {
                        $pdpLast = "0";
                    }
                    if ($pavMancanti <= 0) {
                        $pavMancanti = "0";
                    }
                    echo '<div class="container-premio"><span style="font-color: blue">Ottieni 650€ con:<br></span></div>';
                    echo '<span style="font-weight: bold">' . $pavMancanti . '<span>' . " Ctr Altri Usi<br>Fai " . $pdpLast . " Domestici<br>oppure<br>solo " . $pavMancantiMax . " Ctr Altri Usi";
                } else {
                    $pavMancanti = 12 - $totPAV;
                    $pavMancantiMax = (16 - $totPAV);
                    $pdpLast = (20 - $totPunteggio) * 2;
                    if ($pdpLast <= 0) {
                        $pdpLast = "0";
                    }
                    if ($pavMancanti <= 0) {
                        $pavMancanti = "0";
                    }
                    echo "Premio da 450€ ancora non raggiunto<br>" . $pavMancanti . " Ctr Altri Usi<br>" . $totPBV . "/4" . " Ctr Domestico<br>oppure<br>solo " . $pavMancantiMax . " Ctr Altri Usi";
                }
            } else {
                if ($totPunteggio >= 20) {
                    if ($totPAV >= 14) {
                        echo "<img src='iconFornitures/trophy650.png" . "' alt='img'>";
                        $sql = "UPDATE users SET bonus = '650' WHERE id = $_SESSION[userID];";
                        $conn = mysqli_connect($server, $user, $pass, $database);
                        mysqli_query($conn, $sql);
                        mysqli_close($conn);
                    } else {
                        echo "Premio da 650€ ancora non raggiunto" . $totPAV . "/14" . " Alto Valore<br>" . $totPBV . "/6" . " Basso Valore";
                    }
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
    <link rel="stylesheet" type="text/css" href="styleOK.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/3890e69984.js" crossorigin="anonymous"></script>
    <title>My Office</title>
</head>

<body>
    <div class="container-header">
        <div class="header-welcome">
            <!-- <img src="img/man-technologist-medium-light-skin-tone.png" class="logo"> -->
            <div class="titolo">
                <?php
                $nameLogin = $_SESSION['username'];
                echo "<h1 style='font-family: Poppins; font-weight: bold'>" . $nameLogin . "</h1>";
                ?>
            </div>


            <div>
                <a href="tipo.php" style="text-decoration: none;">
                    <button class="css-selector-icon_account" title="Inserisci Nuovo Contratto"><img src='iconFornitures/add-file.png' style='width: 90px;'></button>
                </a>
            </div>
            <div>
                <a href="" target="_blank" style="text-decoration: none;">
                    <button class="css-selector-icon_account" title="Calcolo Penali - Energia Reattiva || It is a DEMO - NOT AVAIABLE"><img src='iconFornitures/contatore2.png' style='width: 90px'></button>
                </a>
            </div>
            <div>
                <a href="account.php" style="text-decoration: none;">
                    <button class="css-selector-icon_account" title="Impostazioni account"><img src='iconFornitures/account.png' style='width: 90px'></button>
                </a>
            </div>
            <div>
                <a href="logout.php" style="text-decoration: none;">
                    <button class="css-selector_Logout" title="Chiudi la Sessione"><img src='iconFornitures/logout-icon.svg' style='width: 25px; height: 25px'></button>
                </a>
            </div>
        </div>
    </div>

    <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span><?php echo $sumCtrBus ?></span>
                <span>Ctr Altri Usi</span>
            </li>
            <li class="step-wizard-item">
                <span><?php echo $sumCtrDom ?></span>
                <span>Ctr Domestici</span>
            </li>
            <li class="step-wizard-item current-item">
                <span><?php echo $totContratti ?></span>
                <span>Totale Contratti </span>
            </li>
        </ul>
    </section>


    <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class="step-wizard-item current-item">
                <span><?php if ($totPAV != 0) {
                        ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                        }
                                                                        echo $totPAV; ?></span>
                        <span>Punti Altri Usi</span>
            </li>
            <li class="step-wizard-item">
                <span><?php if ($totPBV != 0) {
                        ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                        }
                                                                        echo $totPBV; ?></span>
                        <span>Punti Domestico</span>
            </li>
            <li class="step-wizard-item">
                <span><?php if ($totPunteggio != 0) {
                        ?> <span style='font-weight: bold; font-size: 20px'><?php
                                                                        }
                                                                        echo $totPunteggio; ?></span>
                        <span>Punti totale</span>
            </li>
        </ul>
    </section>
    <section class="step-wizard">
        <ul class="step-wizard-list">
            <li class=step-wizard-item>
                <span>
                    <span>
                        <div>
                            <div class="imgFooter__gettone" id="imgFooter">
                                <img src="iconFornitures/apple-wallet.png" alt="" id="imgFooter">
                                <ul>
                                    <li class="step-wizard-item">
                                        <span style='font-weight: bold; font-size: 20px; color: green'>
                                            <?php
                                            $provavar = $totGettonato['gettonaA'];

                                            if ($totGettonato['gettoneA'] == 0 || $totGettonato['gettoneA'] == NULL) {
                                                /* var_dump($totGettonato); */
                                                echo '<i class="fa-solid fa-business-time fa-shake" style="--fa-animation-duration: 3s"></i>';
                                                echo '<span style="font-size: 10px"><br>In attesa di approvati</span>';
                                            } else {
                                                echo '<i class="fa-solid fa-sync fa-spin" style="--fa-animation-duration: 7s; font-size: 20px; color: green"></i>';
                                                $number = $totGettonato['gettoneA'];
                                                // Notazione Italiana
                                                $num_it_format = number_format($number, 2, ',', '.');
                                                // es. 1.234,56 €

                                                echo "  " . $num_it_format . "€";

                                                /* echo "  " . $totGettonato['gettoneA'] . " €"; */
                                            }
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </span>
                </span>
            </li>
            <li class="step-wizard-item">
                <span>
                    <div class="container-premio"><?php conteggio($totPunteggio, $totPAV, $totPBV, $totContratti); ?></div>
                </span>
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
                    <th id="td-colorSmart">ID Ctr</th>
                    <th>Ragione Sociale</th>
                    <th>Iban</th>
                    <th>Email</th>
                    <th>Cell</th>
                    <th>Contratto</th>
                    <th>Effic.</th>
                    <th>Stipula</th>
                    <th>Data Inserimento</th>
                    <th>Listino</th>
                    <th>Consumo Annuo</th>
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
                            <td id="td-colorSmart"><?php echo $row['id_delContratto'] ?></td>
                            <td><?php echo $row['r_sociale'] ?></td>
                            <td><?php echo $row['iban'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['tel'] ?></td>
                            <td>
                                <center>
                                    <div>
                                        <?php
                                        if ($row['luce'] == "Domestico") {
                                            echo "<img src='iconFornitures/luce_domestico.svg' style='width: 50px; height: 50px' title='Contratto Domestico LUCE'>";
                                        } elseif ($row['luce'] == "Altri Usi") {
                                            echo "<img src='iconFornitures/luce_business.svg' style='width: 50px; height: 50px' title='Contratto LUCE di tipo Business/Altri Usi'>";
                                        } elseif ($row['luce'] == "Privato A") {
                                            echo "<img src='iconFornitures/privato-altriusi.svg' style='width: 50px; height: 50px' title='Contratto PRIVATO Altri Usi es: contatore cancello automatico, contatore scala, contatore agricolo...'>";
                                        }
                                        ?>


                                        <center>
                                            <div><?php
                                                    if ($row['gas'] == "Domestico") {
                                                        echo "<img src='iconFornitures/gas_domestico.svg' style='width: 50px; height: 50px' title='Contratto Domestico GAS'>";
                                                    } elseif ($row['gas'] == "Altri Usi") {
                                                        echo "<img src='iconFornitures/gas_business.svg' style='width: 50px; height: 50px' title='Contratto GAS di tipo Business/Altri Usi'>";
                                                    }
                                                    ?>


                                                <center>
                                                    <div><?php
                                                            if ($row['luce_gas'] == "Domestico") {
                                                                echo "<img src='iconFornitures/luceGas_Dom.svg' style='width: 50px; height: 50px' title='Contratto Luce&Gas Domestico'>";
                                                            } elseif ($row['luce_gas'] == "Altri Usi") {
                                                                echo "<img src='iconFornitures/luceGas_Business.svg' style='width: 50px; height: 50px' title='Contratto Luce&Gas Business/Altri Usi'>";
                                                            }
                                                            ?>
                            </td>
                            <td>
                                <center>
                                    <?php
                                    if ($row['effic'] == "0") {
                                        echo "NO";
                                    } else {
                                        echo "SI";
                                    }
                                    ?><center>
                            </td>
                            <td><?php echo $row['stipula'] ?></td>
                            <td><?php echo $row['insert_date'] ?></td>
                            <td>
                                <?php
                                if ($row['consAnnuoGas'] > 0) {
                                    if ($row['index_or_pun'] == "0") {
                                        echo "Index" . "<br>";
                                    } elseif ($row['index_or_pun'] == "1") {
                                        echo "PUN" . "<br>";
                                    }
                                    if ($row['index_or_pun_gas'] == "0") {
                                        echo "Index";
                                    } elseif ($row['index_or_pun_gas'] == "1") {
                                        echo "PUN";
                                    }
                                } else {
                                    if ($row['index_or_pun'] == "0") {
                                        echo "Index";
                                    } elseif ($row['index_or_pun'] == "1") {
                                        echo "PUN";
                                    }
                                }
                                ?></td>
                            <td>
                                <?php
                                if ($row['consAnnuo'] > 0 && $row['consAnnuoGas'] > 0) {
                                    echo $row['consAnnuo'] . " Luce" . "<br>" . $row['consAnnuoGas'] . " Gas";
                                } elseif ($row['consAnnuo'] > 0) {
                                    echo $row['consAnnuo'] . " kWh";
                                } else {
                                    echo $row['consAnnuoGas'] . " kWh";
                                }
                                ?></td>
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
            }
            if (mysqli_num_rows($result_select) == 0) {
                echo "<center><a style='color: rgba(0,0,255,1);'><i>" . "Nessun contratto ancora inserito" . "</i></a></center>";
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

                    #td-color_approved {
                        color: #FFF;
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
        <div id="second__container-winner">
            <?php                                               // CLONE
            $winner = "SELECT * FROM users
                       /* WHERE bonus != '0'  */
                       ORDER BY bonus DESC;";


            /* $winner = "SELECT SUM(business) + SUM(domestico)  
                      FROM contratti
                      WHERE FK_id_users = 1"; */

            $winnerList = mysqli_query($conn, $winner);

            $winnerLast = "SELECT * FROM bonusbackup
           /* WHERE bonus != '0' */
           ORDER BY bonus DESC;";

            $winnerListLast = mysqli_query($conn, $winnerLast);

            // per stampare eventuali errori
            if (!$winnerList) {
                echo "Errore query della select" . mysqli_error($conn);
            }
            ?>

            <div class="winnerList_container_P">
                <img src="iconFornitures/coppa.png" alt="coppa">
                <p style="padding-left: 1rem">Bonus Vinti <span style='font-weight: bold'>questo</span> mese elettrico</p>
            </div>
            <table class="w3-table-all w3-center" border="1">
                <tr style='background-color: #A49BFE; color: white'>
                    <th>Agente</th>
                    <th>Bonus Vinto</th>
                    <th>Punti</th>
                </tr>
                <?php
                $i = 1;
                if (mysqli_num_rows($winnerList) > 0) {
                    while ($row = mysqli_fetch_assoc($winnerList)) { ?>
                        <tr>
                            <?php
                            if ($row['bonus'] != 0) {
                                echo "<td>" . $row['username'] . "</td>";
                                echo "<td>" . $row['bonus'] . "</td>";
                                echo "<td>" . $row['punti_tot'] . "</td>";
                            }
                            ?>
                        </tr>

                <?php }
                } ?>
            </table>
        </div>

        <form action="" method="POST" class="login-email" id="formInserimento">
            <div class="container_statoUP-header">
                <p class="login-text">
                    <center style="font-size: 2rem; font-weight: 800;">Ricerca</center>
                </p>
                <div class="input-group">
                    <center>Dal
                        <input type="date" name="selectDal" value="<?php echo $_POST['selectDal']; ?>">
                    </center>
                </div>
                <div class="input-group">
                    <center>Al
                        <input type="date" name="selectAl" value="<?php echo $_POST['selectAl']; ?>">
                    </center>
                </div>
                <div class="input-group">
                    <center>
                        <input type="submit" value="Cerca" class="btn">
                    </center>
                </div>

                <div class="input-group">
                    <a href="contratti_admin.php" style="text-decoration: none;">
                        <input type="button" value="Mese Elettrico" class="btn" style="background-color: #58A332; align-content: space-around; flex-wrap: wrap; display: inline;">
                    </a>
                </div>

                <!-- <div class="input-group">
                        <center><a href="contratti_admin.php" style="text-decoration: none;">
                                <input type="button" value="Mese Elettrico" class="btn" style="background-color: #58A332; width: 50%;"></a>
                    </div> -->
            </div>
        </form>
    </div>

    <input type="hidden" name="id" value="<?php echo "La sessione equivale a: " . $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->


    <!-- Copyright -->
    <div class="footer">
        <center>
            <!--
            <img src="https://crm.alessandrodigiusto.it/assets/img/man-technologist-medium-light-skin-tone.png">
            -->
            <p><a href="https://alessandrodigiusto.it" target="_blank">
                    &lt;/Dev&gt;
                    with L</a>♥<a href="https://alessandrodigiusto.it" target="_blank">ve: <br>Alessandro Di Giusto</a></p>
        </center>
        <center>
            <p class="copyright"><a href="https://alessandrodigiusto.it/" target="_BLANK"> &copy; <script>
                        document.write(new Date().getFullYear())
                    </script> Copyright All Rights Reserved<br>alessandrodigiusto.it </a></p>
        </center>
    </div>
    <!-- Copyright -->


    </div>
    <!-- End of .container -->
</body>

</html>