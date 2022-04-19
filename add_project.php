<?php
//пока просто скрипт для показа проектов, без связи с базой
require_once ('helpers.php');
$content = include_template('form-project.php', [
    'a' => 'Входящие']);
$layout_content = include_template('layout.php',[
    'content' => $content,
    'title'=> 'Дела в порядке']);
print($layout_content);


