// Arrays that store data fetched from the backend
let users = [];   // List users
let skins = [];   // List skins
let items = [];   // List items

// *DELETE* --- old rarity map
// Converts rarity number from database into readable text
function rarityLabelFromWeight(weight) {
  const map = {
    1: "Common",
    2: "Uncommon",
    3: "Rare",
    4: "Legendary"
  };

  // Return matching label, or Common if value is invalid
  return map[Number(weight)] || "Common";
}

// Shows an error message to the user
function showError(message) {
  alert(message || "Something went wrong.");
}

// USERS

// Loads all users from the backend
async function loadUsers() {
  try {
    // Request users from PHP
    const response = await fetch("../php/admin_list_user.php", {
      method: "GET",
      credentials: "same-origin" // include session cookie
    });

    // Convert response into JSON
    const data = await response.json();

    // If response failed, show error
    if (!response.ok) {
      showError(data.error || "Could not load users.");
      return;
    }

    // Store users safely as an array
    users = Array.isArray(data) ? data : [];

    // Render user table
    renderUsers();

  } catch (error) {
    console.error("loadUsers error:", error);
    showError("Could not load users.");
  }
}

// Renders the users table on the page
function renderUsers() {
  const tbody = document.querySelector("#usersTable tbody");
  if (!tbody) return;

  // Clear old table rows
  tbody.innerHTML = "";

  // If no users exist, show a message row
  if (users.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `<td colspan="4">No users found.</td>`;
    tbody.appendChild(row);
    return;
  }

  // Create one row per user
  users.forEach((user) => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${user.username}</td>
      <td>${Number(user.isAdmin) === 1 ? "Admin" : "User"}</td>
      <td>
        <button class="viewBtn" data-id="${user.userID}">View</button>
        <button class="resetBtn" data-id="${user.userID}">Reset Password</button>
        <button class="deleteBtn" data-id="${user.userID}">Delete</button>
      </td>
      <td>${user.email}</td>
    `;

    tbody.appendChild(row);
  });
}

// Shows a popup with user details
function viewUser(user) {
  alert(
    "USER PROFILE\n\n" +
    "User ID: " + user.userID + "\n" +
    "Username: " + user.username + "\n" +
    "Email: " + user.email + "\n" +
    "Admin: " + (Number(user.isAdmin) === 1 ? "Yes" : "No")
  );
}

// Sends request to reset a user's password
async function resetUserPassword(user) {
  // Ask admin for the new password
  const newPassword = prompt("Enter a new password for " + user.username);

  // Stop if nothing was entered
  if (!newPassword) return;

  try {
    // Send POST request to PHP
    const response = await fetch("../php/admin_update_user.php", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body:
        "action=reset_password" +
        "&userID=" + encodeURIComponent(user.userID) +
        "&newPassword=" + encodeURIComponent(newPassword)
    });

    // Convert response into JSON
    const data = await response.json();

    // If request failed, show error
    if (!response.ok || !data.ok) {
      showError(data.error || "Password reset failed.");
      return;
    }

    // Let admin know reset worked
    alert("Password reset successfully.");

  } catch (error) {
    console.error("resetUserPassword error:", error);
    showError("Could not reset password.");
  }
}

// Deletes a user from the database
async function deleteUser(user) {
  // Ask for confirmation first
  const confirmed = confirm("Delete user " + user.username + "?");
  if (!confirmed) return;

  try {
    // Send delete request to PHP
    const response = await fetch("../php/admin_delete_user.php", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "userID=" + encodeURIComponent(user.userID)
    });

    // Convert response into JSON
    const data = await response.json();

    // If request failed, show error
    if (!response.ok || !data.ok) {
      showError(data.error || "Delete failed.");
      return;
    }

    // Remove deleted user from local array
    users = users.filter((u) => Number(u.userID) !== Number(user.userID));

    // Re-render table
    renderUsers();

    // Let admin know delete worked
    alert("User deleted.");

  } catch (error) {
    console.error("deleteUser error:", error);
    showError("Could not delete user.");
  }
}

// Sets up click handling for View/Reset/Delete buttons in the user table
function setupUserActions() {
  const table = document.getElementById("usersTable");
  if (!table) return;

  // Use event delegation so buttons work even after table re-renders
  table.addEventListener("click", async (e) => {
    const id = Number(e.target.dataset.id);
    if (!id) return;

    // Find matching user in local array
    const user = users.find((u) => Number(u.userID) === id);
    if (!user) return;

    // View button
    if (e.target.classList.contains("viewBtn")) {
      viewUser(user);
    }

    // Reset password button
    if (e.target.classList.contains("resetBtn")) {
      await resetUserPassword(user);
    }

    // Delete button
    if (e.target.classList.contains("deleteBtn")) {
      await deleteUser(user);
    }
  });
}

// COSMETICS

// Loads skins or items from the backend depending on type
async function loadCosmetics(type) {
  try {
    // Request list from PHP
    const response = await fetch(
      `../php/admin_list_cosmetics.php?type=${encodeURIComponent(type)}`,
      {
        method: "GET",
        credentials: "same-origin"
      }
    );

    // Convert response into JSON
    const data = await response.json();

    // If request failed, show error
    if (!response.ok) {
      showError(data.error || `Could not load ${type}s.`);
      return;
    }

    // Store data in correct array and render correct dropdown
    if (type === "skin") {
      skins = Array.isArray(data) ? data : [];
      renderSkins();
    } else {
      items = Array.isArray(data) ? data : [];
      renderItems();
    }

  } catch (error) {
    console.error("loadCosmetics error:", error);
    showError(`Could not load ${type}s.`);
  }
}

// Renders the skins dropdown
function renderSkins() {
  const select = document.getElementById("existingSkins");
  if (!select) return;

  // Clear old options
  select.innerHTML = "";

  // If no skins exist, show placeholder
  if (skins.length === 0) {
    select.innerHTML = `<option value="">No skins yet</option>`;
    return;
  }

  // Create one option per skin
  skins.forEach((skin) => {
    const option = document.createElement("option");
    option.value = skin.cosmeticID;
    option.textContent = `${skin.cosmeticDescription} (${rarityLabelFromWeight(skin.rarityWeight)})`;
    select.appendChild(option);
  });
}

// Renders the items dropdown
function renderItems() {
  const select = document.getElementById("existingItems");
  if (!select) return;

  // Clear old options
  select.innerHTML = "";

  // If no items exist, show placeholder
  if (items.length === 0) {
    select.innerHTML = `<option value="">No items yet</option>`;
    return;
  }

  // Create one option per item
  items.forEach((item) => {
    const option = document.createElement("option");
    option.value = item.cosmeticID;
    option.textContent = `${item.cosmeticDescription} (${rarityLabelFromWeight(item.rarityWeight)})`;
    select.appendChild(option);
  });
}

// Sets up Add Skin and Delete Skin buttons
function setupSkinButtons() {
  const addBtn = document.getElementById("addSkinBtn");
  const deleteBtn = document.getElementById("deleteSkinBtn");

  // Add new skin
  if (addBtn) {
    addBtn.addEventListener("click", async () => {
      const name = document.getElementById("newSkinName").value.trim();
      const rarity = document.getElementById("newSkinRarity").value;

      // Stop if no skin name was entered
      if (!name) {
        showError("Enter a skin name.");
        return;
      }

      try {
        // Send add request to PHP
        const response = await fetch("../php/admin_add_cosmetic.php", {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body:
            "type=skin" +
            "&name=" + encodeURIComponent(name) +
            "&rarity=" + encodeURIComponent(rarity)
        });

        // Convert response into JSON
        const data = await response.json();

        // If request failed, show error
        if (!response.ok || !data.ok) {
          showError(data.error || "Could not add skin.");
          return;
        }

        // Clear input field
        document.getElementById("newSkinName").value = "";

        // Reload skins so dropdown updates
        await loadCosmetics("skin");

        // Let admin know it worked
        alert("Skin added successfully.");

      } catch (error) {
        console.error("add skin error:", error);
        showError("Could not add skin.");
      }
    });
  }

  // Delete selected skin
  if (deleteBtn) {
    deleteBtn.addEventListener("click", async () => {
      const select = document.getElementById("existingSkins");
      const id = select.value;

      // Stop if nothing selected
      if (!id) {
        showError("Select a skin.");
        return;
      }

      // Ask for confirmation
      const confirmed = confirm("Delete this skin?");
      if (!confirmed) return;

      try {
        // Send delete request to PHP
        const response = await fetch("../php/admin_delete_cosmetic.php", {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body:
            "type=skin" +
            "&cosmeticID=" + encodeURIComponent(id)
        });

        // Convert response into JSON
        const data = await response.json();

        // If request failed, show error
        if (!response.ok || !data.ok) {
          showError(data.error || "Could not delete skin.");
          return;
        }

        // Reload skins so dropdown updates
        await loadCosmetics("skin");

        // Let admin know it worked
        alert("Skin deleted successfully.");

      } catch (error) {
        console.error("delete skin error:", error);
        showError("Could not delete skin.");
      }
    });
  }
}

// Sets up Add Item and Delete Item buttons
function setupItemButtons() {
  const addBtn = document.getElementById("addItemBtn");
  const deleteBtn = document.getElementById("deleteItemBtn");

  // Add new item
  if (addBtn) {
    addBtn.addEventListener("click", async () => {
      const name = document.getElementById("newItemName").value.trim();
      const rarity = document.getElementById("newItemRarity").value;

      // Stop if no item name was entered
      if (!name) {
        showError("Enter an item name.");
        return;
      }

      try {
        // Send add request to PHP
        const response = await fetch("../php/admin_add_cosmetic.php", {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body:
            "type=item" +
            "&name=" + encodeURIComponent(name) +
            "&rarity=" + encodeURIComponent(rarity)
        });

        // Convert response into JSON
        const data = await response.json();

        // If request failed, show error
        if (!response.ok || !data.ok) {
          showError(data.error || "Could not add item.");
          return;
        }

        // Clear input field
        document.getElementById("newItemName").value = "";

        // Reload items so dropdown updates
        await loadCosmetics("item");

        // Let admin know it worked
        alert("Item added successfully.");

      } catch (error) {
        console.error("add item error:", error);
        showError("Could not add item.");
      }
    });
  }

  // Delete selected item
  if (deleteBtn) {
    deleteBtn.addEventListener("click", async () => {
      const select = document.getElementById("existingItems");
      const id = select.value;

      // Stop if nothing selected
      if (!id) {
        showError("Select an item.");
        return;
      }

      // Ask for confirmation
      const confirmed = confirm("Delete this item?");
      if (!confirmed) return;

      try {
        // Send delete request to PHP
        const response = await fetch("../php/admin_delete_cosmetic.php", {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body:
            "type=item" +
            "&cosmeticID=" + encodeURIComponent(id)
        });

        // Convert response into JSON
        const data = await response.json();

        // If request failed, show error
        if (!response.ok || !data.ok) {
          showError(data.error || "Could not delete item.");
          return;
        }

        // Reload items so dropdown updates
        await loadCosmetics("item");

        // Let admin know it worked
        alert("Item deleted successfully.");

      } catch (error) {
        console.error("delete item error:", error);
        showError("Could not delete item.");
      }
    });
  }
}

// Sets up the Back to Login button
function setupBackButton() {
  const btn = document.getElementById("backToLoginBtn");
  if (!btn) return;

  // Send user back to index/login page
  btn.addEventListener("click", () => {
    window.location.href = "../index.php";
  });
}

// Sets up the Save Changes button
function setupSaveButton() {
  const btn = document.getElementById("saveChangesBtn");
  if (!btn) return;

  // Right now changes already save automatically
  btn.addEventListener("click", () => {
    alert("Changes are already saved automatically.");
  });
}

// INIT DASHBOARD

// Sets up buttons and loads all page data
async function initDashboard() {
  // Set up user action buttons
  setupUserActions();

  // Set up cosmetic buttons
  setupSkinButtons();
  setupItemButtons();

  // Set up page navigation buttons
  setupBackButton();
  setupSaveButton();

  // Load all data from backend
  await loadUsers();
  await loadCosmetics("skin");
  await loadCosmetics("item");
}

// Start the dashboard when the script loads
initDashboard();