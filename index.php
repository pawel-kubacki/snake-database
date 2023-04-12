<?php
session_start();

// Logowanie:
if ((!isset($_POST['login'])) || (!isset($_POST['haslo']))) {
    if (isset($_SESSION['blad']))
        unset($_SESSION['blad']);
} else if ((isset($_POST['login'])) || (isset($_POST['haslo']))) {
    require_once "connect.php";
    try {
        $polaczenie = new mysqli($host, $user, $password, $name);
        if ($polaczenie->connect_errno != 0) {
            echo "Error: " . $polaczenie->connect_errno;
        } else {
            $login = $_POST['login'];
            $haslo = $_POST['haslo'];
            $_SESSION['fr_login'] = $login;
            $_SESSION['fr_haslo'] = $haslo;
            $login = htmlentities($login, ENT_QUOTES, "UTF-8");
            if (
                $rezultat = $polaczenie->query(
                    sprintf(
                        "SELECT * FROM snake.players WHERE user='%s'",
                        mysqli_real_escape_string(
                            $polaczenie,
                            $login
                        )
                    )
                )
            ) {
                $ilu_userow = $rezultat->num_rows;
                if ($ilu_userow > 0) {
                    $wiersz = $rezultat->fetch_assoc();
                    if (password_verify($haslo, $wiersz['password'])) {
                        $_SESSION['zalogowany'] = true;
                        $_SESSION['id'] = $wiersz['id'];
                        $_SESSION['user'] = $wiersz['user'];
                        $_SESSION['email'] = $wiersz['email'];
                        unset($_SESSION['blad']);
                        $rezultat->free_result();
                        header('Location: snake.php');
                    } else {
                        $_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
                    }
                } else {
                    $_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
                }
            }
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
        echo '<br />Informacja developerska: ' . $e;
    }
    $polaczenie->close();
}

// Rejestracja:
if (isset($_POST['email'])) {
    $ok = true;

    $nick = $_POST['nick'];
    if ((strlen($nick) < 3) || (strlen($nick) > 20)) {
        $ok = false;
        $_SESSION['e_nick'] = "Nick musi posiadać od 3 do 20 znaków!";
    }
    if (ctype_alnum($nick) == false) {
        $ok = false;
        $_SESSION['e_nick'] = "Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
    }

    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
        $ok = false;
        $_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
    }

    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    if ((strlen($password1) < 8) || (strlen($password1) > 20)) {
        $ok = false;
        $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
    }
    if ($password1 != $password2) {
        $ok = false;
        $_SESSION['e_password'] = "Podane hasła nie są identyczne!";
    }
    $password_hash = password_hash($password1, PASSWORD_DEFAULT);

    if (!isset($_POST['regulations'])) {
        $ok = false;
        $_SESSION['e_regulations'] = "Potwierdź akceptację regulaminu!";
    }

    $_SESSION['fr_nick'] = $nick;
    $_SESSION['fr_email'] = $email;
    $_SESSION['fr_password1'] = $password1;
    $_SESSION['fr_password2'] = $password2;
    if (isset($_POST['regulations']))
        $_SESSION['fr_regulations'] = true;
    // $captcha  = "Wpisz wygenerowany kod!";
    // $sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $sekret . '&response=' . $_POST['g-recaptcha-response']);
    // $odpowiedz = json_decode($sprawdz);
    // if ($odpowiedz->success == false) {
    //     $ok = false;
    //     $_SESSION['e_bot'] = "Potwierdź, że nie jesteś botem!";
    // }

    if ($ok === true) {
        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $connection = new mysqli($host, $user, $password, $name);
            if ($connection->connect_errno != 0) {
                throw new Exception(mysqli_connect_errno());
            } else {
                $isEmail = $connection->query("SELECT id FROM snake.players WHERE email='$email'");
                if (!$isEmail)
                    throw new Exception($connection->error);
                $howManyEmails = $isEmail->num_rows;
                if ($howManyEmails > 0) {
                    $ok = false;
                    $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
                }

                $isNick = $connection->query("SELECT id FROM snake.players WHERE user='$nick'");
                if (!$isNick)
                    throw new Exception($connection->error);
                $howManyNicks = $isNick->num_rows;
                if ($howManyNicks > 0) {
                    $ok = false;
                    $_SESSION['e_nick'] = "Istnieje już gracz o takim nicku! Wybierz inny.";
                }

                if ($ok == true) {
                    if ($connection->query("INSERT INTO snake.players VALUES (NULL, '$nick', '$email', '$password_hash', now())")) {
                        $_SESSION['registration_ok'] = '<p style="color:green">Udana rejestracja. Zaloguj się na swoje konto!</p>';
                        if (isset($_SESSION['fr_nick']))
                            unset($_SESSION['fr_nick']);
                        if (isset($_SESSION['fr_email']))
                            unset($_SESSION['fr_email']);
                        if (isset($_SESSION['fr_password1']))
                            unset($_SESSION['fr_password1']);
                        if (isset($_SESSION['fr_password2']))
                            unset($_SESSION['fr_password2']);
                        if (isset($_SESSION['fr_regulations']))
                            unset($_SESSION['fr_regulations']);
                        if (isset($_SESSION['fr_login']))
                            unset($_SESSION['fr_login']);
                        if (isset($_SESSION['fr_haslo']))
                            unset($_SESSION['fr_haslo']);

                        if (isset($_SESSION['e_nick']))
                            unset($_SESSION['e_nick']);
                        if (isset($_SESSION['e_email']))
                            unset($_SESSION['e_email']);
                        if (isset($_SESSION['e_password']))
                            unset($_SESSION['e_password']);
                        if (isset($_SESSION['e_regulations']))
                            unset($_SESSION['e_regulations']);
                        // if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
                    } else {
                        throw new Exception($connection->error);
                    }
                }
                $connection->close();
            }
        } catch (Exception $e) {
            echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
            echo '<br />Informacja developerska: ' . $e;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake</title>

    <link rel="stylesheet" type="text/css" href="./css/styles.css" />
</head>

<body>
    <div id="mainWrapper">
        <div id="welcome">
            <h1></h1>
            <img src="./files/snakeLogo.png" alt="Snake - super gra" width="80" height="80">
            <p class="logo">Witamy w grze Snake!</p>
        </div>
        <div id="formWrapper" name="mainWrapper">
            <form action="index.php" method="post">
                <h3>Zarejestruj się:</h3>
                <label>Podaj nazwę użykownika</label>
                <input type="text" name="nick" value="<?php
                if (isset($_SESSION['fr_nick'])) {
                    echo $_SESSION['fr_nick'];
                    unset($_SESSION['fr_nick']);
                } ?>" />
                <?php
                if (isset($_SESSION['e_nick'])) {
                    echo '<div class="error">' . $_SESSION['e_nick'] . '</div>';
                    unset($_SESSION['e_nick']);
                }
                ?>
                <label>Podaj adres email</label>
                <input type="email" name="email" value="<?php
                if (isset($_SESSION['fr_email'])) {
                    echo $_SESSION['fr_email'];
                    unset($_SESSION['fr_email']);
                } ?>" />
                <?php
                if (isset($_SESSION['e_email'])) {
                    echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                    unset($_SESSION['e_email']);
                }
                ?>
                <label>Podaj hasło</label>
                <input type="current-password" name="password1" value="<?php
                if (isset($_SESSION['fr_password1'])) {
                    echo $_SESSION['fr_password1'];
                    unset($_SESSION['fr_password1']);
                } ?>" />
                <?php
                if (isset($_SESSION['e_password'])) {
                    echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                    unset($_SESSION['e_password']);
                }
                ?>
                <label>Powtórz hasło</label>
                <input type="repeat-password" name="password2" value="<?php
                if (isset($_SESSION['fr_password2'])) {
                    echo $_SESSION['fr_password2'];
                    unset($_SESSION['fr_password2']);
                } ?>" />
                <div id="regulations">
                    <label>
                        <input type="checkbox" name="regulations" <?php
                        if (isset($_SESSION['fr_regulations'])) {
                            echo "checked";
                            unset($_SESSION['fr_regulations']);
                        }
                        ?> /> Akceptuję regulamin
                    </label>
                    <?php
                    if (isset($_SESSION['e_regulations'])) {
                        echo '<div class="error">' . $_SESSION['e_regulations'] . '</div>';
                        unset($_SESSION['e_regulations']);
                    }
                    ?>
                    <a href="./files/regulations.odt">Pobierz regulamin</a>
                </div>

                <button type="submit" name="registration">Zarejestruj :)</button>
                <?php
                if (isset($_SESSION['registration_ok'])) {
                    echo $_SESSION['registration_ok'];
                    if (isset($_SESSION['registration_ok']))
                        unset($_SESSION['registration_ok']);
                }
                ?>
            </form>
            <form action="index.php" method="post">
                <h3>Zaloguj się:</h3>
                <label>Podaj login</label>
                <input type="text" name="login" value="<?php
                if (isset($_SESSION['fr_login'])) {
                    echo $_SESSION['fr_login'];
                    unset($_SESSION['fr_login']);
                } ?>" />
                <label>Podaj hasło</label>
                <input type="current-password" name="haslo" value="<?php
                if (isset($_SESSION['fr_haslo'])) {
                    echo $_SESSION['fr_haslo'];
                    unset($_SESSION['fr_haslo']);
                } ?>" />
                <button type="submit" name="inputRegistration">Zaloguj...</button>
                <?php
                if (isset($_SESSION['blad'])) {
                    echo $_SESSION['blad'];
                }
                ; ?>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="./js/index.js"></script>
</body>

</html>