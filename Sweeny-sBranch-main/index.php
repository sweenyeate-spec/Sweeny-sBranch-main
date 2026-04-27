<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Oracle Of Shrimp</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
  >

  <link href="css/style.css" rel="stylesheet">
  <link href="css/themes.css" rel="stylesheet">

  <script src="scripts/app.js" defer></script>
  <script src="scripts/login.js" defer></script>
  <script src="scripts/signup.js" defer></script>
</head>

<!-- overlay for popup, makes popup window have priority -->
<body>
  <div id="overlay"></div>

  <hr>

  <div class="content-with-sidebars" style="justify-content: center; align-items: center; height: 100px;">
    <div style="width: auto;">
      <h1 class="title_jumbo abhaya-libre-regular">ORACLE OF SHRIMP</h1>
    </div>

    <div style="width: auto;">
      <a class="abhaya-libre-bold" href="pages/about.html">About</a>
    </div>
  </div>

  <hr>

  <div class="three-columns-grid">
    <!-- Signup Container -->
    <div>
      <!-- From Uiverse.io by mi-series -->
      <div id="signupContainer" class="container">
        <div class="form-area">
          <p class="title"> SIGN UP</p>
          <p id="signupMessage"></p>

          <form id="signupForm">
            <div class="form_group">
              <label class="sub_title" for="name">Name</label>
              <input
                placeholder="Enter your username"
                class="form-style"
                type="text"
                name="username"
              >
            </div>

            <div class="form_group">
              <label class="sub_title" for="email">Email</label>
              <input
                placeholder="Enter your email"
                id="email"
                class="form-style"
                type="email"
                name="email"
              >
            </div>

            <div class="form_group">
              <label class="sub_title" for="password">Password</label>
              <input
                placeholder="Enter your password"
                id="password"
                class="form-style"
                type="password"
                name="password"
              >
            </div>

            <div>
              <button type="submit" class="btn" id="signupBtn">SIGN UP</button>
              <p>Have a shrimp already? <a class="link" href="pages/login.html">Login Here!</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="center-column">
      <button id="adminBoardBtn" class="button-name" role="button">Admin</button>
      <img src="img/shrimp.png" class="img_container" alt="Shrimp">
    </div>

    <!-- Skin Container -->
    <div>
      <div id="inventoryContainer" class="items-skin-container">
        <div
          style="background: #85F2E7; margin: 10px; border-radius: 5px; padding: 1px; box-shadow: 3px 3px 0px 0px #1b67b3;"
        >
          <p style="margin-left: 20px; font-size: 24;" class="abhaya-libre-bold"> Skins:</p>

          <div class="skin-container">
            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #00FF87, #60EFFF);"
            >

            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #4290bd, #584969);"
            >

            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #ff4800, #6a60ff);"
            >

            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #021a0f, #e2e4e4);"
            >

            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #a200ff, #8a60ff);"
            >

            <img
              src="img/shrimp.png"
              class="skin"
              alt="Shrimp"
              style="background: linear-gradient(90deg, #ff0055, #ff6060);"
            >
          </div>
        </div>

        <div
          style="background: #85F2E7; margin: 10px; border-radius: 5px; padding: 1px; box-shadow: 3px 3px 0px 0px #1b67b3;"
        >
          <p style="margin-left: 20px; font-size: 24;" class="abhaya-libre-bold"> Items:</p>

          <div class="item-container">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
            <img src="img/shrimp.png" class="item" alt="Shrimp">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Fortune Container -->
  <div id="fortuneContainer" class="fortune-container">
    <h2>Fortune</h2>

    <hr>

    <!-- JavaScript replaces this text with the random fortune from the database -->
    <p><i id="fortuneText">Click Tell Fortune to ask the shrimp.</i></p>

    <p id="fortuneType">Good Fortune</p>
    <p>Total Fortunes Received: <span id="fortuneCountHome">0</span></p>
    <p>Saved At: <span id="fortuneTime">--</span></p>

    <h3>Reward:</h3>

    <h4>Item:</h4>
    <img src="img/shrimp.png" class="item" alt="Shrimp">

    <h4>Skin:</h4>
    <img
      src="img/shrimp.png"
      class="skin"
      alt="Shrimp"
      style="background: linear-gradient(90deg, #00FF87, #60EFFF);"
    >

    <p>Rarity: 5%</p>

    <div class="fortune-button-container">
      <button id="closeFortuneBtn" class="button-name" role="button">Close</button>
      <button id="newFortuneBtn" class="button-name" role="button">Another?</button>
    </div>
  </div>

  <!-- footer buttons: login, tell fortune, profile -->
  <footer>
    <nav class="footer-nav">
      <div class="nav-item center">
        <button id="loginBtn" class="button-name" role="button">
          <a class="link-normal" href="pages/login.html">Login</a>
        </button>
      </div>

      <div class="nav-item right fortune-footer-controls">
        <!-- Evil toggle tells JavaScript which type of fortune to request -->
        <label class="evil-toggle">
          <input type="checkbox" id="evilToggle">
          Evil Fortune
        </label>

        <!-- Button asks the shrimp for a random fortune -->
        <button id="tellFortuneBtn" class="button-name" role="button">
          Tell Fortune
        </button>
      </div>

      <div class="nav-item left">
        <button id="profileBtn" class="button-name" role="button">
          Profile
        </button>
      </div>
    </nav>
  </footer>
</body>

</html>