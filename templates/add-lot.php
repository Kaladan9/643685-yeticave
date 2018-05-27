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

  <form class="form form--add-lot container <?=$classname; ?>" action="add.php" method="post" enctype="multipart/form-data">
    <div class="form__container-two">

      <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
      $value = isset($lot['name']) ? htmlspecialchars($lot['name']) : ""; ?>

      <div class="form__item <?=$classname; ?>">
        <label for="lot-name">Наименование</label>
        <input id="lot-name" type="text" name="lot[name]" placeholder="Введите наименование лота" value="<?=$value;?>">
        <span class="form__error"><?=$dict['name']; ?> : <?=$errors['name']; ?></span>
      </div>

      <?php $classname = isset($errors['category']) ? "form__item--invalid" : "";?>

      <div class="form__item <?=$classname; ?>">
        <label for="category">Категория</label>
        <select id="category" name="lot[category]">
          <option value="">Выберите категорию</option>
          <?php foreach ($product_categories as $cat): ?>
            <option value="<?=$cat['id']; ?>"
              <?php if ($cat['id'] === $lot['category']) {
            print('selected'); } ?> >
            <?=$cat['name']; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="form__error"><?=$dict['category']; ?> : <?=$errors['category']; ?></span>
      </div>
    </div>

    <?php $classname = isset($errors['description']) ? "form__item--invalid" : "";
    $value = isset($lot['description']) ? htmlspecialchars($lot['description']) : ""; ?>

    <div class="form__item form__item--wide <?=$classname; ?>">
      <label for="message">Описание</label>
      <textarea id="message" name="lot[description]" placeholder="Напишите описание лота"><?=$value;?></textarea>
      <span class="form__error"><?=$dict['description']; ?> : <?=$errors['description']; ?></span>
    </div>

    <?php $classname = isset($errors['file']) ? "" : "form__item--uploaded"; ?>

    <div class="form__item form__item--file <?=$classname; ?>"> <!-- form__item--uploaded -->
      <label>Изображение</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?=htmlspecialchars($lot['img_url']); ?>" width="113" height="113" alt="Изображение лота">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="lot_img" id="photo2">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
    </div>
    <div class="form__container-three">

      <?php $classname = isset($errors['primary_price']) ? "form__item--invalid" : "";
      $value = isset($lot['primary_price']) ? htmlspecialchars($lot['primary_price']) : ""; ?>

      <div class="form__item form__item--small <?=$classname; ?>">
        <label for="lot-rate">Начальная цена</label>
        <input id="lot-rate" type="number" name="lot[primary_price]'" placeholder="0" value="<?=$value;?>">
        <span class="form__error"><?=$dict['primary_price']; ?> : <?=$errors['primary_price']; ?></span>
      </div>

      <?php $classname = isset($errors['rate_step']) ? "form__item--invalid" : "";
      $value = isset($lot['rate_step']) ? htmlspecialchars($lot['rate_step']) : ""; ?>

      <div class="form__item form__item--small <?=$classname; ?>">
        <label for="lot-step">Шаг ставки</label>
        <input id="lot-step" type="number" name="lot[rate_step]" placeholder="0" value="<?=$value;?>">
        <span class="form__error"><?=$dict['rate_step']; ?> : <?=$errors['rate_step']; ?></span>
      </div>

      <?php $classname = isset($errors['end_date']) ? "form__item--invalid" : "";
      $value = isset($lot['end_date']) ? htmlspecialchars($lot['end_date']) : ""; ?>

      <div class="form__item <?=$classname; ?>">
        <label for="lot-date">Дата окончания торгов</label>
        <input class="form__input-date" id="lot-date" type="date" name="lot[end_date]" value="<?=$value;?>">
        <span class="form__error"><?=$dict['end_date']; ?> : <?=$errors['end_date']; ?></span>
      </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
  </form>
</main>
