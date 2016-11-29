<?
    $filter = $_GET["filter"];
?>
<!DOCTYPE html>
<html>
<head lang="ru">
    <meta charset="UTF-8">
    <title>Заметки о расписании</title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../upload/bootstrap/css/bootstrap.min.css">
    <script src="../upload/bootstrap/js/bootstrap.min.js"></script>

    <link href="notes.css" type="text/css" rel="stylesheet" />

    <script src="notes.js"></script>
</head>
<body>
    <div id="container">
        <header class="cf">
            <img src="../upload/images/DVZ.png" id="headerLogo" width="150" height="150">
            <h1>
                Диспетчерская учебного отдела СГОАН<br />
                Заметки о расписании
            </h1>
        </header>

        <section id="content" class="cf">
            <div id="filterDiv">
                Фильтр:
                <input type="text" name="filter" id="filter" value="<?php echo (isset($filter))?$filter:'';?>">
                <button id="go" class="btn btn-default" type="button">GO</button>
            </div>

            <div id="notes">
                <? include_once 'notes.php' ?>
            </div>
        </section>

        <footer>
            <p>
                &copy; Диспетчерская учебного отдела СГОАН, <?php echo date("Y"); ?>
            </p>
        </footer>
    </div>
</body>
</html>