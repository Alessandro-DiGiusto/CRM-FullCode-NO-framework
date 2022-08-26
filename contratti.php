<?php

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

$idSessione = $_SESSION['userID'];
$meseCorrente = new DateTime();
$dataOra = $meseCorrente->format('m-Y');

$select = "SELECT r_sociale, iban, email, tel, stipula, insert_date, stato, luce, gas, luce_gas
            FROM contratti
            WHERE contratti.FK_id_users = '$idSessione' AND insert_date LIKE '%$dataOra%'
            ORDER BY insert_date DESC;";

$result_select = mysqli_query($conn, $select);

// per stampare eventuali errori
if (!$result_select) {
    echo "Errore query della select" . mysqli_error($conn);
}



/* ----------------------------------- QUERY CONTEGGIO CONTRATTI BUSINESS IN TOTALE ------------- */
$querySum = "SELECT SUM(business) AS business FROM contratti where domestico = '0' AND FK_id_users = '$idSessione' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrBus = $row['business'];
/* ----------------------------------- QUERY CONTEGGIO CONTRATTI DOMESTICI IN TOTALE ------------- */
$querySum = "SELECT SUM(domestico) AS domestico FROM contratti where business = '0' AND FK_id_users = '$idSessione' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$sumCtrDom = $row['domestico'];

/* --------------------------- QUERY CONTEGGIO CONTRATTI basso valore 0,5 Punti ------------- */
$querySum = "SELECT SUM(valore) AS valoreB FROM contratti where business = '0' AND FK_id_users = '$idSessione' AND stato = '5' AND insert_date LIKE '%$dataOra%';";
$result = mysqli_query($conn, $querySum);
$row = mysqli_fetch_assoc($result);
$totPBV = $row['valoreB'];
/* --------------------------- QUERY CONTEGGIO CONTRATTI ALTO valore 1 Punto ------------- */
$querySum = "SELECT SUM(valore) AS valoreA FROM contratti where domestico = '0' AND FK_id_users = '$idSessione'  AND stato = '5' AND insert_date LIKE '%$dataOra%';";
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
                echo "<h1>Ciao " . $asd  . " !</h1>";
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

    <div id="div-desktop" class="container-progressBar">
        <div class="skill">
            <p>Totale Punteggio</p>
            <div class="outer">
                <div class="inner">
                    <svg style="position: absolute; z-index:1;" id="svg1" xmlns="http://www.w3.org/2000/svg" version="1.1" width="160px" height="160px">
                        <defs>
                            <linearGradient id="GradientColor">
                                <stop offset="0%" stop-color="#e91e63" />
                                <stop offset="100%" stop-color="#673ab7" />
                            </linearGradient>
                        </defs>
                        <circle class="circle1" cx="80" cy="80" r="70" stroke-linecap="round" />
                    </svg>
                    <div id="number1">
                    </div>
                </div>
            </div>
        </div>
        <script>
            let number = document.getElementById("number1");
            let counter = 0;
            setInterval(() => {
                if (counter == 65) {
                    clearInterval();
                } else {
                    counter += 1;
                    number.innerHTML = counter + "%";
                }
            }, 50);
        </script>
        <div class="skill">
            <p>Alto Valore</p>
            <div class="outer">
                <div class="inner">
                    <svg style="position: absolute; z-index:2;" id="svg2" xmlns="http://www.w3.org/2000/svg" version="1.1" width="160px" height="160px">
                        <defs>
                            <linearGradient id="GradientColor">
                                <stop offset="0%" stop-color="#e91e63" />
                                <stop offset="100%" stop-color="#673ab7" />
                            </linearGradient>
                        </defs>
                        <circle class="circle2" cx="80" cy="80" r="70" stroke-linecap="round" />
                    </svg>
                    <div id="number2">
                    </div>
                </div>
            </div>
        </div>
        <script>
            let number2 = document.getElementById("number2");
            let counter2 = 0;
            setInterval(() => {
                if (counter2 == 37) {
                    clearInterval();
                } else {
                    counter2 += 1;
                    number2.innerHTML = counter2 + "%";
                }
            }, 50);
        </script>
        <div class="skill">
            <p>Basso Valore</p>
            <div class="outer">
                <div class="inner">
                    <svg style="position: absolute; z-index:3;" id="svg3" xmlns="http://www.w3.org/2000/svg" version="1.1" width="160px" height="160px">
                        <defs>
                            <linearGradient id="GradientColor">
                                <stop offset="0%" stop-color="#e91e63" />
                                <stop offset="100%" stop-color="#673ab7" />
                            </linearGradient>
                        </defs>
                        <circle class="circle3" cx="80" cy="80" r="70" stroke-linecap="round" />
                    </svg>
                    <div id="number3">
                    </div>
                </div>
            </div>
        </div>
        <script>
            let number3 = document.getElementById("number3");
            let counter3 = 0;
            setInterval(() => {
                if (counter3 == 81) {
                    clearInterval();
                } else {
                    counter3 += 1;
                    number3.innerHTML = counter3 + "%";
                }
            }, 50);
        </script>
    </div>

    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Totale Punteggio: <?php echo $totPunteggio ?></p>
    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Alto Valore: <?php echo $totPAV ?></p>
    <p class="login-text" style="font-size: 2rem; font-weight: 400;">Basso Valore: <?php echo $totPBV ?></p>


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
            echo "<td><center>";
            if ($row['stato'] == "1") {
                echo "Inserito" . "</tr>";
            } else {
                if ($row['stato'] == "2") {
                    echo "Lavorazione" . "</tr>";
                } else {
                    if ($row['stato'] == "3") {
                        echo "Sospeso" . "</tr>";
                    } else {
                        if ($row['stato'] == "4") {
                            echo "KO" . "</tr>";
                        } else {
                            if ($row['stato'] == "5") {
                                echo "APPROVATO" . "</tr>";
                            }
                        }
                    }
                }
            }
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
            <a href="FIX-tipo-privato.php" style="text-decoration: none;" <button class="btn" name="btn-scelta" value="1">Privato</button>
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