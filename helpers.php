<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Проверяет, осталось ли от текущего момента до переданной даты менее 24 часов
 *
 * Примеры использования для текущей даты 2022-05-12:
 * is_task_important('2022-05-12'); // true
 * is_task_important('2022-05-13'); // true
 * is_task_important('2022-05-11'); // true
 * is_task_important('2020-01-11'); // true
 * is_task_important('2022-05-14'); // false
 *
 * @param string $task_date Дата в виде строки
 *
 * @return bool true если до переданной даты осталось менее 24 часов, иначе false
 */
function is_task_important($task_date): bool
{
    if ($task_date) {
        $current_time = time();
        return (strtotime($task_date) - $current_time < SECONDS_IN_DAY);
    }
    return false;
}

/**
 * Регистронезависимая проверка присутствия переданного значения в переданном массиве
 *
 * @param string $name Значение в виде строки
 * @param array $allowed_list Переданный с писок в виде массива
 *
 * @return bool true если переданное значение проекта присутствует в переданном массиве, иначе false
 */
function validate_project($name, $allowed_list): bool
{
    $mb_name = mb_strtolower($name);
    $length = count($allowed_list);
    for ($i = 0; $i < $length; $i++) {
        $allowed_list[$i] = mb_strtolower($allowed_list[$i]);
    }
    return (in_array($mb_name, $allowed_list));
}

/**
 * Считает колество задач, у которых значение проекта совпадает с заданным значением проекта
 *
 * @param array $array_of_task Задачи в виде массива
 * @param int $id_of_category Значение id текущего проекта в виде числа
 *
 * @return int Количество задач в текущем проекте
 */
function count_of_tasks($array_of_task, $id_of_category)
{
    $count_of_task = 0;
    foreach ($array_of_task as $task) {
        if ($task['project_id'] === $id_of_category) {
            $count_of_task++;
        }
    }
    return $count_of_task;
}

/**
 * Проверяет, больше или равна ли переданная дата текущей
 *
 * Примеры использования для текущей даты 2022-05-12:
 * is_date_greater_than_today('2022-05-12'); // true
 * is_date_greater_than_today('2022-05-13'); // true
 * is_date_greater_than_today('2022-05-11'); // false
 * is_date_greater_than_today('2020-01-11'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true если до переданная дата больше или равна текущей, иначе false
 */
function is_date_greater_than_today($date): bool
{
    $current_time = time();
    return strtotime($date) + SECONDS_IN_DAY > $current_time;
}

/**
 * Проверяет, меньше ли количество символов переданного значения, чем переданное максимально допустимое значение
 *
 * Примеры использования:
 * is_validate_length('Проект', 10); // true
 * is_validate_length('Проект', 6); // true
 * is_validate_length('Проект', 3); // false
 *
 * @param string $value Значение в виде строки
 * @param int $max Значение максимально допустимого значения в виде числа
 *
 * @return bool true если количество символов переданного значение меньше максимально допустимого значения, иначе false
 */
function is_validate_length($value, $max): bool
{
    $len = mb_strlen($value);
    return ($len <= $max);
}

/**
 * Определяет значение переменной error_no_tasks
 *
 * @param int $current_project_id Значение id текущего проекта в виде числа
 * @param array $tasks Выводимые (в зависимости от выбранного проекта/дедлайна) задачи в виде массива
 * @param string $current_project_id_without_filter Значение id текущего проекта, без фильтра FILTER_SANITIZE_NUMBER_INT
 * @param array $user_tasks Все задачи пользователя в виде массива
 * @param array $user_project_ids Значения всех id проектов пользователя в виде массива
 *
 * @return string если количество выводимых задач равно 0, возвращаем 'Нет задач', иначе пустую строку ''
 */
function what_is_error_no_tasks(
    $current_project_id,
    $tasks,
    $current_project_id_without_filter,
    $user_tasks,
    $user_project_ids
): string {
    $error_no_tasks = '';
    if ($current_project_id === 0 and count($tasks) === 0) {
        $error_no_tasks = 'Нет задач';
    }
    if (!$current_project_id_without_filter and count($user_tasks) === 0) {
        $error_no_tasks = 'Нет задач';
    }
    if ($current_project_id_without_filter) {
        if (in_array($current_project_id, $user_project_ids)) {
            if (count_of_tasks($tasks, $current_project_id) === 0) {
                $error_no_tasks = 'Нет задач';
            }
        } else {
            $error_no_tasks = 'Вы обращаетесь к несуществующему проекту';
        }
    }
    return $error_no_tasks;
}
