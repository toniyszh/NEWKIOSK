function createEmptyOrderModal() {
  const modal = document.createElement("div");
  modal.className = "modal empty-order-modal";
  modal.style.display = "flex";
  modal.style.alignItems = "center";
  modal.style.justifyContent = "center";
  modal.style.position = "fixed";
  modal.style.top = "0";
  modal.style.left = "0";
  modal.style.width = "100%";
  modal.style.height = "100vh";
  modal.style.zIndex = "20000";
  modal.innerHTML = `
    <div class="modal-content" style="width: 90%; max-width: 480px; padding: 20px; background: var(--primary-color); border-radius: 10px; border: 1px solid #e26b28; text-align:center; box-shadow:0 8px 24px rgba(0,0,0,0.2);">
      <img src="../images/icons/warning.png" alt="Empty Cart" style="width: 80px; height: 80px; margin-bottom: 10px;">
    <h2 style="margin-top:0; color: black">Your cart is empty</h2>
      <p style="color: black;">You haven't added any items yet. Would you like to browse the menu?</p>
      <div style="display:flex; gap:12px; justify-content:center; margin-top:18px;">
        <button class="empty-order-ok" style="padding:10px 18px; border-radius:8px; display:none; background:#4CAF50; color:#fff; border:none; cursor:pointer;">OK</button>
        <button class="empty-order-browse" style="font-size: 15px; padding:10px 18px; border-radius:8px; background:var(--secondary-color); color: var(--primary-color); border: none; cursor:pointer;">Browse Menu</button>
      </div>
    </div>
  `;
  return modal;
}

function showEmptyOrderModal() {
  const existing = document.querySelector(".empty-order-modal");
  if (existing) return; // already shown
  const modal = createEmptyOrderModal();
  document.body.appendChild(modal);

  modal.querySelector(".empty-order-ok").addEventListener("click", () => {
    modal.remove();
  });

  modal.querySelector(".empty-order-browse").addEventListener("click", () => {
    modal.remove();
  });
}

