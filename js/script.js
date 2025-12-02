document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const leftArrow = document.querySelector(".sidebar-arrow-left");
  const rightArrow = document.querySelector(".sidebar-arrow-right");

  const scrollAmount = 200;
});

// referenceNo

function getReferenceNo() {
  return localStorage.getItem("referenceNo") || 0;
}

function setReferenceNo(referenceNo) {
  localStorage.setItem("referenceNo", referenceNo);
}

function clearReferenceNo() {
  localStorage.removeItem("referenceNo");
}
function clearCart() {
  localStorage.removeItem("cart");
}

// registerNo (Register Number)
function getRegisterNo() {
  return localStorage.getItem("registerNo") || null;
}

function setRegisterNo(registerNo) {
  localStorage.setItem("registerNo", registerNo);
}

function clearRegisterNo() {
  localStorage.removeItem("registerNo");
}

// Function to fetch register number from server and store in localStorage
function loadRegisterNo() {
  return fetch("../api/getRegisterNo.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.data && data.data.register_no) {
        setRegisterNo(data.data.register_no);
        console.log("Register number loaded:", data.data.register_no);
        return data.data.register_no;
      }
      throw new Error(data.message || "Failed to load register number");
    })
    .catch((err) => {
      console.error("Error loading register number:", err);
      return null;
    });
}

// kioskRegNo
function getKioskRegNo() {
  return localStorage.getItem("kioskRegNo") || "";
}

function setKioskRegNo(kioskRegNo) {
  localStorage.setItem("kioskRegNo", kioskRegNo);
}

function clearOrderType() {
  localStorage.removeItem("orderType");
}

function clearKioskRegNo() {
  localStorage.removeItem("kioskRegNo");
}

function redirectToIndexIfNoReferenceNumber() {
  const referenceNo = getReferenceNo();

  if (referenceNo === 0) {
    console.warn("No reference number found!");
    window.location.href = "index.php";
  }
}

// totals

function getTotals() {
  return JSON.parse(localStorage.getItem("totals")) || [];
}

function setTotals(totals) {
  localStorage.setItem("totals", JSON.stringify(totals));
}

function clearTotals() {
  localStorage.removeItem("totals");
}

function clearCurrentOrder() {
  localStorage.removeItem("currentOrder");
}
function ScanOrders() {
  window.location.href = "../pages/kioskScanOrder.php";
}

function finishOrder() {
  clearAll();

  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    modal.style.display = "none";
  });

  const confirmations = document.body.querySelectorAll(
    'div[style*="z-index: 10000"]'
  );
  confirmations.forEach((modal) => {
    try {
      document.body.removeChild(modal);
    } catch (e) {
      console.error("Error removing confirmation modal:", e);
    }
  });

  const quantityElement = document.querySelector(".view-cart-btn span");
  const priceElement = document.querySelector(".cart-price");

  if (quantityElement) {
    quantityElement.textContent = "0";
  }

  if (priceElement) {
    priceElement.textContent = "₱0.00";
  }

  window.location.href = "../index.php";
}

function clearAll() {
  clearOrderType();
  clearKioskRegNo();
  clearReferenceNo();
  clearCart();
  clearTotals();
  clearCurrentOrder();
  // Note: Do NOT clear registerNo as it should persist across sessions
}
