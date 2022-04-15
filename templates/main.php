<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>
    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <!-- Добавляем название задач в таблицу-->
            <?php foreach ($projects as $project): ?>
                <li class="main-navigation__list-item">
                    <!-- Выделяем текущий проект с помощью доп. класса main-navigation__list-item--active -->
                    <a class="main-navigation__list-item-link
                        <?php if ($project['id']===$current_pr_id):
                        echo ' main-navigation__list-item--active';
                        endif; ?>"
                       href="/index.php?pr_id=<?=$project['id'] ?>"><?=htmlspecialchars($project['name']) ?></a>
                    <!-- Выводим количество с помощью функции-->
                    <span class="main-navigation__list-item-count"><?=count_of_tasks($tasks, $project['id']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="post" autocomplete="off">
        <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">
        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">
            <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden show_completed" type="checkbox"<?php if ($show_complete_tasks===1): ?> checked<?php endif; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <!--Добавляем каждой задаче новую строчку с названием, категорией и датой-->
        <?php foreach ($tasks as $task):
            // Проверяем, задан ли странице параметр запроса pr_id
            if ($current_project_id):
                if ($task['project_id']!==$current_project_id):
                    continue;
                endif;
            endif;
            // если задача выполнена и $show_complete_tasks=0, пропускаем итерацию и не выводим задачу
            if ($task['task_status']===true and $show_complete_tasks===0):
                continue;
            endif; ?>
            <tr class="tasks__item task">
            <!--Если задача выполнена, добавляем строчке класс task--completed-->
            <?php if ($task['task_status']===true): ?>
                <tr class="tasks__item task<?=' task--completed' ?>">
            <?php endif; ?>
            <!--Если до выполенений задачи осталось менее 24 часов, строке добавляем класс task--important -->
            <?php if (is_task_important($task['task_date'])): ?>
                <tr class="tasks__item task<?=' task--important' ?>">
            <?php endif; ?>

            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                    <span class="checkbox__text"><?=htmlspecialchars($task['task_name']); ?></span>
                </label>
            </td>
            <!-- Вывод подгружаемого файла в таблицу -->
            <td class="task__file">
                <a class="download-link" href="#">Home.psd</a>
            </td>
            <!-- Вывод категории в таблицу -->
            <td>
                <span><?=htmlspecialchars($task['project_name']); ?></span>
            </td>
            <!-- Вывод даты в таблицу -->
            <td class="task__date">
                <span><?=htmlspecialchars($task['task_date']); ?></span>
            </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
