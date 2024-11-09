/**
 * Displays a "Great Choice!" message when the user selects a quantity of 70.
 */
function displayGreatChoiceMessage() {
  const quantityInput = document.querySelector('[name="sylius_shop_add_to_cart[cartItem][quantity]"]');

  if (!quantityInput) return;

  quantityInput.addEventListener('input', function() {
    const value = parseInt(quantityInput.value);

    if (isNaN(value)) return;

    if (value === 70) {
      alert('Great Choice!');
    }
  });
}

(function () {
  displayGreatChoiceMessage();
})();