function addToOrder(name, price, itemCode, event) {
  let modal = document.createElement("div");
  modal.classList.add("modal");

  let imageSrc = "";

  if (event && event.currentTarget) {
    const menuItem = event.currentTarget;
    const img = menuItem.querySelector("img");
    if (img && img.src) {
      imageSrc = img.src;
    }
  }

  // Add CSS for animations (add this once, ideally in your stylesheet or <style> tag)
  const styleSheet = document.createElement("style");
  styleSheet.textContent = `
  @keyframes modalFadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }
  
  @keyframes slideDown {
    from {
      transform: translateY(-100%);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  @keyframes contentFadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .modal-backdrop {
    animation: modalFadeIn 0.3s ease-out;
  }
  
  .modal-content-animated {
    animation: slideDown 0.4s ease-out;
  }
  
  .modal-content-animated > * {
    animation: contentFadeIn 0.5s ease-out forwards;
    animation-delay: 0.2s;
    opacity: 0;
  }
  
  .modal-content-animated > *:nth-child(2) {
    animation-delay: 0.3s;
  }
  
  .modal-content-animated > *:nth-child(3) {
    animation-delay: 0.4s;
  }
`;
  document.head.appendChild(styleSheet);

  // Add animation class to modal
  modal.classList.add("modal-backdrop");

  modal.innerHTML = `
  <div class="modal-content modal-content-animated" style="
    width: 100vw; 
    height: 100vh; 
    display: flex; 
    flex-direction: column; 
    position: relative; 
    padding: clamp(10px, 3vw, 20px); 
    background-image: url('../images/background/tsuru.png'); 
    background-size: cover; 
    background-position: center; 
    margin: 0; 
    border-radius: 0;
  ">
  
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
    
    <h2 style="
      text-align: center;
      margin: clamp(15px, 5vw, 40px) 0 clamp(10px, 3vw, 20px);
      position: relative;
      z-index: 2;
      font-size: clamp(18px, 4vw, 28px);
    ">
      YOU'VE SELECTED:
    </h2>

    <!-- Main content -->
    <div class="mainContent" style="
      flex: 1;
      overflow-y: auto;  
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      position: relative; 
      z-index: 2; 
      padding-bottom: clamp(100px, 20vw, 140px);
      padding: 0 clamp(10px, 2vw, 20px) clamp(100px, 20vw, 140px);
    ">
      
      <!-- Item Display -->
      <div class="added-item" style="
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        margin-bottom: clamp(15px, 3vw, 20px);
        width: 100%;
        max-width: 500px;
      ">
        <img src="${imageSrc}" alt="${name}" class="modal-image" style="
          width: 100%; 
          max-width: clamp(250px, 60vw, 400px); 
          height: auto; 
          object-fit: contain;
        ">
        <p style="
          font-size: clamp(20px, 4vw, 30px); 
          font-weight: bold; 
          text-align: center; 
          margin-top: clamp(10px, 2vw, 15px); 
          color: black;
          line-height: 1.3;
        ">
          ${name} <br> 
          <strong>₱${Number(price).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}</strong>
        </p>
        
        <!-- Quantity Selector -->
        <div class="quantity-selector" style="
          display: flex; 
          align-items: center; 
          justify-content: center; 
          margin: clamp(15px, 3vw, 20px) 0;
          gap: clamp(10px, 2vw, 15px);
        ">
          <button class="decrease-qty" style="
            width: clamp(45px, 10vw, 60px); 
            height: clamp(45px, 10vw, 60px); 
            font-size: clamp(22px, 5vw, 30px); 
            border-radius: 10px; 
            background: #fae1d14f; 
            color: black; 
            border: 2px solid #721719; 
            cursor: pointer;
          ">-</button>
          <input type="text" value="1" class="qtyinput" id="quantity" style="
            width: clamp(50px, 12vw, 60px); 
            height: clamp(45px, 10vw, 60px); 
            background: #fae1d14f; 
            color: #080808; 
            text-align: center; 
            font-size: clamp(20px, 4.5vw, 28px); 
            border: 2px solid #721719; 
            border-radius: 5px;
          ">
          <button class="increase-qty" style="
            width: clamp(45px, 10vw, 60px); 
            height: clamp(45px, 10vw, 60px); 
            font-size: clamp(22px, 5vw, 28px); 
            border-radius: 10px; 
            background: var(--secondary-color); 
            color: #e0e1dc; 
            border: none; 
            cursor: pointer;
          ">+</button>
        </div>
      </div>

      <!-- Modifiers Section -->
      <div class="modifiers-section" style="
        width: 100%; 
        max-width: 900px; 
        margin-top: clamp(15px, 3vw, 20px);
      ">
        <h3 style="
          text-align: center; 
          margin-bottom: clamp(10px, 2vw, 15px); 
          font-size: clamp(18px, 4vw, 25px);
        ">ADD ONS</h3>
        <div id="modifiersGrid" class="modifiers-grid" style="
          display: grid; 
          grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 40vw, 250px), 1fr)); 
          gap: clamp(10px, 2vw, 15px); 
          padding: 0 clamp(10px, 3vw, 20px);
        ">
          <p style="text-align: center; color: #666;">Loading modifiers...</p>
        </div>
      </div>

    </div>
    
    <!-- Bottom Buttons -->
    <div class="bottom-actions" style="
      display: flex; 
      gap: clamp(10px, 3vw, 20px); 
      width: 100%; 
      margin-bottom: clamp(60px, 12vw, 80px); 
      padding: 0 clamp(15px, 5vw, 40px); 
      z-index: 10; 
      box-sizing: border-box;
    ">
      <button class="cancel-order btn secondary" style="
        flex: 1; 
        font-size: clamp(16px, 3.5vw, 24px); 
        padding: clamp(12px, 3vw, 18px); 
        border-radius: 10px; 
        cursor: pointer; 
        background: #fae1d14f; 
        color: #000000ff; 
        border: 2px solid #721719; 
        max-width: 600px;
      ">CANCEL</button>
      <button class="add-to-order btn success" style="
        flex: 1; 
        font-size: clamp(16px, 3.5vw, 24px); 
        padding: clamp(12px, 3vw, 18px); 
        border-radius: 10px; 
        cursor: pointer;
        background: var(--secondary-color); 
        color: #fff8f3; 
        border: none; 
        max-width: 600px;
      ">ADD TO ORDER</button>
    </div>
  </div>
`;
  const categoryCode = "10101";
  fetch(`../api/fetch_modifiers.php?category_code=${categoryCode}`)
    .then((res) => res.json())
    .then((data) => {
      const grid = modal.querySelector("#modifiersGrid");
      grid.innerHTML = "";
      t;

      if (!data.length) {
        grid.innerHTML =
          "<p style='text-align:center;'>No modifiers available.</p>";
        return;
      }

      data.forEach((mod) => {
        const price = mod.Price ? parseFloat(mod.Price) : 0;
        const priceText = price > 0 ? `+₱${price.toFixed(2)}` : "Free";

        const div = document.createElement("div");
        div.className = "modifier-item";
        div.style.cssText =
          "background: var(--secondary-color); padding: 15px; border-radius: 10px;";

        div.innerHTML = `
        <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
   
          <div style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" class="modifier-checkbox"
                   data-modifier="${mod.Modifier}"
                   data-price="${price}"
                   style="width: 20px; height: 20px; cursor: pointer;">
            <span style="font-size: 18px; color: #e0e1dc; font-weight: 500;">${mod.Modifier}</span>
          </div>
          <span style="font-size: 16px; color: #d7ccccff;">${priceText}</span>
        </label>
      `;
        grid.appendChild(div);
      });
    })
    .catch((err) => {
      console.error("Error loading modifiers:", err);
      modal.querySelector("#modifiersGrid").innerHTML =
        "<p style='color:red; text-align:center;'>Failed to load modifiers.</p>";
    });

  const style = document.createElement("style");
  style.textContent = `

`;

  document.head.appendChild(style);

  document.head.appendChild(style);
  document.head.appendChild(style);

  const modifierCheckboxes = modal.querySelectorAll(".modifier-checkbox");
  modifierCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const modifierItem = this.closest(".modifier-item");
      if (this.checked) {
        modifierItem.style.borderColor = "var(--primary-color)";
        modifierItem.style.background = "rgba(255, 255, 255, 1)";
      } else {
        modifierItem.style.borderColor = "#ddd";
        modifierItem.style.background = "rgba(255, 255, 255, 0.9)";
      }
      updateTotal();
    });
  });

  document.body.appendChild(modal);
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");

  const quantityInput = modal.querySelector("#quantity");
  modal.querySelector(".decrease-qty").addEventListener("click", function () {
    let qty = parseInt(quantityInput.value);
    if (qty > 1) {
      quantityInput.value = qty - 1;
    }
  });
  modal.querySelector(".increase-qty").addEventListener("click", function () {
    let qty = parseInt(quantityInput.value);
    quantityInput.value = qty + 1;
  });

  modal.querySelector(".add-to-order").addEventListener("click", function () {
    const quantity = parseInt(quantityInput.value);

    const existingItemIndex = currentOrder.findIndex(
      (item) => item.itemCode === itemCode
    );

    if (existingItemIndex !== -1) {
      currentOrder[existingItemIndex].quantity += quantity;
      currentOrder[existingItemIndex].totalPrice =
        currentOrder[existingItemIndex].price *
        currentOrder[existingItemIndex].quantity;
    } else {
      currentOrder.push({
        name: name,
        price: price,
        quantity: quantity,
        itemCode: itemCode,
        totalPrice: price * quantity,
        imageSrc: imageSrc,
      });
    }

    localStorage.setItem("currentOrder", JSON.stringify(currentOrder));

    addToCart(currentOrder);

    let totalQuantity = 0;
    let totalPrice = 0;
    currentOrder.forEach((item) => {
      totalQuantity += item.quantity;
      totalPrice += item.totalPrice;
    });

    const quantityElement = document.querySelector(".view-cart-btn span");
    const priceElement = document.querySelector(".cart-price");

    if (quantityElement) {
      quantityElement.textContent = totalQuantity;
    }

    if (priceElement) {
      priceElement.textContent = `₱${totalPrice.toFixed(2)}`;
    }

    modal.remove();

    let successModal = document.createElement("div");
    successModal.classList.add("modal", "success-modal");

    successModal.innerHTML = `
            <div class="modal-contents success-content" style="width: 60%; max-width: 500px; padding: 30px; text-align: center; background-color: var(--primary-color); border-radius: 15px; box-shadow: 2px 2px 2px 2px var(--secondary-color);">
                <div class="success-icon" style="color: var(--secondary-color); font-size: 60px; margin-bottom: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h2 style="color: black; margin-bottom: 10px;">Added to Order!</h2>
                <p style="font-size: 18px; color: black; margin-bottom: 20px;">
                    ${quantity} × ${name} has been added to your order
                </p>
               
            </div>
        `;
    //timer of modal //scucess 1
    document.body.appendChild(successModal);

    const autoHideTimeout = setTimeout(() => {
      successModal.remove();
    }, 1000);

    successModal.querySelector(".continue-shopping");
  });

  modal.querySelector(".cancel-order").addEventListener("click", function () {
    modal.remove();
  });
}

