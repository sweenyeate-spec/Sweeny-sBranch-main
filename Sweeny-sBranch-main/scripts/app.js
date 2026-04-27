document.addEventListener("DOMContentLoaded", () => {
  const signup = document.getElementById("signupContainer");
  const inventory = document.getElementById("inventoryContainer");
  const fortuneBox = document.getElementById("fortuneContainer");
  const loginBtn = document.getElementById("loginBtn");
  const adminBtn = document.getElementById("adminBoardBtn");
  const fortuneBtn = document.getElementById("fortuneBtn");
  const overlay = document.getElementById("overlay");
  const closeFortuneBtn = document.getElementById("closeFortuneBtn");

  // Send logout request to PHP, then reload page
  function logout() {
    fetch("php/db_logout.php", {
      credentials: "same-origin"
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "ok") {
          location.reload();
        } else {
          alert("Logout failed.");
        }
      })
      .catch((error) => {
        console.error("Logout error:", error);
        alert("Could not log out.");
      });
  }

  // Show the fortune popup
  function openFortuneBox() {
    if (fortuneBox) {
      fortuneBox.style.display = "block";
      fortuneBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    if (overlay) {
      overlay.style.display = "block";
    }
  }

  // Hide the fortune popup
  function closeFortuneBox() {
    if (fortuneBox) {
      fortuneBox.style.display = "none";
    }

    if (overlay) {
      overlay.style.display = "none";
    }
  }

  // Update page based on whether the user is logged in
  function loginHandler(data) {
    console.log("LOGIN STATE:", data);

    // Remove any old inline handlers by replacing buttons with clones
    if (loginBtn) {
      const newLoginBtn = loginBtn.cloneNode(true);
      loginBtn.parentNode.replaceChild(newLoginBtn, loginBtn);
    }

    if (adminBtn) {
      const newAdminBtn = adminBtn.cloneNode(true);
      adminBtn.parentNode.replaceChild(newAdminBtn, adminBtn);
    }

    if (fortuneBtn) {
      const newFortuneBtn = fortuneBtn.cloneNode(true);
      fortuneBtn.parentNode.replaceChild(newFortuneBtn, fortuneBtn);
    }

    // Re-get elements after cloning
    const freshLoginBtn = document.getElementById("loginBtn");
    const freshAdminBtn = document.getElementById("adminBoardBtn");
    const freshFortuneBtn = document.getElementById("fortuneBtn");

    if (data.loggedIn) {
      // Hide signup section and show inventory
      if (signup) signup.style.display = "none";
      if (inventory) inventory.style.display = "block";

      // Login button becomes logout
      if (freshLoginBtn) {
        freshLoginBtn.textContent = "Logout";
        freshLoginBtn.addEventListener("click", logout);
      }

      // Show admin button only for admins
      if (freshAdminBtn) {
        if (Number(data.isAdmin) === 1) {
          freshAdminBtn.style.visibility = "visible";
          freshAdminBtn.addEventListener("click", () => {
            window.location.href = "pages/admin_dashboard.html";
          });
        } else {
          freshAdminBtn.style.visibility = "hidden";
        }
      }

      // Fortune popup button
      if (freshFortuneBtn) {
        freshFortuneBtn.addEventListener("click", openFortuneBox);
      }

      if (closeFortuneBtn) {
        closeFortuneBtn.addEventListener("click", closeFortuneBox);
      }

      if (overlay) {
        overlay.addEventListener("click", closeFortuneBox);
      }
    } else {
      // User is not logged in
      if (inventory) inventory.style.display = "none";
      if (signup) signup.style.display = "block";

      if (freshLoginBtn) {
        freshLoginBtn.textContent = "Login";
        freshLoginBtn.addEventListener("click", () => {
          window.location.href = "pages/login.html";
        });
      }

      if (freshAdminBtn) {
        freshAdminBtn.style.visibility = "hidden";
      }
    }
  }

  // Ask PHP whether the user is logged in
  fetch("php/check_login_state.php", {
    credentials: "same-origin"
  })
    .then((res) => res.json())
    .then((data) => {
      loginHandler(data);
    })
    .catch((error) => {
      console.error("Login state error:", error);
    });
});