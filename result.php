<?php
$post = $_POST;
if(empty($post)) {
    header('Location: index.php');
} else {
    /** Получаем файл теста по его ключу */
    $test = json_decode(file_get_contents('tests/' . $post['test-name'] . '.json'));
    $testList = require(__DIR__ . '/list.php');
    $testName = $testList[$post['test-name']];
    /** Получаем массив вопросов относительно стуктуры файла */
    if(isset($test->questions) && isset($test)) {
        $questions = $test->questions;
    } else {
        $questions = $test;
    }

    /** @var array $errors Массив ошибок */
    $errors = [];
    $rights = 0;
    $total = 0;
    /** Перебираем все вопросы и сравниваем с тем что прилетело постом */
    foreach ($questions as $key => $question) {
        $total++;
        if(!isset($post[$key])) {
            $errors []= 'Вы не ответили на вопрос "' . $key . '": ' . $question->question;
            continue;
        }

        if($question->rightAnswer == $post[$key]) {
            $rights++;
        } else {
            $errors []= 'Неверный ответ на вопрос "' . $key . '": ' . $question->question;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <title>Результат теста</title>
</head>
<body>
    <h2>Результат теста "<?=$testName?>"</h2>
    <?php if(empty($errors)):?>
        <div>Поздравляем!!! Вы ответили на все вопросы абсолютно верно!</div>
        <a href="index.php">Вернуться на главную</a>
    <?php else:?>
        <div>Вы ответили правильно на <?=$rights?> вопросов из <?=$total?></div>
        <div>
            Ошибки:
            <ul>
                <?php foreach ($errors as $error):?>
                    <li><?=$error?></li>
                <?php endforeach;?>
            </ul>
        </div>
        <p>
            <a href="index.php">Вернуться на главную</a>
        </p>
        <p>
            <a href="test.php?test=<?=$post['test-name']?>">Вернуться к тесту</a>
        </p>
    <?php endif;?>
</body>
</html>
