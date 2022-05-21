<?php

$projects_list = include_template('projects_list.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
    'show_complete_tasks' => $show_complete_tasks,
    'current_deadline' => $current_deadline,
    'tasks_for_counting' => $tasks_for_counting
]);
print ($projects_list) ?>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>
            <?php
            $classname = isset($error) ? "form__input--error" : ""; ?>
            <input class="form__input <?= $classname; ?>" type="text" name="name" id="project_name"
                   value="<?= filter_input(INPUT_POST, 'name'); ?>" placeholder="Введите название проекта">
            <?php
            if (isset($error)): ?>
                <p class="form__message"><?= $error; ?></p>
            <?php
            endif; ?>
        </div>

        <?php
        if (isset($error)): ?>
            <div class="form__message">
                <p>Пожалуйста, исправьте ошибку в форме</p>
            </div>
        <?php
        endif; ?>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
