// Modal Functions
function showModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("show");
    modal.style.display = "block";
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("show");
    modal.style.display = "none";
  }
}

// Close modal when clicking outside
window.onclick = function (event) {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    if (event.target === modal) {
      modal.classList.remove("show");
      modal.style.display = "none";
    }
  });
};

// Close modal with ESC key
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    const modals = document.querySelectorAll(".modal.show");
    modals.forEach((modal) => {
      modal.classList.remove("show");
      modal.style.display = "none";
    });
  }
});

// Auto hide alerts
setTimeout(function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    alert.style.transition = "opacity 0.5s";
    alert.style.opacity = "0";
    setTimeout(() => {
      if (alert.parentNode) {
        alert.remove();
      }
    }, 500);
  });
}, 5000);

// Confirm delete
function confirmDelete(message) {
  return confirm(message || "Apakah Anda yakin ingin menghapus data ini?");
}

// Update datetime
function updateDateTime() {
  const datetimeElement = document.querySelector(".datetime");
  if (datetimeElement) {
    const now = new Date();
    const options = {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    datetimeElement.textContent =
      now.toLocaleDateString("id-ID", options) + " WIB";
  }
}

// Update datetime every minute
setInterval(updateDateTime, 60000);
updateDateTime();

// Table search
function searchTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);

  if (input && table) {
    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      const cells = row.getElementsByTagName("td");
      let found = false;

      for (let j = 0; j < cells.length; j++) {
        const cell = cells[j];
        if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
          found = true;
          break;
        }
      }

      row.style.display = found ? "" : "none";
    }
  }
}

// Form validation
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (form) {
    const inputs = form.querySelectorAll("[required]");
    let isValid = true;

    inputs.forEach((input) => {
      if (!input.value.trim()) {
        isValid = false;
        input.style.borderColor = "#e74c3c";
      } else {
        input.style.borderColor = "#ddd";
      }
    });

    return isValid;
  }
  return true;
}

// Loading state
function showLoading(message = "Loading...") {
  const loading = document.createElement("div");
  loading.id = "loading-overlay";
  loading.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        color: white;
        font-size: 18px;
    `;
  loading.textContent = message;
  document.body.appendChild(loading);
}

function hideLoading() {
  const loading = document.getElementById("loading-overlay");
  if (loading) {
    loading.remove();
  }
}

// Print function
function printElement(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    const printWindow = window.open("", "_blank");
    printWindow.document.write("<html><head><title>Print</title>");
    printWindow.document.write(
      "<style>table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}</style>"
    );
    printWindow.document.write("</head><body>");
    printWindow.document.write(element.innerHTML);
    printWindow.document.write("</body></html>");
    printWindow.document.close();
    printWindow.print();
  }
}
