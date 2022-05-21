<div class="content">
    <section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="auth.php">Войти</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Регистрация аккаунта</h2>

        <form class="form" action="" method="post" autocomplete="off">
            <div class="form__row">
                <?php
                $classname = isset($errors['email']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="email">E-mail <sup>*</sup></label>
                <input class="form__input <?= $classname; ?>" type="text" name="email" id="email"
                       value="<?= filter_input(INPUT_POST, 'email'); ?>"
                       placeholder="Введите e-mail">
                <?php
                if (isset($errors['email'])): ?>
                    <p class="form__message"><?= $errors['email']; ?></p>
                <?php
                endif; ?>
            </div>

            <div class="form__row">
                <?php
                $classname = isset($errors['password']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="password">Пароль <sup>*</sup></label>
                <input class="form__input <?= $classname; ?>" type="password" name="password"
                       id="password" value="<?= filter_input(INPUT_POST, 'password'); ?>"
                       placeholder="Введите пароль">
                <?php
                if (isset($errors['password'])): ?>
                    <p class="form__message"><?= $errors['password']; ?></p>
                <?php
                endif; ?>
            </div>

            <div class="form__row">
                <?php
                $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="name">Имя <sup>*</sup></label>
                <input class="form__input <?= $classname; ?>" type="text" name="name" id="name"
                       value="<?= filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS); ?>"
                       placeholder="Введите имя">
                <?php
                if (isset($errors['name'])): ?>
                    <p class="form__message"><?= $errors['name']; ?></p>
                <?php
                endif; ?>
            </div>

            <div class="form__row form__row--controls">
                <?php
                if (count($errors) !== 0): ?>
                    <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
                <?php
                endif; ?>
            </div>

            <div>
                <input class="button" type="submit" name="" value="Зарегистрироваться">
            </div>
        </form>
    </main>
</div>
