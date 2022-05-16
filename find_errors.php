<?php

if (in_array($key, $required) && empty($value)) {
    $errors[$key] = "Поле $key надо заполнить";
} elseif (isset($rules[$key])) {
    $rule = $rules[$key];
    $errors[$key] = $rule($value);
}
