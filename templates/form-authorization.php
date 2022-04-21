<div class="content">

  <section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="/authorization.php">Войти</a>
  </section>

  <main class="content__main">
    <h2 class="content__main-heading">Вход на сайт</h2>

    <form class="form" action="" method="post" autocomplete="off">
      <div class="form__row">
          <?php $classname = isset($errors['email']) ? "form__input--error" : "";
          $value = isset($form['email']) ? $form['email'] : ""; ?>
        <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <input class="form__input <?=$classname;?>" type="text" name="email" id="email" value="<?=$value;?>" placeholder="Введите e-mail">
          <?php if ($classname): ?>
              <div class="error-notice">
                  <span class="error-notice__icon"></span>
                  <span class="error-notice__tooltip"><?=$errors['email'];?></span>
              </div>
          <?php endif; ?>
      </div>

      <div class="form__row">
          <?php $classname = isset($errors['password']) ? "form__input--error" : "";
          $value = isset($form['password']) ? $form['password'] : ""; ?>
        <label class="form__label" for="password">Пароль <sup>*</sup></label>
        <input class="form__input" type="password" name="password" id="password" value="" placeholder="Введите пароль">
          <?php if ($classname): ?>
              <div class="error-notice">
                  <span class="error-notice__icon"></span>
                  <span class="error-notice__tooltip"><?=$errors['password'];?></span>
              </div>
          <?php endif; ?>
      </div>

      <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Войти">
      </div>
    </form>

  </main>

</div>
