<?php require_once("Database.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF8">
        <title>Список аудиторий</title>
    </head>
    <body>
    <?php
        global $database;
        $res = $database->query("SELECT * FROM auditoriums");
    ?>

    <ul>
        <?php
            while($row = $res->fetch_assoc()){
                echo "<li>";
                echo $row["AuditoriumId"]. " : " . $row["Name"];
                echo "</li>";
            }
        ?>
    </ul>
    </body>
</html>