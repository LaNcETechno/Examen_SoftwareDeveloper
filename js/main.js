const openBtn = document.getElementById('openPopup');
const overlay = document.getElementById('popupOverlay');
const cancelBtn = document.getElementById('cancelBtn');

openBtn.addEventListener('click', () => {
    overlay.style.display = 'flex';
});

cancelBtn.addEventListener('click', () => {
    overlay.style.display = 'none';
});

document.addEventListener("DOMContentLoaded", function () {
  const userIcon = document.getElementById("userIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");

  if (userIcon && dropdownMenu) {
    // Klik op het icoon opent/sluit de dropdown
    userIcon.addEventListener("click", function (event) {
      event.stopPropagation(); // voorkomt dat het meteen sluit
      dropdownMenu.classList.toggle("show");
    });

    // Klik buiten de dropdown sluit hem weer
    document.addEventListener("click", function (event) {
      if (!dropdownMenu.contains(event.target) && event.target !== userIcon) {
        dropdownMenu.classList.remove("show");
      }
    });
  }
});




