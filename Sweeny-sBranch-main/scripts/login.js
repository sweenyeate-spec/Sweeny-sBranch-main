const msg = document.getElementById('loginMessage');

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("loginForm").addEventListener('submit', function(e) {
    e.preventDefault();

    fetch("../php/db_login.php", {
      method: "POST",
      body: new FormData(e.target),
      credentials: "same-origin"
    })
      .then(res => res.text())
      .then(data => {

        document.getElementById("loginMessage").style.margin = "10px 0px";
        data = data.trim();
        //if login succeeds redirect and user is now logged in.
        if (data === "ok") {
          msg.textContent = "Login successful! Redirecting...";
          msg.style.color = "green";
          setTimeout(() => {
            window.location.href = '../index.php';
          }, 1000);
        } else if (data === "invalid") {
          msg.textContent = "Invalid credentials. Please try again.";
          msg.style.color = "red";
        } else {
          msg.textContent = "Error: " + data;
          msg.style.color = "orange";
        }
      });
  });
});
