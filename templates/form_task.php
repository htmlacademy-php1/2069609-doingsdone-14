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
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="" method="POST" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <?php
            $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
            <input class="form__input <?= $classname; ?>" type="text" name="name" id="name"
                   value="<?= filter_input(INPUT_POST, 'name'); ?>" placeholder="Введите название">
            <?php
            if (isset($errors['name'])): ?>
                <p class="form__message"><?= $errors['name']; ?></p>
            <?php
            endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="project_id">Проект <sup>*</sup></label>
            <?php
            $classname = isset($errors['project_id']) ? "form__input--error" : ""; ?>

            <select class="form__input form__input--select <?= $classname; ?>" name="project_id" id="project_id">
                <?php
                foreach ($projects as $project): ?>
                    <option value="<?= $project['id'] ?>"
                        <?php
                        if ($project['id'] === (int)filter_input(INPUT_POST, 'project_id')): ?>
                            selected
                        <?php
                        endif; ?>><?= $project['name'] ?>
                    </option>
                <?php
                endforeach ?>
            </select>
            <?php
            if (isset($errors['project_id'])): ?>
                <p class="form__message"><?= $errors['project_id']; ?></p>
            <?php
            endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>
            <?php
            $classname = isset($errors['due_date']) ? "form__input--error" : ""; ?>
            <input class="form__input form__input--date <?= $classname; ?>" type="text" name="due_date"
                   id="date" value="<?= filter_input(INPUT_POST, 'due_date'); ?>"
                   placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php
            if (isset($errors['due_date'])): ?>
                <p class="form__message"><?= $errors['due_date']; ?></p>
            <?php
            endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>
            <?php
            $classname = isset($errors['file']) ? "form__input--error" : ""; ?>
            <div class="form__input-file">
                <input class="visually-hidden <?= $classname; ?>" type="file" name="file" id="file"
                       value="<?= filter_input(INPUT_POST, 'link_to_file'); ?>">
                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
                <?php
                if (isset($errors['file'])): ?>
                    <p class="form__message"><?= $errors['file']; ?></p>
                <?php
                endif; ?>
            </div>

        </div>

        <?php
        if (isset($errors)): ?>
            <div class="form__message">
                <p>Пожалуйста, исправьте ошибки в форме</p>
            </div>
        <?php
        endif; ?>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
