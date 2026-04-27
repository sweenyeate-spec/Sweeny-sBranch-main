
// this handles the signup form submission and gets the result of the php, mostly for handling users already existing 
document.getElementById("signupForm").addEventListener('submit', function (e) {
    e.preventDefault(); // prevent form from submitting with bad php

  fetch("php/db_signup.php", {
    method: "POST",
    body: new FormData(e.target)
  })
    // Convert response into plain text
    .then(res => res.text())
    // Handle returned data
    .then(data => {
      const msg = document.getElementById("signupMessage");
      msg.style.margin = "10px 0px";
      data = data.trim(); // remove extra whitespace
      //directly check the php response

      // Case 1: User already exists
      if (data === "exists") {
        msg.textContent = "User already exists!";
        msg.style.color = "red";
      }
      // Case 2: Succesful creating account
      else if (data === "ok") {
        msg.textContent = "";
        window.location.href = 'pages/login.html';
      } 
      // Case 3: Unexpected error
      else {
        msg.textContent = "An error occurred during signup: " + data;
        msg.style.color = "orange";
      }
    });
});
