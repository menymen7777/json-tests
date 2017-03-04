<?php 
if(isset($_FILES['test-file'])) {
    try {
        $uploadTempFile = '/home/meny/Stas/tests/' . basename($_FILES['test-file']['name']);
        /** Проверяем тип файла */
        if ($_FILES['test-file']['type'] != 'application/json') {
            throw new Exception("Неверный формат файла\n");
        }

        /** Проверяем что файла еще нет */
        if (file_exists($uploadTempFile)) {
            throw new Exception("Файл с тестом уже существует. Выберите другой тест или измените название файла\n");
        }

        /** Проверям файл со списком */
        if(!file_exists('list.php')) {
            throw new Exception("Файл list.php не существует\n");
        }

        /** Проверяем сожержимое json */
        $json = json_decode(file_get_contents($_FILES['test-file']['tmp_name']));
        checkJson($json);
        if(isset($json->name)) {
            $questionName = $json->name;
        } else {
            $questionName = str_replace('.json', '', basename($_FILES['test-file']['name']));
        }
        $listFile = file_get_contents('list.php');
        $separatedList = explode('[', $listFile);
        $newList = $separatedList[0] . "[\n\t'" . str_replace('.json', '', basename($_FILES['test-file']['name'])) . "' => '" . $questionName . "'," . $separatedList[1];
        if (!file_put_contents('list.php', $newList)) {
            throw new Exception('Не удалось добавить тест в список');
        }

        if (!move_uploaded_file($_FILES['test-file']['tmp_name'], $uploadTempFile)) {
            throw new Exception("Не удалось загрузить файл на сервер\n");
        }

        echo "Тест успешно загружен";
    } catch (Exception $e) {
        echo $e->getMessage();
    }

}

/**
 * Проверка валилности json
 * @param $json
 * @throws Exception
 */
function checkJson($json)
{
    /** Есди не правильная структура json */
    if(is_null($json)) {
        throw new Exception('Неккоректный формат json-файла');
    }

    /** Получаем массив вопрос относительно формата листа */
    if(isset($json->name) && isset($json->questions)) {
        $questions = $json->questions;
    } else {
        $questions = $json;
    }

    /** Перебираем все вопросы и смотрим содержимое */
    foreach ($questions as $key => $value) {
        /** Не существует или пустой вопрос */
        if(!isset($value->question) || empty($value->question)) {
            throw new Exception('В блоке "' . $key . '" отсутсвует вопрос');
        }

        /** Не существуют ответы */
        if(!isset($value->answers)) {
            throw new Exception('В блоке "' . $key . '" отсутсвуют ответы');
        }

        /** Не существует или пустой правильный ответ*/
        if(!isset($value->rightAnswer) || empty($value->rightAnswer) || strlen($value->rightAnswer) != 1) {
            throw new Exception('В блоке "' . $key . '" отсутсвует правильный ответ или он некорректен');
        }

        /** Проверяем существует ли правльынй ответ */
        $checker = false;
        foreach ($value->answers as $answerKey => $answer) {
            if($answerKey == $value->rightAnswer) {
                $checker = true;
                break;
            }
        }

        if(!$checker) {
            throw new Exception('В блоке "' . $key . '" нет ответа, совпадающего по ключу с правильным');
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="UTF-8">
        <title>Загрузка теста</title>
    </head>
    <body>
        <h2>Выберите файл с тестом</h2>
        <form id="upload-test" enctype="multipart/form-data" method="POST">
            <input name="test-file" type="file">
            <p>
                <input type="submit">
            </p>
        </form>
        <a href="index.php">Перейти к списку тестов</a>
    </body>
</html>