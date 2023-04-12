<?php
session_start();
if (!isset($_SESSION["zalogowany"])) {
    header('Location: index.php');
    exit();
}
if (isset($_POST['inputScores']) || isset($_POST['inputLevel'])) {
    $zmienna1 = $_POST['inputScores'];
    $zmienna2 = $_POST['inputLevel'];
    require_once "connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli($host, $user, $password, $name);
        if ($connection->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            if ($connection->query("INSERT INTO snake.results VALUES (NULL, '$_SESSION[user]', '$zmienna1' , '$zmienna2', now())")) {
                echo '<span style="color:green;">Brawo! Dodano wyniki do bazy!</span>';
                header('Location: snake.php');
            } else {
                throw new Exception($connection->error);
            }
            $connection->close();
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności!</span>';
        echo '<br />Informacja developerska: ' . $e;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake</title>
    <link rel="stylesheet" type="text/css" href="./css/snake.css">
</head>

<body>
    <div id="mainWrapper">
        <div id="setNameWrapper">
            <div>
                <p class="setNameLabel">
                    <?php
                    echo '<a id="a" href="./logout.php">Wyloguj się!</a>' . " ----- Witaj " . $_SESSION['user'] . " :)";
                    ?>
                </p>
            </div>
            <p class="logo">Witamy w grze Snake!</p>
        </div>
        <div id="snakeWrapper">
            <div id="info">
                <p>Po kliknięciu w przycisk "Rozpocznij grę!", należy rozpcząć grę poprzez wciśnięcie klawisza Enter
                    <br />
                    Do sterowania wężem służą strzałki.<br />
                    Pauze włączamy poprzez wciśnięcie klawisza spacji, a wznowienie gry następuje przy użycia klawisza
                    enter.<br />
                </p>
                <button id="start">Rozpocznij grę!</button>
            </div>
            <div id="area">
                <div id="enterToStart">Enter = Start</div>
                <div id="pauza">Pauza</div>
                <div class="snakeBody" id="snakeHead">
                    <div id="eyesWrapper">
                        <div class="eyes"></div>
                        <div class="eyes"></div>
                    </div>
                    <div id="mouth"></div>
                </div>
            </div>
            <div id="scoreBox">Punkty: <strong>0</strong></div>
            <div id="gameOver" name="gameOver">
                <p>Koniec gry!</p>
                <span>Sprawdź poprawność imienia.</span>
                <form action="snake.php" method="POST" id="form-input-value">
                    <input type="text" id="inputScores" name="inputScores" value="<?php
                    if (isset($_SESSION['show1'])) {
                        echo $_SESSION['show1'];
                        unset($_SESSION['show1']);
                    } ?>" />
                    <input type="text" id="inputLevel" name="inputLevel" value="<?php
                    if (isset($_SESSION['show2'])) {
                        echo $_SESSION['show2'];
                        unset($_SESSION['show2']);
                    } ?>" />
                    <button id="saveButton" type="submit">Zapisz wynik</button>
                    <?php
                    if (isset($_POST['inputScores']))
                        unset($_POST['inputScores']);
                    if (isset($_POST['inputLevel']))
                        unset($_POST['inputLevel']);
                    ?>
                </form>
            </div>
            <div id="level">
                <p>Wybierz poziom</p>
                <select id="kindOfLevel">
                    <option>Wybierz poziom</option>
                    <option>Bardzo łatwy</option>
                    <option>Łatwy</option>
                    <option>Średni</option>
                    <option>Dość trudny</option>
                    <option>Trudny</option>
                    <option>Bardzo trudny</option>
                    <option>Prawie nie wykonalny :)</option>
                    <option>Nie wykonalny :)</option>
                </select>
                <button id="saveLevel" type="submit">Start</button>
            </div>
        </div>
        <div id="historyWrapper">
            <h1>Historia wyników:</h1>
            <div id="historyContent"></div>
        </div>
    </div>
    <script type="text/javascript" src="./js/snake.js"></script>
</body>

</html>