<?php

include 'config.php';
error_reporting(0);
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

$idSessione = $_SESSION['userID'];

if ($idSessione != 2) {
    header("Location: contratti.php");
}

if (isset($_POST['submit'])) {
    $idCtr = $_POST['id_delContratto'];
    $stato = $_POST['lista-stati'];
    $sql = "UPDATE contratti set stato = '$stato' where id_delContratto = '$idCtr'";
    $result = mysqli_query($conn, $sql);
    if ($stato == "1") {
        $stato = "Inserito";
    } elseif ($stato == "2") {
        $stato = "Lavorazione";
    } elseif ($stato == "3") {
        $stato = "Sospeso";
    } elseif ($stato == "4") {
        $stato = "KO";
    } elseif ($stato == "5") {
        $stato = "APPROVATO";
    }
    if ($result) {
        echo "<script>alert('Il contratto $idCtr è stato aggiornato correttamente in: $stato')</script>";
        $_POST['id_delContratto'] = NULL;
        $idCtr = NULL;
        $_POST['lista-stati'] = NULL;
        $stato = NULL;
        $result = NULL;
    } else {
        echo "<script>alert('ERRORE: Il contratto non è stato aggiornato correttamente ')</script>";
    }
}

/* ---------------------------------------------------------------------- */
$idSessione = $_SESSION['userID'];
$meseCorrente = new DateTime();
$oraCorrente = $meseCorrente->format('H:i:s');
$dataOra = $meseCorrente->format('d-m-Y');
$giorno = $meseCorrente->format('d');
$mese = $meseCorrente->format('m');
$anno = $meseCorrente->format('Y');

$bkQry = "SELECT statoBK from statobk";
$resultBK = mysqli_query($conn, $bkQry);

if ($giorno == 15) {
    $bkFatto = mysqli_query($conn, "UPDATE `statobk` SET `statoBK`='0' WHERE 1;");
} else {
    if ($giorno == 16) {
        if ($row = mysqli_fetch_assoc($resultBK)) {
            if ($row['statoBK'] == 0) {
                $update = "UPDATE bonusbackup, users
                SET bonusbackup.bonus = users.bonus
                WHERE bonusbackup.id = users.id;";
                $queryUP = mysqli_query($conn, $update);
                $update2 = "UPDATE `users` SET `bonus`='0' WHERE 1;";
                $queryUP2 = mysqli_query($conn, $update2);
                $bkFatto = mysqli_query($conn, "UPDATE `statobk` SET `statoBK`='1' WHERE 1;");
            } else {
                // non fare niente
            }
        }
    } else {
        // non fare niente
    }
}

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
            INNER JOIN users
            ON contratti.FK_id_users = users.id
            AND contratti.data_inserimento BETWEEN '$dal' AND '$al'
            ORDER BY username ASC;";
$result_select = mysqli_query($conn, $select);
// per stampare eventuali errori
if (!$result_select) {
    echo "Errore query della select" . mysqli_error($conn);
}

/* ---------------------------------------------------------------------------- */
/* https://www.youtube.com/watch?v=zc1F50TeyIY */
/* ---------------------------------------------------------------------------- */

/* ----------------------------------- QUERY CONTEGGIO CONTRATTI BUSINESS IN TOTALE ------------- */
$querySum = "SELECT SUM(business) AS business FROM contratti where domestico = '0' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrBus = $row['business'];


/* ----------------------------------- QUERY CONTEGGIO CONTRATTI DOMESTICI IN TOTALE ------------- */
$querySum = "SELECT SUM(domestico) AS domestico FROM contratti where business = '0' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrDom = $row['domestico'];

/* --------------------------- QUERY CONTEGGIO CONTRATTI basso valore 0,5 Punti ------------- */
$querySum = "SELECT SUM(valore) AS valoreB FROM contratti where business = '0' AND stato = '5' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPBV = $row['valoreB'];
/* --------------------------- QUERY CONTEGGIO CONTRATTI ALTO valore 1 Punto ------------- */
$querySum = "SELECT SUM(valore) AS valoreA FROM contratti where domestico = '0' AND stato = '5' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPAV = $row['valoreA'];

