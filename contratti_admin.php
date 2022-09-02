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
/* ---------------------------------------------------------------------- */
$idSessione = $_SESSION['userID'];
$meseCorrente = new DateTime();
$dataOra = $meseCorrente->format('m-Y');


$select = "SELECT * FROM contratti
            INNER JOIN users
            ON contratti.FK_id_users = users.id
            ORDER BY username, insert_date DESC;";

$result_select = mysqli_query($conn, $select);

// per stampare eventuali errori
if (!$result_select) {
    echo "Errore query della select" . mysqli_error($conn);
}

/* ---------------------------------------------------------------------------- */

$selectMenus = "SELECT * FROM contratti
INNER JOIN users
ON contratti.FK_id_users = users.id AND users.username = '$userSelezionato'
ORDER BY username, insert_date DESC;";

$result_select_menu = mysqli_query($conn, $selectMenus);

// per stampare eventuali errori
if (!$result_select_menu) {
    echo "Errore query della select" . mysqli_error($conn);
}

/* ---------------------------------------------------------------------------- */
/* https://www.youtube.com/watch?v=zc1F50TeyIY */
/* ---------------------------------------------------------------------------- */

$sql = "select * from contratti";
$result = mysqli_query($conn, $queryCambioStato);
//Get Update id and status  
if (isset($_GET['id']) && isset($_GET['stato'])) {
    $id = $_GET['id'];
    $stato = $_GET['stato'];
    $sql = "update contratti set stato='$stato' where id='$id'";
    $result = mysqli_query($conn, $queryCST);
    if ($result) {
        echo "<script>alert('Stato aggiornato con successo.')</script>";
    } else {
        echo "<script>alert('ERRORE: Lo stato non è stato aggiornato correttamente ')</script>";
    }
}



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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

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
                echo "<h1>Pannello di Controllo</h1>";
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

    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Alto Valore: <?php echo $totPAV ?></p>
    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Basso Valore: <?php echo $totPBV ?></p>

    <div class="container">
        <div>Seleziona Agente </div>
        <!-- <div>Ordina per stato</div> -->
        <div>

            <select id="list" onchange="getSelectValue();">
                <option value="Alessandro">Alessandro</option>
                <option value="Mario Rossi">Mario Rossi</option>
                <option value="Roberto">Roberto</option>
                <option value="Salvatore">Salvatore</option>
            </select>

            <script>
                function getSelectValue() {
                    var selectedValue = document.getElementById("list").value;
                    console.log(selectedValue);
                }
            </script>

            <?php
            $userSelezionato = '
                    <script>
                        var selectedValueE = document.getElementById("list").value;
                        document.writeln(selectedValueE);
                    </script> ';
            ?>

        </div>
    </div>





    <table class="w3-table-all w3-center" border="1">
        <tr>
            <th>Ordine</th>
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
                    <td><?php echo $row['luce'] ?></td>
                    <td><?php echo $row['gas'] ?></td>
                    <td><?php echo $row['luce_gas'] ?></td>
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

                    <td>
                        <select onchange="status_update(this.options[this.selectedIndex].value,'<?php echo $row['stato'] ?>')">
                            <option value="">Update Status</option>
                            <option value="1">Inserito</option>
                            <option value="2">Lavorazione</option>
                            <option value="3">Sospeso</option>
                        </select>
                    </td>
                </tr>
        <?php      }
        } ?>
    </table>

    <script type="text/javascript">  
      function status_update(value,id){  
           //alert(id);  
           let url = "http://127.0.0.1/tutorials/status_update/index.php";  
           window.location.href= url+"?id="+id+"&status="+value;  
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