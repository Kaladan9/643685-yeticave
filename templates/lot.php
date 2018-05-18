<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($product_categories as $cat): ?>
        <li class="nav__item">
          <a href="all-lots.html"><?=$cat; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <section class="lot-item container">

    <?php foreach ($lot_info as $key => $val): ?>
      <h2><?=htmlspecialchars($val['name']); ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=$val['product_img_url']; ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?=htmlspecialchars($val['category']); ?></span></p>
          <p class="lot-item__description"><?=htmlspecialchars($val['description']); ?></p>
        </div>
      <?php endforeach; ?>

      <div class="lot-item__right">
        <div class="lot-item__state">
          <div class="lot-item__timer timer">
            <?=lot_time_ending(); ?>
          </div>

          <?php foreach ($price_info as $key => $val): ?>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=htmlspecialchars(max($val['primary_price'], $val['max_bet'])); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=htmlspecialchars(max($val['primary_price'], $val['max_bet']) + $val['rate_step']); ?></span>
              </div>
            </div>
            <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post">
              <p class="lot-item__form-item">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="number" name="cost"
                placeholder= "<?=htmlspecialchars(max($val['primary_price'], $val['max_bet']) + $val['rate_step']); ?>">
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          <?php endforeach; ?>

        </div>
        <div class="history">
          <h3>История ставок (<span><?=count($bet_info) ?></span>)</h3>
          <table class="history__list">
            <?php foreach ($bet_info as $key => $val): ?>
              <tr class="history__item">
                <td class="history__name"><?=htmlspecialchars($val['name']); ?></td>
                <td class="history__price"><?=htmlspecialchars($val['amount']); ?></td>
                <td class="history__time"><?=htmlspecialchars($val['bet_date']); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>
