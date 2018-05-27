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

  <form class="form container <?=$classname; ?>" action="reg.php" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>

    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($signup['email']) ? htmlspecialchars($signup['email']) : ""; ?>

    <div class="form__item <?=$classname; ?>">
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="signup[email]" placeholder="Введите e-mail" value="<?=$value;?>">
      <span class="form__error"><?=$dict['email']; ?> : <?=$errors['email']; ?></span>
    </div>

    <?php $classname = isset($errors['pass']) ? "form__item--invalid" : ""; ?>

    <div class="form__item <?=$classname; ?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password" name="signup[pass]" placeholder="Введите пароль" >
      <span class="form__error"><?=$dict['pass']; ?> : <?=$errors['pass']; ?></span>
    </div>

    <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
    $value = isset($signup['name']) ? htmlspecialchars($signup['name']) : ""; ?>

    <div class="form__item <?=$classname; ?>">
      <label for="name">Имя*</label>
      <input id="name" type="text" name="signup[name]" placeholder="Введите имя" value="<?=$value;?>">
      <span class="form__error"><?=$dict['name']; ?> : <?=$errors['name']; ?></span>
    </div>

    <?php $classname = isset($errors['contacts']) ? "form__item--invalid" : "";
    $value = isset($signup['contacts']) ? htmlspecialchars($signup['contacts']) : ""; ?>

    <div class="form__item <?=$classname; ?>">
      <label for="message">Контактные данные*</label>
      <textarea id="message" name="signup[contacts]" placeholder="Напишите как с вами связаться" ><?=$value;?></textarea>
      <span class="form__error"><?=$dict['contacts']; ?> : <?=$errors['contacts']; ?></span>
    </div>

    <?php $classname = isset($errors['file']) ? "" : "form__item--uploaded"; ?>

    <div class="form__item form__item--file form__item--last <?=$classname; ?>">
      <label>Аватар</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?=$signup['avatar']; ?>" width="113" height="113" alt="Ваш аватар">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="avatar" id="photo2">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
  </form>
</main>
