// Function to format the date and time
function getFormattedDateTime() {
  const now = new Date();
  const date = now.toLocaleDateString("en-PH", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
  const time = now.toLocaleTimeString("en-PH", {
    hour: "2-digit",
    minute: "2-digit",
  });

  return { date, time };
}

// Function to safely format currency numbers
function formatCurrency(value) {
  // Ensure the value is a number or convert it to 0
  const numValue = typeof value === "number" ? value : 0;
  return `₱${numValue.toFixed(2)}`;
}

// Function to get order details from localStorage
function getOrderDetails() {
  console.log("Fetching order details from localStorage");
  const orderData = localStorage.getItem("currentOrder");
  console.log("Raw localStorage data:", orderData);

  if (!orderData) {
    console.log("No order data found in localStorage");
    return {
      items: [],
      total: 0.0,
    };
  }

  try {
    // Parse the order data
    const parsedData = JSON.parse(orderData);
    console.log("Parsed order data:", parsedData);

    // If the structure is different than expected, try to adapt
    let items = [];
    let total = 0.0;

    // Handle different possible structures
    if (Array.isArray(parsedData)) {
      // If it's an array, assume it's directly an array of items
      items = parsedData;
      // Calculate total from items
      total = items.reduce((sum, item) => {
        const price =
          typeof item.price === "number"
            ? item.price
            : typeof item.price === "string"
            ? parseFloat(item.price.replace(/[^0-9.-]+/g, ""))
            : 0;
        const quantity =
          typeof item.quantity === "number"
            ? item.quantity
            : typeof item.quantity === "string"
            ? parseInt(item.quantity)
            : 1;
        return sum + price * quantity;
      }, 0);
    } else if (typeof parsedData === "object") {
      // If it's an object, check for items property
      if (Array.isArray(parsedData.items)) {
        items = parsedData.items;
      } else if (parsedData.cart && Array.isArray(parsedData.cart)) {
        items = parsedData.cart;
      } else {
        // Try to convert object properties to items if no items array
        items = Object.keys(parsedData)
          .filter((key) => typeof parsedData[key] === "object")
          .map((key) => parsedData[key]);
      }

      // Check for total property or calculate it
      if (typeof parsedData.total === "number") {
        total = parsedData.total;
      } else if (typeof parsedData.total === "string") {
        total = parseFloat(parsedData.total.replace(/[^0-9.-]+/g, ""));
      } else if (typeof parsedData.totalAmount === "number") {
        total = parsedData.totalAmount;
      } else if (typeof parsedData.totalAmount === "string") {
        total = parseFloat(parsedData.totalAmount.replace(/[^0-9.-]+/g, ""));
      } else {
        // Calculate total from items if not provided
        total = items.reduce((sum, item) => {
          const price =
            typeof item.price === "number"
              ? item.price
              : typeof item.price === "string"
              ? parseFloat(item.price.replace(/[^0-9.-]+/g, ""))
              : 0;
          const quantity =
            typeof item.quantity === "number"
              ? item.quantity
              : typeof item.quantity === "string"
              ? parseInt(item.quantity)
              : 1;
          return sum + price * quantity;
        }, 0);
      }
    }

    console.log("Processed items:", items);
    console.log("Calculated total:", total);

    return {
      items: items,
      total: isNaN(total) ? 0.0 : total,
    };
  } catch (e) {
    console.error("Error parsing order data:", e);
    return {
      items: [],
      total: 0.0,
    };
  }
}

// Function to print receipt using thermal printer
function printReceipt(orderNumber) {
  // Show printing modal
  let printingModal = document.createElement("div");
  printingModal.style.position = "fixed";
  printingModal.style.top = "0";
  printingModal.style.left = "0";
  printingModal.style.width = "100%";
  printingModal.style.height = "100vh";
  printingModal.style.background = "rgba(0, 0, 0, 0.8)";
  printingModal.style.zIndex = "10001";
  printingModal.style.display = "flex";
  printingModal.style.alignItems = "center";
  printingModal.style.justifyContent = "center";
  printingModal.style.fontFamily = "'Montserrat', sans-serif";

  printingModal.innerHTML = `
        <div style="background: white; padding: 30px 50px; border-radius: 20px; text-align: center;">
            <div style="margin-bottom: 20px; font-size: 30px;">🖨️</div>
            <h3 style="margin: 0; font-size: 24px; color: #333; margin-bottom: 10px;">Printing Receipt...</h3>
            <p style="margin: 0; color: #666; font-size: 16px;">Please wait a moment...</p>
        </div>
    `;

  document.body.appendChild(printingModal);

  // Get order details with safety checks
  const orderDetails = getOrderDetails();

  // Prepare print data
  const printData = {
    orderNumber: orderNumber,
    items: orderDetails.items,
    total: orderDetails.total || 0,
  };

  fetch("print.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(printData),
  })
    .then((response) => response.json())
    .then((data) => {
      try {
        document.body.removeChild(printingModal);
      } catch (e) {
        console.error("Error removing print modal:", e);
      }

      if (data.error) {
        throw new Error(data.error);
      }

      let successModal = document.createElement("div");
      successModal.style.position = "fixed";
      successModal.style.top = "20px";
      successModal.style.right = "20px";
      successModal.style.background = "#4CAF50";
      successModal.style.color = "white";
      successModal.style.padding = "15px 20px";
      successModal.style.borderRadius = "8px";
      successModal.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.2)";
      successModal.style.zIndex = "10002";
      successModal.style.fontFamily = "'Montserrat', sans-serif";

      successModal.innerHTML = `
      <div style="display: flex; align-items: center;">
          <span style="font-size: 18px; margin-right: 10px;">✓</span>
          <span style="font-size: 16px;">Receipt sent to printer!</span>
      </div>
    `;

      document.body.appendChild(successModal);

      setTimeout(() => {
        try {
          document.body.removeChild(successModal);
        } catch (e) {
          console.error("Error removing success modal:", e);
        }
      }, 3000);
    })
    .catch((error) => {
      console.error("Print Error:", error);

      try {
        document.body.removeChild(printingModal);
      } catch (e) {
        console.error("Error removing print modal after error:", e);
      }

      let errorModal = document.createElement("div");
      errorModal.style.position = "fixed";
      errorModal.style.top = "20px";
      errorModal.style.right = "20px";
      errorModal.style.background = "var(--danger-color)";
      errorModal.style.color = "white";
      errorModal.style.padding = "15px 20px";
      errorModal.style.borderRadius = "8px";
      errorModal.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.2)";
      errorModal.style.zIndex = "10002";
      errorModal.style.fontFamily = "'Montserrat', sans-serif";

      errorModal.innerHTML = `
      <div style="display: flex; align-items: center;">
          <span style="font-size: 18px; margin-right: 10px;">✗</span>
          <span style="font-size: 16px;">Error printing receipt: ${error.message}</span>
      </div>
    `;

      document.body.appendChild(errorModal);

      // Remove error message after 5 seconds
      setTimeout(() => {
        try {
          document.body.removeChild(errorModal);
        } catch (e) {
          console.error("Error removing error modal:", e);
        }
      }, 5000);
    });
}