function showOrderModal() {
  const orderSummaryModal = document.getElementById("orderSummaryModal");
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
  const imageSrc = "";
  if (currentOrder.length === 0) {
    showEmptyOrderModal();
    return;
  }

  const orderItemsContainer = orderSummaryModal.querySelector(
    ".modal-content > div:nth-child(2) > div"
  );

  orderItemsContainer.innerHTML = "";

  let subtotal = 0;
  currentOrder.forEach((item, index) => {
    subtotal += item.totalPrice;

    const orderItemDiv = document.createElement("div");
    orderItemDiv.className = "order-item";

    orderItemDiv.innerHTML = `
   <div class="order-item-image">
  <img 
   
    src="${item.imageSrc || "../images/default-item.png"}"
    alt="${item.name}" 
    class="item-image"
   
  />
  <button class="remove-item-btn">×</button>
</div>

    <div class="order-item-content">
        <h3 class="item-name">${item.name}</h3>
        <div class="price-per-item">₱${item.price.toFixed(2)} each</div>
    </div>
    <div class="order-item-controls">
        <div class="quantity-control">
            <button class="quantity-btn minus" data-index="${index}">
                <span class="minus-symbol">−</span>
            </button>
            <span class="quantity">${item.quantity}</span>
            <button class="quantity-btn plus" data-index="${index}">
                <span class="plus-symbol">+</span>
            </button>
        </div>
        <p class="item-total">₱${item.totalPrice.toFixed(2)}</p>
    </div>
`;

    orderItemsContainer.appendChild(orderItemDiv);

    const minusBtn = orderItemDiv.querySelector(".quantity-btn.minus");
    const plusBtn = orderItemDiv.querySelector(".quantity-btn.plus");
    const removeBtn = orderItemDiv.querySelector(".remove-item-btn");

    minusBtn.addEventListener("click", function () {
      updateItemQuantity(index, -1);
    });

    plusBtn.addEventListener("click", function () {
      updateItemQuantity(index, 1);
    });

    removeBtn.addEventListener("click", function () {
      removeOrderItem(index);
    });
  });

  const serviceCharge = subtotal * 0.1;
  const vat = subtotal * 0.12;
  const total = subtotal + serviceCharge + vat;

  const summarySection = orderSummaryModal.querySelector(
    ".modal-content > div:last-child"
  );

  const amountFields = summarySection.querySelectorAll(
    "div:first-child + div p"
  );
  amountFields[0].textContent = `₱${subtotal.toFixed(2)}`;
  amountFields[1].textContent = `₱${serviceCharge.toFixed(2)}`;
  amountFields[2].textContent = `₱${vat.toFixed(2)}`;

  const totalAmount = summarySection.querySelector("h3 + h3");
  totalAmount.textContent = `₱${total.toFixed(2)}`;

  orderSummaryModal.style.display = "flex";
}

