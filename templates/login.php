<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($product_categories as $cat): ?>
        <li class="nav__item">
          <a href="all-lots.html"><?=$cat['name']; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <?php $classname = (count($errors)) ? "form--invalid" : ""; ?>

  <form class="form container <?=$classname; ?>" action="login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>

    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($login['email']) ? htmlspecialchars($login['email']) : ""; ?>

    <div class="form__item <?=$classname; ?>"> <!-- form__item--invalid -->
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="login[email]" placeholder="Введите e-mail" value="<?=$value;?>">
      <span class="form__error"><?=$dict['email']; ?> : <?=$errors['email']; ?></span>
    </div>

<?php $classname = isset($errors['pass']) ? "form__item--invalid" : ""; ?>

    <div class="form__item form__item--last <?=$classname; ?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password" name="login[pass]" placeholder="Введите пароль" >
      <span class="form__error"><?=$dict['pass']; ?> : <?=$errors['pass']; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
  </form>
</main>
