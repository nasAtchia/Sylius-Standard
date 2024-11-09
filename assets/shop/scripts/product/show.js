(function () {
  const quantityInput = document.querySelector('[name="sylius_shop_add_to_cart[cartItem][quantity]"]');

  if (!quantityInput) return;

  quantityInput.addEventListener('input', function() {
    const value = parseInt(quantityInput.value);

    if (isNaN(value)) return;

    // Displays a "Great Choice!" message when the user selects a quantity of 70.
    if (value === 70) {
      alert('Great Choice!');
    }
  });
})();