function calculateTotals(order) {
  const subtotal = order.reduce(
    (sum, it) => sum + (Number(it.totalPrice) || 0),
    0
  );
  const service_charge = +(subtotal * 0.1);
  const vat = +(subtotal * 0.12);
  const total = +(subtotal + service_charge + vat);

  return {
    subtotal: +subtotal.toFixed(2),
    service_charge: +service_charge.toFixed(2),
    vat: +vat.toFixed(2),
    discount: 0,
    less_vat: 0,
    total: +total.toFixed(2),
  };
}

function saveTotalsToLocalStorage(totals) {
  localStorage.setItem("totals", JSON.stringify(totals));
}

function updateOrderFooter() {
  const totals = JSON.parse(localStorage.getItem("totals") || "{}");
  const showTotalEl = document.getElementById("showtotal");
  if (showTotalEl) {
    showTotalEl.textContent =
      "Total: ₱" + (totals.total != null ? totals.total.toFixed(2) : "0.00");
  }
}

function removeOrderItem(index) {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
  if (!currentOrder[index]) return;

  currentOrder.splice(index, 1);
  localStorage.setItem("currentOrder", JSON.stringify(currentOrder));

  const newTotals = calculateTotals(currentOrder);
  saveTotalsToLocalStorage(newTotals);
  updateOrderFooter();

  showOrderModal();
}