/* --------------------------- N contratti Inseriti ------------- */
$insertSum = "SELECT * FROM contratti where stato = '1' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$resultInsert = mysqli_query($conn, $insertSum);
$totInsert = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Lavorazione ------------- */
$insertSum = "SELECT * FROM contratti where stato = '2' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$resultInsert = mysqli_query($conn, $insertSum);
$totLavor = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Sospeso ------------- */
$insertSum = "SELECT * FROM contratti where stato = '3' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$resultInsert = mysqli_query($conn, $insertSum);
$totSospe = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti KO ------------- */
$insertSum = "SELECT * FROM contratti where stato = '4' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$resultInsert = mysqli_query($conn, $insertSum);
$totKO = mysqli_num_rows($resultInsert);

/* --------------------------- N contratti Approvato ------------- */
$insertSum = "SELECT * FROM contratti where stato = '5' AND contratti.data_inserimento BETWEEN '$dal' AND '$al';";
$resultInsert = mysqli_query($conn, $insertSum);
$totAppro = mysqli_num_rows($resultInsert);

/* -----------------------------------  QUERY CONTEGGIO CONTRATTI IN TOTALE ------------- */
$totContratti = $sumCtrBus + $sumCtrDom;
$totPunteggio = $totPAV + $totPBV;

