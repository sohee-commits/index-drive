<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

  <?php require_once './el-head.php'; ?>

  <body>
    <?php require_once './el-header.php'; ?>

    <main>
      <h2 class="fw-m">Бронирование автомобиля</h2>
      <section class="booking-form">
        <form action="./scripts/insCarData.php" method="post">
          <h3 class="fw-m">Данные автомобиля</h3>
          <div class="conc">
            <div class="group">
              <p role="label">Идентификатор автомобиля</p>
              <input required name="car_id" id="car_id" role="text" placeholder="-">
            </div>
            <div class="group">
              <p role="label">Номер машины</p>
              <p id="car_number">-</p>
            </div>
          </div>
          <div class="group">
            <p role="label">Стоимость бронирования за день</p>
            <input required name="car_price" id="car_price" role="text" placeholder="-">
          </div>
          <!-- марка и модель -->
          <div class="conc">
            <div class="group">
              <label for="mark" role="label">Марка</label>
              <select required name="mark" id="mark">
                <option value="null" selected>Выбрать</option>
                <?php
                require_once './_config.php';
                $sql = "SELECT DISTINCT mark FROM cars";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . $row['mark'] . '">' . $row['mark'] . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="group">
              <label for="model" role="label">Модель</label>
              <select required name="model" id="model">
                <!-- динамическая генерация моделей на основе выбранной марки -->
              </select>
            </div>
          </div>
          <!-- даты -->
          <div class="conc">
            <div class="group">
              <label for="date_start" role="label">Дата бронирования</label>
              <input required type="date" id="date_start" name="date_start">
            </div>
            <div class="group">
              <label for="date_end" role="label">Дата возврата</label>
              <input required type="date" id="date_end" name="date_end">
            </div>
          </div>
          <div class="group">
            <label for="branch" role="label">Филиал</label>
            <select required name="branch" id="branch">
              <!-- динамическая генерация филиалов -->
            </select>
          </div>
          <div class="img-sales" title="Скидки на долгосрочную аренду до 25% * за каждые 3 дня +5%"></div>
          <h3 class="fw-m">Ваши Данные</h3>
          <?php
          require_once './_config.php';

          $user_id = $_SESSION['user_id']; // Получение идентификатора текущего пользователя из сессии
          $sql = "SELECT * FROM users WHERE _user_id = ?";
          $stmt = $conn->prepare($sql); // Подготовка запроса
          $stmt->bind_param("i", $user_id); // Привязка параметра (идентификатор пользователя)
          $stmt->execute(); // Выполнение запроса
          $result = $stmt->get_result(); // Получение результата запроса
          
          if ($row = $result->fetch_assoc()) {
            // Вывод информации о пользователе, если данные получены
            echo '
            <div class="conc">
                <div class="group">
                    <p role="label">Номер телефона</p>
                    <p>' . $row['tel'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Имя</p>
                    <p>' . $row['first_name'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Фамилия</p>
                    <p>' . $row['last_name'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Отчество</p>
                    <p>' . $row['patronymic'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Дата рождения</p>
                    <p>' . $row['birth'] . '</p>
                </div>
            </div>
            <div class="conc">
                <div class="group">
                    <p role="label">Серия</p>
                    <p>' . $row['pass_series'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Номер</p>
                    <p>' . $row['pass_number'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Дата выдачи</p>
                    <p>' . $row['pass_date'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Наименование выдавшего органа</p>
                    <p>' . $row['pass_authority'] . '</p>
                </div>
                <div class="group">
                    <p role="label">Код подразделения</p>
                    <p>' . $row['pass_code'] . '</p>
                </div>
            </div>';
          }
          ?>
          <div class="card">
            <div class="bank-card">
              <h3 class="fw-m">Банковская карта</h3>
              <div class="conc">
                <div class="group">
                  <p role="label">Тип</p>
                  <p id="card_type">-</p>
                </div>
                <div class="group">
                  <p role="label">Статус</p>
                  <p id="card_status">-</p>
                </div>
              </div>
              <div class="group">
                <label for="cards" role="label">Номер</label>
                <select required name="cards" id="cards">
                  <?php
                  // Запрос к базе данных для получения номеров банковских карт текущего пользователя
                  $stmt = $conn->prepare("SELECT number FROM cards WHERE _user_id = ?");
                  $stmt->bind_param("i", $user_id); // Привязка параметра (идентификатор пользователя)
                  $stmt->execute(); // Выполнение запроса
                  $result = $stmt->get_result(); // Получение результата запроса
                  
                  while ($row = $result->fetch_assoc()) {
                    // Вывод опций для выпадающего списка с номерами карт
                    echo '<option value="' . $row['number'] . '">' . $row['number'] . '</option>';
                  }

                  echo '<option value="null" selected>Выбрать</option>'; // Добавление стандартной опции "Выбрать"
                  ?>

                </select>
              </div>
            </div>
            <img loading='lazy' src="./assets/booking/payment-transparency.png"
              alt="Отмена аренды без штрафов Бесплатно * до 24 часов до старта аренды">
          </div>
          <button name="book" class="btn-primary" type="submit">Арендовать</button>
        </form>
      </section>
    </main>

    <?php require_once './el-footer.php'; ?>

    <script src="./scripts/getCarData.js"></script>
    <script src="./scripts/getCardInfo.js"></script>

  </body>

</html>