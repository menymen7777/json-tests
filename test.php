<?php
if(isset($_GET['test'])) {
   $test = $_GET['test'];
} else {
    $test = '';
}

/** Если нет get-параметра или такого теста то ебаошим 404  */
if(empty($test) || !file_get_contents('tests/' . $test . '.json')) {
    echo '404 Not Found';
    die();
}

$testBody = json_decode(file_get_contents('tests/' . $test . '.json'));
/** Получаем массив вопросов относительно стуктуры файла */
if(isset($testBody->questions) && isset($testBody)) {
    $questions =$testBody->questions;
} else {
    $questions = $testBody;
}

$testList = require(__DIR__ . "/list.php");
$testName = $testList[$test];
?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <title>Тест <?=$testName?></title>
</head>
    <body>
        <a href="index.php">Вернуться к списку</a>
        <h2>Тест "<?=$testName?>"</h2>
        <form action="result.php" method="POST">
            <input name="test-name" value="<?=$test?>" hidden>
            <?php foreach ($questions as $key => $question): ?>
                <div>
                    <label>Воспрос "<?=$key?>": <?=$question->question?></label><br>
                    <?php foreach ($question->answers as $answerKey => $answer):?>
                        <input type="radio" name="<?=$key?>" value="<?=$answerKey?>"><?=$answer?><br>
                    <?php endforeach;?>
                </div>
            <?php endforeach; ?>
            <p>
                <input type="submit">
            </p>
        </form>
    </body>
</html>