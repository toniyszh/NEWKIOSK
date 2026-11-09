function addToCart(currentOrder) {
  let referenceNo = localStorage.getItem("referenceNo");
  if (!referenceNo) {
    fetch("api/generateRefNo.php")
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          console.error("Failed to generate reference number:", data.message);
          return;
        }
        referenceNo = data.data.referenceNo;
        localStorage.setItem("referenceNo", referenceNo);
        addToCart(currentOrder);
      })
      .catch((error) => {
        console.error("Error generating reference number:", error);
        alert("Reload or Restart this app to start a new order.");
      });
    return;
  }

  const cartItems = currentOrder.flatMap((item) => {
    const parent = {
      item_code: item.itemCode,
      description: item.name,
      quantity: item.quantity,
      price: item.price,
      total: item.totalPrice,
      taxable: true,
      is_modifier: false,

      special_instruction: item.specialInstruction || "",
      cust_instruction: item.custInstruction || "",
    };

    const modifiers = (item.modifiers || []).map((mod) => ({
      item_code: mod.itemOption ?? "",
      description: mod.name,
      quantity: item.quantity,
      price: 0,
      total: 0,
      taxable: false,
      is_modifier: true,
      special_instruction: "",
    }));

    return [parent, ...modifiers];
  });
  console.log("Cart payload:", JSON.stringify(cartItems, null, 2));

  const orderType = localStorage.getItem("orderType") || "dine-in";

  const body = {
    ReferenceNo: referenceNo,
    cart: cartItems,
    orderType: orderType,
  };

  const options = {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
  };

  fetch("../api/addToCart.php", options)
    .then((result) => result.json())
    .then((data) => {
      if (!data.success) {
        console.error("Failed to add to cart:", data.message);
        alert("Failed to save order: " + data.message);
        return;
      }
      localStorage.setItem("totals", JSON.stringify(data.data));
    })
    .catch((err) => {
      console.error("error:", err);
      alert("Error saving order. Please try again.");
    });
}