function updateItemQuantity(index, change) {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");

  if (!currentOrder[index]) return;

  if (change < 0 && currentOrder[index].quantity <= 1) {
    removeOrderItem(index);
    return;
  }

  currentOrder[index].quantity = Math.max(
    1,
    Number(currentOrder[index].quantity || 0) + change
  );

  currentOrder[index].totalPrice = +(
    Number(currentOrder[index].price) * currentOrder[index].quantity
  );

  localStorage.setItem("currentOrder", JSON.stringify(currentOrder));

  const totals = calculateTotals(currentOrder);
  saveTotalsToLocalStorage(totals);

  updateOrderFooter();

  showOrderModal();
}

function removeOrderItem(index) {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");

  if (index >= 0 && index < currentOrder.length) {
    currentOrder.splice(index, 1);

    localStorage.setItem("currentOrder", JSON.stringify(currentOrder));

    updateOrderFooter();

    if (currentOrder.length === 0) {
      closeOrderModal();
      return;
    }

    showOrderModal();
  }
}

function closeOrderModal() {
  const orderSummaryModal = document.getElementById("orderSummaryModal");
  orderSummaryModal.style.display = "none";
}

function updateOrderFooter() {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
  let totalQuantity = 0;
  let totalPrice = 0;

  currentOrder.forEach((item) => {
    totalQuantity += item.quantity;
    totalPrice += item.totalPrice;
  });

  const quantityElement = document.querySelector(".view-cart-btn span");
  const priceElement = document.querySelector(".cart-price");

  if (quantityElement) {
    quantityElement.textContent = totalQuantity;
  }

  if (priceElement) {
    priceElement.textContent = `₱${totalPrice.toFixed(2)}`;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  updateOrderFooter();

  const viewCartBtn = document.querySelector(".view-cart-btn");
  if (viewCartBtn) {
    viewCartBtn.addEventListener("click", showOrderModal);
  }

  const orderMoreBtn = document.querySelector(
    "#orderSummaryModal button:nth-last-child(2)"
  );
  if (orderMoreBtn) {
    orderMoreBtn.addEventListener("click", closeOrderModal);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
  let totalQuantity = 0;
  let totalPrice = 0;

  currentOrder.forEach((item) => {
    totalQuantity += item.quantity;
    totalPrice += item.totalPrice;
  });

  const quantityElement = document.querySelector(".view-cart-btn span");
  const priceElement = document.querySelector(".cart-price");

  if (quantityElement) {
    quantityElement.textContent = totalQuantity;
  }

  if (priceElement) {
    priceElement.textContent = `₱${totalPrice.toFixed(2)}`;
  }
});