// Modified function to show order confirmation
function showOrderConfirmation() {
  const orderNumber = Math.floor(10000 + Math.random() * 90000);
  let confirmationModal = document.createElement("div");
  confirmationModal.style.position = "fixed";
  confirmationModal.style.top = "0";
  confirmationModal.style.left = "0";
  confirmationModal.style.width = "100%";
  confirmationModal.style.height = "100vh";
  confirmationModal.style.background = "rgba(0, 0, 0, 0.8)";
  confirmationModal.style.zIndex = "10000";
  confirmationModal.style.display = "flex";
  confirmationModal.style.alignItems = "center";
  confirmationModal.style.justifyContent = "center";
  confirmationModal.style.fontFamily = "'Montserrat', sans-serif";

  confirmationModal.innerHTML = `
        <div style="background: white; width: 90%; max-width: 800px; border-radius: 24px; padding: 40px; text-align: center; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);">
            <div style="width: 100px; height: 100px; background: #4CAF50; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                <span style="color: white; font-size: 50px;">✓</span>
            </div>
            <h2 style="margin: 0 0 15px; font-size: 36px; color: #333;">Order Confirmed!</h2>
            <p style="margin: 0 0 30px; font-size: 20px; color: #666; max-width: 600px; margin-left: auto; margin-right: auto;">Your order has been successfully placed. You will receive a confirmation shortly.</p>
            <p style="margin: 0 0 40px; font-size: 24px; font-weight: 600; color: #333;">Order #${orderNumber}</p>
            <button style="background: #2e7d32; color: white; padding: 18px 40px; font-size: 20px; font-weight: 600; border: none; border-radius: 14px; cursor: pointer; box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);" onclick="finishOrder()">DONE</button>
        </div>
    `;

  document.body.appendChild(confirmationModal);

  printReceipt(orderNumber);
}

// Functions for the payment modal
function openPaymentModal() {
  document.getElementById("paymentOptionsModal").style.display = "flex";
}

function closePaymentModal() {
  document.getElementById("paymentOptionsModal").style.display = "none";
}

function processPayment(method) {
  closePaymentModal();

  let processingModal = document.createElement("div");
  processingModal.style.position = "fixed";
  processingModal.style.top = "0";
  processingModal.style.left = "0";
  processingModal.style.width = "100%";
  processingModal.style.height = "100vh";
  processingModal.style.background = "rgba(0, 0, 0, 0.8)";
  processingModal.style.zIndex = "10000";
  processingModal.style.display = "flex";
  processingModal.style.alignItems = "center";
  processingModal.style.justifyContent = "center";
  processingModal.style.fontFamily = "'Montserrat', sans-serif";

  let content = "";

  switch (method) {
    case "debit-credit":
      content = "Processing card payment...";
      break;
    case "gcash":
      content = "Connecting to GCash...";
      break;
    case "counter":
      content = "Confirming counter payment...";
      break;
    default:
      content = "Processing payment...";
  }

  processingModal.innerHTML = `
        <div style="background: white; padding: 30px 50px; border-radius: 20px; text-align: center;">
            <div style="margin-bottom: 20px; font-size: 30px;">⏳</div>
            <h3 style="margin: 0; font-size: 24px; color: #333; margin-bottom: 10px;">${content}</h3>
            <p style="margin: 0; color: #666; font-size: 16px;">Please wait a moment...</p>
        </div>
    `;

  document.body.appendChild(processingModal);

  setTimeout(() => {
    try {
      document.body.removeChild(processingModal);
    } catch (e) {
      console.error("Error removing processing modal:", e);
    }
    showOrderConfirmation();
  }, 2000);
}

function closeOrderModal() {
  // This function depends on your specific implementation
  const orderModal = document.getElementById("orderModal");
  if (orderModal) {
    orderModal.style.display = "none";
  }
}
function updateTotal() {
  const totals = JSON.parse(localStorage.getItem("totals"));

  // document.getElementById("showtotal").textContent =
  //   "Total: " + (totals && totals.total ? totals.total : 0);
}
