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

    if ($result) {
        echo "<script>alert('Aggiornato correttamente.')</script>";
        $_POST['id_delContratto'] = "";
        $idCtr = "";
        $_POST['lista-stati'] = "";
        $stato = "";
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


/* -----------------------------DAL-------------------------------------- */
$mese_mod = $mese - 1; // es. 8

if (strlen($mese_mod) == 1) {   // se il mese è tra 1 al 9, aggiunge uno 0 d'avanti.
    $length = 2;
    $mese_mod = str_pad($mese_mod, $length, "0", STR_PAD_LEFT);
}

$electric_date_F = 16 . "-" . $mese_mod . "-" . $anno; 
/* ---------------------------------------------------------------------- */

/* -----------------------------AL--------------------------------------- */

$electric_date_L = 15 . "-" . $mese . "-" . $anno;


/* ---------------------------------------------------------------------- */



$select = "SELECT * FROM contratti
            INNER JOIN users
            ON contratti.FK_id_users = users.id
            ORDER BY username, insert_date BETWEEN '$electric_date_F' AND '$electric_date_L' DESC;";

$result_select = mysqli_query($conn, $select);

// per stampare eventuali errori
if (!$result_select) {
    echo "Errore query della select" . mysqli_error($conn);
}

/* ---------------------------------------------------------------------------- */

/* ---------------------------------------------------------------------------- */
/* https://www.youtube.com/watch?v=zc1F50TeyIY */
/* ---------------------------------------------------------------------------- */


/* ----------------------------------- QUERY CONTEGGIO CONTRATTI BUSINESS IN TOTALE ------------- */
$querySum = "SELECT SUM(business) AS business FROM contratti where domestico = '0' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrBus = $row['business'];


/* ----------------------------------- QUERY CONTEGGIO CONTRATTI DOMESTICI IN TOTALE ------------- */
$querySum = "SELECT SUM(domestico) AS domestico FROM contratti where business = '0' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrDom = $row['domestico'];

/* --------------------------- QUERY CONTEGGIO CONTRATTI basso valore 0,5 Punti ------------- */
$querySum = "SELECT SUM(valore) AS valoreB FROM contratti where business = '0' AND stato = '5' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPBV = $row['valoreB'];
/* --------------------------- QUERY CONTEGGIO CONTRATTI ALTO valore 1 Punto ------------- */
$querySum = "SELECT SUM(valore) AS valoreA FROM contratti where domestico = '0' AND stato = '5' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPAV = $row['valoreA'];

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
        echo "OTTIENI 350€ DI PREMIO CON: <br>$totPAV /7 Alto Valore <br> $totPBV /2 Basso Valore";
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
                echo "<h1>Pannello di Controllo|DAL $electric_date_F |AL| $electric_date_L </h1>";
                ?>
            </div>

            <div class="input-group">
                <a href="FIX-tipo.php" style="text-decoration: none;">
                    <button class="css-selector" class="white-font">Inserisci</button>
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



    <div class="container">
        <div class="container_statoUP-header">
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Alto Valore: <?php echo $totPAV ?></p>
            <p class="login-text" style="font-size: 2rem; font-weight: 400;">Basso Valore: <?php echo $totPBV ?></p>
        </div>
        <form action="" method="POST" class="login-email" id="formInserimento">
            <div class="container_statoUP-header">
                <p class="login-text" style="font-size: 2rem; font-weight: 800;">Aggiorna Stato</p>
                <div class="input-group">
                    <input type="integer" placeholder="ID del Contratto d'aggiornare" name="id_delContratto" value="<?php echo $_POST['id_delContratto']; ?>">
                </div>



                <div class="input-group">
                    <label for="lista-stati">Inserisci stato</label>
                    <input list="lista-stati-valori" id="lista-stati" name="lista-stati" />

                    <datalist id="lista-stati-valori">
                        <option value="1">Inserito
                        <option value="2">Lavorazione
                        <option value="3">Sospeso
                        <option value="4">KO
                        <option value="5">APPROVATO
                    </datalist>
                </div>

            </div>
            <!--             <p>1=Inserito, 2=Lavorazione, 3=Sospeso, 4=KO, 5=APPROVATO</p> -->
            <div style="display: flex; justify-content: space-between">
                <p>1=Inserito</p>
                <p>2=lavorazione</p>
                <p>3=Sospeso</p>
                <p>4=KO</p>
                <p>5=APPROVATO</p>
            </div>

            <div class="input-group">
                <button name="submit" class="btn">Aggiorna stato del contratto</button>
            </div>

        </form>
    </div>





    <table class="w3-table-all w3-center" border="1">
        <tr>
            <th>N° ctr</th>
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
            <th class="login-text" style="font-size: 15px; font-weight: 800;">Aggiorna Stato</th>

        </tr>
        <?php
        $i = 1;
        if (mysqli_num_rows($result_select) > 0) {
            while ($row = mysqli_fetch_assoc($result_select)) { ?>
                <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo $row['username'] ?></td>
                    <td><?php echo $row['r_sociale'] ?></td>
                    <td><?php echo $row['iban'] ?></td>
                    <td><?php echo $row['email'] ?></td>
                    <td><?php echo $row['tel'] ?></td>
                    <td>
                        <center>
                            <div id=td-color><?php echo $row['luce'] ?>
                    </td>
                    <td>
                        <center>
                            <div id=td-color2><?php echo $row['gas'] ?>
                    </td>
                    <td>
                        <center>
                            <div id=td-color3><?php echo $row['luce_gas'] ?>
                    </td>
                    <td><?php echo $row['stipula'] ?></td>
                    <td><?php echo $row['insert_date'] ?></td>
                    <td><?php echo "<center>";
                        if ($row['stato'] == "1") {
                            echo "Inserito";
                        } else {
                            if ($row['stato'] == "2") {
                                echo "Lavorazione";
                            } else {
                                if ($row['stato'] == "3") {
                                    echo "Sospeso";
                                } else {
                                    if ($row['stato'] == "4") {
                                        echo "KO";
                                    } else {
                                        if ($row['stato'] == "5") {
                                            echo "APPROVATO";
                                        }
                                    }
                                }
                            }
                        } ?></td>

                    <td><?php echo $row['id_delContratto'] ?></td>

                    <td>
                        <div class="container_statoUP">
                            <form action="" method="POST" class="login-email" id="formInserimento">

                                <div class="input-group">
                                    <input type="integer" placeholder="Conferma ID" name="id_delContratto" value="<?php echo $_POST['id_delContratto']; ?>">
                                </div>



                                <div class="input-group">
                                    <!-- <label for="lista-stati">Inserisci stato</label> -->
                                    <input list="lista-stati-valori" id="lista-stati" name="lista-stati" placeholder="Seleziona Stato" />

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
        <?php      }
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
        </style>
    </table>

    <script type="text/javascript">
        function stato_update(value, id_delContratto) {
            //alert(id_delContratto);
            //alert(value);
            let url = "http://localhost/dashboard/asdd/LoginPage_PHP-MySql/contratti_admin.php";
            window.location.href = url + "?id=" + id_delContratto + "&status=" + value;
            aggiorna(id_delContratto, value);
        }
    </script>

    <script>
        function aggiorna() {
            <?php
            $idCtr = $_POST['id_delContratto'];
            $stato = $_POST['value'];

            $sql = "UPDATE contratti set stato='$stato' where id_delContratto='$idCtr'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo "<script>alert('Lo stato del contratto è stato aggiornato correttamente.')</script>";
                $_POST['id_delContratto'] = "";
                $idCtr = "";
                $_POST['stato'] = "";
                $stato = "";
            } else {
                echo "<script>alert('Lo stato del contratto NON è stato aggiornato correttamente!')</script>";
            }
            ?>
        }
    </script>
    <!-- </div> -->
    <div class="container">
        <div class="input-group">
            <a href="FIX-tipo-privato.php" style="text-decoration: none;" <button class="btn" name="btn-scelta" value="1"><?php echo $userSelezionato; ?></button>
            </a>
        </div>

        <div class="input-group">
            <a href="FIX-tipo-azienda.php" style="text-decoration: none;" <button class="btn" name="btn-scelta" value="2">Azienda</button>
            </a>
        </div>
    </div>


    <input type="hidden" name="id" value="<?php echo $_SESSION['userID']; ?>"> <!-- i am taking the id value corresponding to the agent database row -->
    <!-- </div> -->
    </section>

</body>

</html>