<?php
$SpecialCase = $_GET["SpecialCase"];

switch ($SpecialCase) {
    case "Карина":
        $Name = "Evstifeeva Karina";
        $img = 'https://pp.vk.me/c425430/v425430399/1889/OXIpWDUzBUA.jpg';
        break;
    case "":
        $Name = "Uzhas letyashchiy na kryl'yakh nochi";
        $img = 'http://psycompetence.net/wp-content/uploads/2013/05/str.jpg';
        break;
    default:
        $Name = $SpecialCase;
        $img = 'http://wolgodacha.ru/wp-content/uploads/2011/02/redis.jpg';
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Диплом</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script>
        $(function() {
            $( "#generate").click(function() {

                var Name = $('#Name').val();

                var img = $('#img').val();

                var path = "http://wiki.nayanova.edu/TankGenerate2.php?Name=" + Name + "&img=" + img;

                $('#dimplomaImage').html('<img src="' + path + '" />');
            });

            $('#generate').trigger("click");
        });
    </script>
</head>
<body>
<table style="width: 100%">
    <tr>
        <td>
            <div id="dimplomaImage" style="width: 972px"></div>
        </td>
        <td style="width: 99%">
            <table style="width: 100%" border="1">
                <tr>
                    <td>
                        Имя
                    </td>
                    <td>
                        <input type="text" id="Name" value="<?php echo $Name; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        URL картинки
                    </td>
                    <td>
                        <input type="text" id="img" value="<?php echo $img; ?>">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <button id="generate" style="width: 100%">Поехали</button>
        </td>
    </tr>
</table>
</body>
</html>