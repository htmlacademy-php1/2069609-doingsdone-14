<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>
    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <!-- Добавляем название задач в таблицу -->
            <?php
            foreach ($projects as $project): ?>
                <li class="main-navigation__list-item">
                    <!-- Выделяем текущий проект с помощью доп. класса -->
                    <a class="main-navigation__list-item-link
                        <?php
                    if ($project['id'] === $current_project_id) {
                        echo ' main-navigation__list-item--active';
                    } ?>" href="index.php?project_id=<?= $project['id'] ?>">
                        <?= htmlspecialchars($project['name']) ?>
                    </a>
                    <!-- Выводим количество с помощью функции-->
                    <span class="main-navigation__list-item-count">
                        <?= count_of_tasks($tasks, $project['id']) ?>
                    </span>
                </li>
            <?php
            endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="add_project.php"
       target="project_add">Добавить проект
    </a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="GET" autocomplete="off">
        <input class="search-form__input" type="text" name="q"
               value="<?= filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?>"
               placeholder="Поиск по задачам">
        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <?php
            foreach ($tasks_section as $task_section): ?>
                <a href="index.php?deadline=<?= $task_section['deadline']; ?>" class="tasks-switch__item
                <?php
                if ($task_section['deadline'] === $current_deadline): echo ' tasks-switch__item--active'; endif; ?>">
                    <?= $task_section['name']; ?>
                </a>
            <?php
            endforeach; ?>
        </nav>

        <label class="checkbox">
            <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden show_completed"
                   type="checkbox" <?php
                   if ($show_complete_tasks === 1): ?>checked<?php
            endif; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <?php
        if (count($tasks) === 0) { ?>
            <tr>
                <p>
                    <?= 'Задачи отсутствуют'; ?>
                </p>
            </tr>
            <?php
        } else {
            //Добавляем каждой задаче новую строчку с названием, категорией и датой-->
            foreach ($tasks as $task) {
                // Проверяем, задан ли странице параметр запроса project_id
                if ($current_project_id) {
                    if ($task['project_id'] !== $current_project_id) {
                        continue;
                    }
                }
                // если задача выполнена и $show_complete_tasks=0, пропускаем итерацию и не выводим задачу
                if ($task['task_status'] === 1 and $show_complete_tasks === 0) {
                    continue;
                }

                $classname = '';
                if (is_task_important($task['task_date'])) {
                    $classname = ' task--important';
                }
                if ($task['task_status'] === 1) {
                    $classname = ' task--completed';
                }
                ?>

                <tr class="tasks__item task <?= $classname; ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                                   value="<?= $task['task_id']; ?>">
                            <span class="checkbox__text"><?= htmlspecialchars($task['task_name']); ?></span>
                        </label>
                    </td>

                    <!-- Вывод подгружаемого файла в таблицу -->
                    <td class="task__file">
                        <?php
                        if ($task['path']): ?>
                            <a class="download-link" href="uploads/<?= $task['path']; ?>"><?= htmlspecialchars(
                                    $task['path']
                                ); ?></a>
                        <?php
                        endif; ?>
                    </td>

                    <!-- Вывод категории в таблицу -->
                    <td>
                        <span><?= htmlspecialchars($task['project_name']); ?></span>
                    </td>

                    <!-- Вывод даты в таблицу -->
                    <td class="task__date">
                        <span><?php
                            if ($task['task_date']) {
                                echo htmlspecialchars($task['task_date']);
                            } ?></span>
                    </td>

                </tr>
                <?php
            }
        } ?>
    </table>
</main>
