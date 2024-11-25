<?php
$successful_message = '';
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    //сбор данных из формы
    $name_polya = ['text', 'email', 'number', 'select', 'radio', 'password'];
    foreach ($name_polya as $pole) 
    {
        $data[$pole] = $_POST[$pole] ?? '';
    }
    $data['checkbox'] = isset($_POST['checkbox']) ? 'checked' : 'unchecked';

    //валидация формы
    $validation_pole = 
    [
        'text' => 'Текстовое поле обязательно для заполнения.',
        'email' => 'Некорректный email.',
        'number' => 'Поле должно содержать только числа.',
        'select' => 'Выберите значение из списка.',
        'radio' => 'Выберите один из вариантов.',
        'password' => 'Пароль обязателен.'
    ];

    //проверка обязательных полей
    foreach ($validation_pole as $pole => $errorMessage) 
    {
        if ($pole === 'email' && !filter_var($data[$pole], FILTER_VALIDATE_EMAIL)) 
        {
            $errors[$pole] = $errorMessage;
        } elseif ($pole === 'number' && !is_numeric($data[$pole])) 
        {
            $errors[$pole] = $errorMessage;
        } elseif (empty($data[$pole])) 
        {
            $errors[$pole] = $errorMessage;
        }
    }
   //запись в файл
    if (empty($errors)) 
    {
        $DB = 
        [
            'date' => date('Y-m-d H:i:s'),
            'form_data' => $data
        ];
        file_put_contents('db.json', json_encode($DB, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
        $successful_message = 'Форма успешно отправлена и данные записаны в файл "db.json"';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles_index.css">
    <title>Форма</title>
</head>
<body>

    <form method="post" action=""> 
         <?php if ($successful_message): ?>
        <p style="color: green;"><?= $successful_message; ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $errmsg): ?>
                <ul><?= $errmsg; ?></ul>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
         <h2>Форма</h2>
        <label for="text">Имя</label>
        <input type="text" name="text" value="<?= htmlspecialchars($data['text'] ?? ''); ?>"><br>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? ''); ?>"><br>

        <label for="number">Количество домашних животных</label>
        <input type="number" name="number" value="<?= htmlspecialchars($data['number'] ?? ''); ?>"><br>

        <label for="select">Выберите</label>
        <select id="select" name="select">
            <option value="котик" <?= ($data['select'] === 'option1') ? 'selected' : ''; ?>>котик</option>
            <option value="собачка" <?= ($data['select'] === 'option2') ? 'selected' : ''; ?>>собачка</option>
        </select><br>

        <label>Выберите пол</label><br>
        <input type="radio" name="radio" value="женский" <?= ($data['radio'] === 'option1') ? 'checked' : ''; ?>> Женский<br>
        <input type="radio" name="radio" value="мужской" <?= ($data['radio'] === 'option2') ? 'checked' : ''; ?>> Мужской<br>

        <label>
            <input type="checkbox" name="checkbox" <?= ($data['checkbox'] === 'checked') ? 'checked' : ''; ?>> Согласен с обработкой персональных данных
        </label><br>

        <label for="password">Пароль</label>
        <input type="password" name="password"><br>

        <button type="submit">Отправить</button>
    </form>
   
</body>
</html>