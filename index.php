<?php
$testList = require(__DIR__ . '/list.php');
?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <title>Тесты</title>
</head>
    <body>
        <h2><?= empty($testList) ? 'К сожалению вы не загрузили не один тест =(' : 'Выберите тест'?></h2>
        <?php if (empty($testList)): ?>
            <a href="admin.php">Загрузить тест</a>
        <?php else:?>
            <ul>
            <?php foreach ($testList as $file => $name):?>
                <li><a href="<?='test.php?test=' . $file?>"><?=$name?></a></li>
        <?php endforeach;?>
            </ul>
        <?php endif;?>
        <a href="admin.php">Добавить еще один тест</a>
    </body>
</html>