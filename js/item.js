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
        alert("Error generating reference number. Please try again.");
      });
    return;
  }

  const cartItems = currentOrder.map((item) => ({
    item_code: item.itemCode,
    description: item.name,
    quantity: item.quantity,
    price: item.price,
    total: item.totalPrice,
    taxable: true,
  }));

  const body = {
    ReferenceNo: referenceNo,
    cart: cartItems,
  };

  const options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
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
