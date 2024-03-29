<?php
include "../cfg.php";
session_start();
?>

<div class="lista_postron">
    <?php
    if ($_SESSION['login'] == $login && $_SESSION['haslo'] == $haslo) {
        // sprawdzam czy POST i GET są
        if (isset($_POST) && isset($_POST["tresc"])) {
            // zmieniam treść POSTA przez specjalne znakowanie
            $tresc = htmlspecialchars($_POST["tresc"]);
            // sprawdzam czy wybrano odpowiednią pozycję do edycji
            if (isset($_POST["page_id"])) {
                $query = 'UPDATE page_list SET `page_title` = "' . $_POST['tytul'] . '", `page_content` = "' . $tresc . '" WHERE id = ' . $_POST['page_id'] . ' LIMIT 1';
            }
            // sprawdzam dodwanie nowej podstrony
            else {
                $query = 'INSERT INTO page_list(page_title, page_content) VALUES ("' . $_POST['tytul'] . '","' . htmlspecialchars($_POST['tresc']) . '")';
            }
            mysqli_query($conn, $query);
            // wyświetlam odpowiednią informację
            if (isset($_POST['page_id'])) {
                echo '<div class="info">zaktualizowano stronę ' . $_POST['page_id'] . '</div>';
            } else {
                echo '<div class="info">dodano stronę</div>';
            }
        }

        // sprawdzam czy GET jest jest usuń i usuwam dana podstronę
        elseif (isset($_GET['usun'])) {
            echo '<div class="info">Usunięto stronę o id=' . $_GET['page_id'] . '</div>';
            $query = 'DELETE FROM page_list WHERE id = "' . $_GET['page_id'] . '" LIMIT 1';
            mysqli_query($conn, $query);
        }


        ListaPodstron($conn);
        if (isset($_GET['dodaj'])) {
            DodajPodstrone($conn);
        } elseif (isset($_GET['edytuj'])) {
            EdytujPodstrone($conn);
        }
        echo '<form action="podstrony.php" method="get">
        <input type="submit" name="dodaj" value="dodaj"><br>
        </form>
        <a href="sklep.php">Zarządzaj Sklepem</a><br>
        <a href="produkt.php"> Zarządzaj Produktem</a><br>
        <a href="logout.php"> Wyloguj</a>';
    } else {
        header('location: admin.php');
    }
    ?>
</div>

<?php


function ListaPodstron($conn)
{
    echo 'Niżej wyświetlam wszystkie podstrony z bazy';
    $query = 'SELECT * FROM page_list LIMIT 10';
    $result = mysqli_query($conn, $query);
    $calc = 1;
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="item nr' . $calc . '"> ' . $row['id'] . '  -->  ' . $row['page_title'] . '
<form action="podstrony.php" method="get">
<input type="hidden" name="page_id" value="' . $row['id'] . '">
<input type="submit" name="edytuj" value="Edytuj" >
<input type="submit" name="usun" value="Usuń" >
</form></div>';
        $calc++;
    }
}

// przsyłam jako argumnet do zmiany wartości
function EdytujPodstrone($conn)
{
    $id = $_GET['page_id'];
    $query = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $content = '<form action="podstrony.php" method="post">
    <h2>Edytujesz stronę ' . $row['page_title'] . '</h2>
    <input type="hidden" name="page_id" value=' . $row['id'] . '
    <div class="box">
        podaj Tytuł
        <input type="text" name="tytul" placeholder="tytul" value="' . $row['page_title'] . '">
    </div>
    <div class="box">
        Podaj treść strony
        <textarea name="tresc" placeholder="tresc strony">
    ' . html_entity_decode($row['page_content'], ENT_HTML5 | ENT_QUOTES | ENT_SUBSTITUTE) . '
        </textarea>
    </div>
    <div class="box">
        Czy strona ma być aktywna
        <input type="checkbox" name="aktywna"> TAK
    </div>
    <input type="submit" name="zmiana" value="zapisz">
</form>';
    echo $content;
}

// Dodanie podstrony

function DodajPodstrone($conn)
{
    $query = "SELECT * FROM page_list";
    mysqli_query($conn, $query);
    $text = 'text';
    $content = '<div>
    <form action="podstrony.php" method="post"> 
    <input type="text" name="tytul" placeholder="Tytuł">
    <br>
    <textarea name="tresc" value=' . $text . ' placeholder="Konktekst Strony"></textarea>
    <input type="submit" name="zapisz">
    </form>
    </div>';
    echo $content;
}
?>