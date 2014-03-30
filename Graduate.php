<?php
$SpecialCase = $_GET["SpecialCase"];
if ($SpecialCase == "")
{
    $SpecialCase = "Иванову Ивану Ивановичу";
}
switch ($SpecialCase) {
    case "Оля":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Мукасеевой Ольге Николаевне";
        $Qualification = "Дипломных дел мастер";
    break;
    case "Вася":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Токареву Василию";
        $Qualification = "Магистр сравнительного тепловедения";
        break;
    case "Вася2":
        $Who = "Безуглов Сергей";
        $Whom = "Токареву Василию";
        $Qualification = "Пловец в стиле хлюп (с энтузиазмом)";
        break;
    case "Яна":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Яниной Яне";
        $Qualification = "Именинник и самый активный творец";
    break;
    case "Маша":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Торховой Марии";
        $Qualification = "Просто хороший человек";
        break;
    case "Лена":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Калининой Елене";
        $Qualification = "Оптимист и творческий человек";
        break;
    case "Вика":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Усыниной Виктории";
        $Qualification = "Знатный путешественник";
        break;
    case "Марта":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Платоновой Софье";
        $Qualification = "Экстремальный шеф-повар";
        break;
    case "Фрэнк":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Головко Полине";
        $Qualification = "Мистер Ватсон";
        break;
    case "Карина":
        $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
        $Whom = "Евстифеевой Карине";
        $Qualification = "Юрист-танцор (с отличием)";
        break;
default:
    $Who = "Его императорского величества\nуниверситетъ\nвсъего и обо всем";
    $Whom = $SpecialCase;
    $Qualification = "Магистр прикладных ко лбу наук";
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
                var entered = $('#Who').val();
                var lines = entered.split(/\n/);
                var Who = lines.join(';');

                var Whom = $('#Whom').val();
                var Qualification = $('#Qualification').val();

                var path = "Graduates.php?Who=" + Who + "&Whom=" + Whom + "&Qualification=" + Qualification;

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
            <div id="dimplomaImage" style="width: 520px"></div>
        </td>
        <td style="width: 99%">
            <table style="width: 100%" border="1">
                <tr>
                    <td>
                        Наименование учреждения<br />
                        (не&nbsp;более&nbsp;6&nbsp;строчек)
                    </td>
                    <td>
                        <textarea rows="6" cols="50" id="Who"><?php echo $Who; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Кому
                    </td>
                    <td>
                        <input type="text" id="Whom" value="<?php echo $Whom; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Квалификация
                    </td>
                    <td>
                        <input type="text" id="Qualification" value="<?php echo $Qualification; ?>">
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