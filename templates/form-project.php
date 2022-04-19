    <div class="content">
      <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
          <ul class="main-navigation__list">
            <li class="main-navigation__list-item">
              <a class="main-navigation__list-item-link" href="#"><?=$a; ?></a>
              <span class="main-navigation__list-item-count">24</span>
            </li>

            <li class="main-navigation__list-item main-navigation__list-item--active">
              <a class="main-navigation__list-item-link" href="#">Работа</a>
              <span class="main-navigation__list-item-count">12</span>
            </li>

            <li class="main-navigation__list-item">
              <a class="main-navigation__list-item-link" href="#">Здоровье</a>
              <span class="main-navigation__list-item-count">3</span>
            </li>

            <li class="main-navigation__list-item">
              <a class="main-navigation__list-item-link" href="#">Домашние дела</a>
              <span class="main-navigation__list-item-count">7</span>
            </li>

            <li class="main-navigation__list-item">
              <a class="main-navigation__list-item-link" href="#">Авто</a>
              <span class="main-navigation__list-item-count">0</span>
            </li>
          </ul>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="/add_project.php">Добавить проект</a>
      </section>

      <main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form"  action="index.html" method="post" autocomplete="off">
          <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input" type="text" name="name" id="project_name" value="" placeholder="Введите название проекта">
          </div>

          <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
          </div>
        </form>
      </main>
    </div>
  </div>
</div>



