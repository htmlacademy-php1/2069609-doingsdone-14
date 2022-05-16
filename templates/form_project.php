<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php
            foreach ($projects as $project): ?>
                <li class="main-navigation__list-item">
                    <a class="main-navigation__list-item-link"
                       href="index.php?project_id=<?= $project['id'] ?>"><?= $project['name'] ?></a>
                    <span class="main-navigation__list-item-count"><?= count_of_tasks($tasks, $project['id']) ?></span>
                </li>
            <?php
            endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="add_project.php">Добавить проект</a>
</section>

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
