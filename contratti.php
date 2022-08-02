<?php 

include 'config.php';

error_reporting(0);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
}

/* -------------------------------------- */

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


            <div class="welcome_btn">
                <div class="css-selector">
                <a href="welcome.php" class="white-font">Carica</a>
                </div>

                <div class="css-selector_INVERSO">
                <a href="contratti2.php" class="white-font">lista 2</a>
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
                <th>Ragione Sociale</th>
                <th>IBAN</th>
                <th>Email</th>
                <th>Cellulare</th>
                <th>Stipula</th>
                <th>Data Inserimento</th>
                <th class="w3-center">Stato</th>
            </tr>

            <tr>
                <td>Pizzeria CampoBasso S.r.l</td>
                <td>IT009876253456776</td>
                <td>pizza@gmail.com</td>
                <td>3495268756</td>
                <td>13/03/22</td>
                <td>14/03/22</td>
                <td class="w3-center">INSERITO</td>
            </tr>

                echo "            
                <tr>
                    <td>Q Pizzeria CampoBasso S.r.l</td>
                    <td>QIT009876253456776</td>
                    <td>Qpizza@gmail.com</td>
                    <td>Q3495268756</td>
                    <td>Q13/03/22</td>
                    <td>Q14/03/22</td>
                    <td class="w3-center">QINSERITO</td>
                </tr>
                "
            
            <tr>
                <td>MC Donald's C.C Etnapolis S.p.A</td>
                <td>IT23474566776</td>
                <td>mcdonalds@mc.com</td>
                <td>3477823600</td>
                <td>15/03/22</td>
                <td>17/03/22</td>
                <td class="w3-center">APPROVATO</td>
            </tr>
            <tr>
                <td>--> Rossopomodoro S.r.l</td>
                <td>IT11187625340000</td>
                <td>info@rossopomodoro.it</td>
                <td>3495268756</td>
                <td>18/03/22</td>
                <td>19/03/22</td>
                <td class="w3-center">LAVORAZIONE</td>
            </tr>
            
        </table>

<!--     <div class="container_main">
        <div class="container-lista">
        <canvas id="oilChart" width="600" height="400"></canvas>
        </div>
    </div> -->



</body>
</html>