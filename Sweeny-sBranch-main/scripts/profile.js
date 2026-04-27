// TODO: ATTACH TO DB
// Back button
document.getElementById("backBtn").addEventListener("click", () => {
  window.location.href = "../pages/login.html";
});

// Example inventory fill
const inventoryGrid = document.getElementById("inventoryGrid");

for (let i = 0; i < 24; i++) {
  const box = document.createElement("div");
  inventoryGrid.appendChild(box);
}

// Example fortune data
const fortunes = [
  { text: "Today is a good day to make new friends.", item: "Coin", date: "2026-04-13" },
  { text: "Someone is thinking of you.", item: "Old Boot", date: "2026-04-12" }
];

const table = document.getElementById("fortuneTable");

fortunes.forEach(f => {
  const row = document.createElement("tr");
  row.innerHTML = `
    <td>${f.text}</td>
    <td>${f.item}</td>
    <td>${f.date}</td>
  `;
  table.appendChild(row);
});

// Stats example
document.getElementById("fortuneCount").innerText = fortunes.length;
document.getElementById("invSize").innerText = 24;