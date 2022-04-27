<?php
if (isset($_SESSION['user'])) {
    $is_auth = 1;
    $current_user_name = $_SESSION['user']['name'];
}
else {
    $is_auth = 0;
    $current_user_name = '';
}
