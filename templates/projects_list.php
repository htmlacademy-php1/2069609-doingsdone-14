<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>
    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <!-- Добавляем проекты в таблицу -->
            <?php
            foreach ($projects as $project): ?>
                <li class="main-navigation__list-item"
                <!-- Выделяем текущий проект с помощью доп. класса -->
                <a class="main-navigation__list-item-link
                    <?php
                if ($project['id'] === $current_project_id) {
                    echo ' main-navigation__list-item--active';
                } ?>"
                   href="index.php?project_id=<?= $project['id'] ?>&show_completed=<?= $show_complete_tasks; ?>
                   &deadline=<?= $current_deadline; ?> ">
                    <?= htmlspecialchars($project['name']) ?>
                </a>
                <!-- Выводим количество проектов с помощью функции count_of_tasks -->
                <span class="main-navigation__list-item-count">
                        <?= count_of_tasks($tasks_for_counting, $project['id']) ?>
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