/* --------------------------- Logica raggiungimendo obiettivi ------------- */
function conteggio($totPunteggio, $totPAV, $totPBV, $totContratti)
{
    if ($totPunteggio < 9) {
        $sett = ($totPunteggio * 70) / 100;
        $settRound = ceil($sett);
        $tren = ($totPunteggio * 30) / 100;
        $trenRound = ceil($tren);
        /*         echo "$totPAV . "/" . $settRound .  Alto Valore  . \n . $totPBV . / . $trenRound .  Basso Valore"; */
        echo "OTTIENI 1.350€ DI PREMIO AZIENDALE ERMES CON: <br>$totPAV /7 Alto Valore <br> $totPBV /2 Basso Valore";
    } else {
        if ($totPunteggio >= 9 && $totPunteggio < 12) {
            if ($totPAV >= 7 && $totPBV >= 2) {
                echo "350€ premio raggiunto";
                echo "<br>";
                echo "Premio da 520€: <br>" . $totPAV . "/9 Alto Valore " . "<br>" . $totPBV . "/" . 3 . " Basso Valore";
            } else {
                echo "Premio da 350€: <br>" . $totPAV . "/7 Alto Valore " . "<br>" . $totPBV . "/" . 2 . " Basso Valore";
            }
        } else {
            if ($totPunteggio >= 12 && $totPunteggio < 17) {
                if ($totPAV >= 9 && $totPBV >= 3) {
                    echo "520€ premio raggiunto";
                } else {
                    if ($totPAV >= 7 && $totPBV >= 2) {
                        echo "Premio da 350€ già vinto, ma puoi andare oltre!";
                        echo "<br>";
                        echo "Premio da 520€ manca ancora: " . $totPAV . "/" . 9 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
                    } else {
                        echo "Premio da 350€ manca ancora:<br>" . "Altri Usi " . $totPAV . "/" . 7 . "<br>" . "Domestici " . $totPBV . "/" . 3;
                    }
                }
            } else {
                if ($totPunteggio >= 17 && $totPunteggio < 21) {          /* 3' fascia */
                    if ($totPAV >= 12 && $totPBV >= 5) {
                        echo "900€ premio raggiunto";
                    } else {
                        if ($totPAV >= 9 && $totPBV >= 3) {
                            echo "520€ premio raggiunto";
                            echo "Premio da 900 manca ancora: " . $totPAV . "/" . 12 . " Alto Valore " . "\n" . $totPBV . "/" . 5 . " Basso Valore";
                        } else {
                            if ($totPAV >= 7 && $totPBV >= 2) {
                                echo "Premio da 350€ già vinto";
                                echo "<br>";
                                echo "Premio da 520€ manca ancora: " . $totPAV . "/" . 9 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
                            } else {
                                echo "Premio da 350€ manca ancora:<br>" . "Altri Usi " . $totPAV . "/" . 7 . "<br>" . "Domestici " . $totPBV . "/" . 2;
                            }
                        }
                    }
                } else {
                    if ($totPunteggio >= 21 && $totPunteggio < 25) {          /* 4' fascia */
                        if ($totPAV >= 15 && $totPBV >= 6) {
                            echo "1.200,00€ premio raggiunto";
                        } else {
                            if ($totPAV >= 12 && $totPBV >= 5) {
                                echo "900€ premio raggiunto";
                                echo "Aggiudicati il premio da 1.200,00€! manca soltanto: " . $totPAV . "/" . 15 . " Alto Valore " . "\n" . $totPBV . "/" . 6 . " Basso Valore";
                            } else {
                                if ($totPAV >= 9 && $totPBV >= 3) {
                                    echo "520€ premio raggiunto";
                                    echo "Premio da 900 manca ancora: " . $totPAV . "/" . 12 . " Alto Valore " . "\n" . $totPBV . "/" . 5 . " Basso Valore";
                                } else {
                                    if ($totPAV >= 7 && $totPBV >= 2) {
                                        echo "Premio da 350€ già vinto";
                                        echo "<br>";
                                        echo "Premio da 520€ manca ancora: " . $totPAV . "/" . 9 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
                                    } else {
                                        echo "Premio da 350€ manca ancora:<br>" . "Altri Usi " . $totPAV . "/" . 7 . "<br>" . "Domestici " . $totPBV . "/" . 2;
                                    }
                                }
                            }
                        }
                    } else {
                        if ($totPunteggio >= 25 && $totPunteggio < 30) {          /* 5' fascia */
                            if ($totPAV >= 18 && $totPBV >= 7) {
                                echo "1.560,00€ premio raggiunto";
                                echo "Aggiudicati il premio da 2.000€! manca soltanto: " . $totPAV . "/" . 21 . " Alto Valore " . "\n" . $totPBV . "/" . 9 . " Basso Valore";
                            } else {
                                if ($totPAV >= 15 && $totPBV >= 6) {
                                    echo "1.200,00€ premio raggiunto";
                                    echo "Aggiudicati il premio da 1.560,00€! manca soltanto: " . $totPAV . "/" . 18 . " Alto Valore " . "\n" . $totPBV . "/" . 7 . " Basso Valore";
                                } else {
                                    if ($totPAV >= 12 && $totPBV >= 5) {
                                        echo "900€ premio raggiunto";
                                        echo "Aggiudicati il premio da 1.200,00€! manca soltanto: " . $totPAV . "/" . 15 . " Alto Valore " . "\n" . $totPBV . "/" . 6 . " Basso Valore";
                                    } else {
                                        if ($totPAV >= 9 && $totPBV >= 3) {
                                            echo "520€ premio raggiunto";
                                            echo "Premio da 900 manca ancora: " . $totPAV . "/" . 12 . " Alto Valore " . "\n" . $totPBV . "/" . 5 . " Basso Valore";
                                        } else {
                                            if ($totPAV >= 7 && $totPBV >= 2) {
                                                echo "Premio da 350€ già vinto";
                                                echo "<br>";
                                                echo "Premio da 520€ manca ancora: " . $totPAV . "/" . 9 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
                                            } else {
                                                echo "Premio da 350€ manca ancora:<br>" . "Altri Usi " . $totPAV . "/" . 7 . "<br>" . "Domestici " . $totPBV . "/" . 2;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($totPunteggio >= 30) {          /* 6' fascia */
                                if ($totPAV >= 21 && $totPBV >= 9) {
                                    echo "PREMIO DA 2.000€ RAGGIUNTO COMPLIMENTI !!";
                                    echo "Totalizzando un totale di: " . $totPAV . "/" . 21 . " Alto Valore " . "\n" . $totPBV . "/" . 9 . " Basso Valore";
                                } else {
                                    if ($totPAV >= 18 && $totPBV >= 7) {
                                        echo "1.560,00€ premio raggiunto";
                                        echo "Aggiudicati il premio da 2.000€! manca soltanto: " . $totPAV . "/" . 21 . " Alto Valore " . "\n" . $totPBV . "/" . 9 . " Basso Valore";
                                    } else {
                                        if ($totPAV >= 15 && $totPBV >= 6) {
                                            echo "1.200,00€ premio raggiunto";
                                            echo "Aggiudicati il premio da 1.560,00€! manca soltanto: " . $totPAV . "/" . 18 . " Alto Valore " . "\n" . $totPBV . "/" . 7 . " Basso Valore";
                                        } else {
                                            if ($totPAV >= 12 && $totPBV >= 5) {
                                                echo "900€ premio raggiunto";
                                                echo "Aggiudicati il premio da 1.200,00€! manca soltanto: " . $totPAV . "/" . 15 . " Alto Valore " . "\n" . $totPBV . "/" . 6 . " Basso Valore";
                                            } else {
                                                if ($totPAV >= 9 && $totPBV >= 3) {
                                                    echo "520€ premio raggiunto";
                                                    echo "Premio da 900 manca ancora: " . $totPAV . "/" . 12 . " Alto Valore " . "\n" . $totPBV . "/" . 5 . " Basso Valore";
                                                } else {
                                                    if ($totPAV >= 7 && $totPBV >= 2) {
                                                        echo "Premio da 350€ già vinto";
                                                        echo "<br>";
                                                        echo "Premio da 520€ manca ancora: " . $totPAV . "/" . 9 . " Alto Valore " . "\n" . $totPBV . "/" . 3 . " Basso Valore";
                                                    } else {
                                                        echo "Premio da 350€ manca ancora:<br>" . "Altri Usi " . $totPAV . "/" . 7 . "<br>" . "Domestici " . $totPBV . "/" . 2;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
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

    <link rel="stylesheet" type="text/css" href="FIX-welcome-test-style.css">
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="style-circle-bar.css">

    <title>JLAB Office</title>
</head>

<body>
    <div class="container-header">
        <div class="header-welcome">
            <img src="jlab-logo-alpha.png" class="logo-jlab">
            <div class="titolo">
                <?php
                $asd = $_SESSION['username'];
                echo "<h1 style='font-family: Poppins; font-weight: bold'>Pannello di Controllo Amministrativo JLAB</h1>";
                ?>
            </div>

            <div>
                <a href="FIX-tipo.php" style="text-decoration: none;">
                    <button class="css-selector-icon"><img src='iconFornitures/upload-icon2.svg' style='width: 50px; height: 50px'></button>
                </a>
            </div>
            <div>
                <a href="logout.php" style="text-decoration: none;">
                    <button class="css-selector_Logout"><img src='iconFornitures/logout-icon.svg' style='width: 25px; height: 25px'></button>
                </a>
            </div>
        </div>
    </div>
    <div class="container-Space-Between">
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
            </ul>
        </section>
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span><?php conteggio($totPunteggio, $totPAV, $totPBV, $totContratti); ?></span>
            </li>
            <!--             <li class="step-wizard-item" id="li-completato">
                <span></span>
                <span>l'ape maya</span>
            </li> -->
        </ul>

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
    </div>

    <center>
        <div id="p-month">
            <p>Mese elettrico corrente: <br>Dal <?php echo "$use_date_F"; ?> AL <?php echo "$use_date_L"; ?></p>
            <p>Stai visualizzando i contratti: <br>Dal <?php echo "$selectDal"; ?> AL <?php echo "$selectAl"; ?></p>
        </div>
        <table class="content-table">
            <thead>
                <tr>
                    <!-- <th>N° ctr</th> -->
                    <th>Agente</th>
                    <th>Ragione Sociale</th>
                    <th>Iban</th>
                    <th>Email</th>
                    <th>Cell</th>
                    <th>Luce</th>
                    <th>Gas</th>
                    <th>Luce & Gas</th>
                    <th>Stipula</th>
                    <th>Data Inserimento</th>
                    <th>Stato</th>
                    <th>ID contratto</th>
                    <th class="login-text" style="font-size: 15px; font-weight: 800; display: flex; justify-content: center">Aggiorna Stato</th>
                </tr>
            </thead>

            <?php
            $i = 1;
            if (mysqli_num_rows($result_select) > 0) {
                while ($row = mysqli_fetch_assoc($result_select)) { ?>
                    <tbody>
                        <tr class="active-row">

                            <td style="color: black; bacakground-color: #fff"><?php echo $row['username'] ?></td>
                            <td style="color: black;"><?php echo $row['r_sociale'] ?></td>
                            <td style="color: black;"><?php echo $row['iban'] ?></td>
                            <td style="color: black;"><?php echo $row['email'] ?></td>
                            <td style="color: black;"><?php echo $row['tel'] ?></td>
                            <td style="color: black;">
                                <center>
                                    <div>
                                        <?php
                                        if ($row['luce'] == "Domestico") {
                                            echo "<img src='iconFornitures/luce_domestico.svg' style='width: 70px; height: 70px' >";
                                        } elseif ($row['luce'] == "Altri Usi") {
                                            echo "<img src='iconFornitures/luce_business.svg' style='width: 70px; height: 70px' >";
                                        } elseif ($row['luce'] == "Privato A") {
                                            echo "<img src='iconFornitures/privato-altriusi.svg' style='width: 70px; height: 70px' >";
                                        }
                                        ?>
                            </td>
                            <td>
                                <center>
                                    <div><?php
                                            if ($row['gas'] == "Domestico") {
                                                echo "<img src='iconFornitures/gas_domestico.svg' style='width: 70px; height: 70px' >";
                                            } elseif ($row['gas'] == "Altri Usi") {
                                                echo "<img src='iconFornitures/gas_business.svg' style='width: 70px; height: 70px' >";
                                            }
                                            ?>
                            </td>
                            <td>
                                <center>
                                    <div><?php
                                            if ($row['luce_gas'] == "Domestico") {
                                                echo "<img src='iconFornitures/luceGas_Dom.svg' style='width: 70px; height: 70px' >";
                                            } elseif ($row['luce_gas'] == "Altri Usi") {
                                                echo "<img src='iconFornitures/luceGas_Business.svg' style='width: 70px; height: 70px' >";
                                            }
                                            ?>
                            </td>
                            <td style="color: black;"><?php echo $row['stipula'] ?></td>
                            <td style="color: black;"><?php echo $row['insert_date'] ?></td>
                            <td style="color: black;"><?php echo "<center>";
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

                            <td style="color: black;"><?php echo $row['id_delContratto'] ?></td>

                            <td>
                                <div class="container_statoUP">
                                    <form action="" method="POST" class="login-email" id="formInserimento">
                                        <div class="input-group">
                                            <input type="integer" placeholder="Conferma ID" name="id_delContratto" value="">
                                        </div>
                                        <div class="input-group">
                                            <input list="lista-stati-valori" id="lista-stati" name="lista-stati" placeholder="Scegli Stato" />
                                            <datalist id="lista-stati-valori">
                                                <option value="1">Inserito
                                                <option value="2">Lavorazione
                                                <option value="3">Sospeso
                                                <option value="4">KO
                                                <option value="5">APPROVATO
                                            </datalist>
                                        </div>
                                        <div class="input-group">
                                            <button name="submit" class="btn">Aggiorna</button>
                                        </div>
                                    </form>
                                </div>
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

    <div class="container-SpaceBetween">
        <div class="container">
            <div class="container_statoUP-header">
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Alto Valore: <?php echo $totPAV ?></p>
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Basso Valore: <?php echo $totPBV ?></p>
                <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
            </div>
        </div>

        <div class="container">
            <?php                                               // CLONE
            $winner = "SELECT * FROM users
           /* WHERE bonus != '0' */
           ORDER BY bonus DESC;";

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
            <img src="coppa.png" alt="" style="width:fit-content; height:15rem">
            <table class="w3-table-all w3-center" border="1">
                <tr style='background-color: #A49BFE; color: white'>
                    <th>Agente</th>
                    <th>Bonus Vinto</th>
                </tr>
                <?php
                $i = 1;
                if (mysqli_num_rows($winnerList) > 0) {
                    while ($row = mysqli_fetch_assoc($winnerList)) { ?>
                        <tr>
                            <td><?php echo $row['username'] ?></td>
                            <td><?php echo $row['bonus'] ?></td>
                        </tr>

                <?php }
                } ?>
            </table>
            <div class="winnerList_container_P">
                <p style="padding-left: 1rem">Bonus Vinti <span style='font-weight: bold'>questo</span> mese elettrico</p>
            </div>
        </div>
        </table>

        <div class="container">
            <form action="" method="POST" class="login-email" id="formInserimento">
                <div class="container_statoUP-header">
                    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Ricerca Contratti</p>
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
                                <input type="button" value="Mese Elettrico" class="btn" style="background-color: #58A332; width: 60%;"></a>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div style='display: flex; justify-content: center; align-content: center'>
        <div class="container">
            <img src="coppa-last.png" alt="" style="width:fit-content; height:10rem">
            <table class="w3-table-all w3-center" border="1">
                <tr style='background-color: #A49BFE; color: white'>
                    <th>Agente</th>
                    <th>Premio</th>
                </tr>
                <?php
                $i = 1;
                if (mysqli_num_rows($winnerListLast) > 0) {
                    while ($row = mysqli_fetch_assoc($winnerListLast)) { ?>
                        <tr>
                            <td><?php echo $row['username'] ?></td>
                            <td><?php echo $row['bonus'] ?></td>
                        </tr>
                <?php }
                } ?>
            </table>
            <div class="winnerList_container_P">
                <p style="padding-left: 1rem">Bonus <span style='font-weight: bold'>scorso</span> mese elettrico</p>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
</body>

</html>