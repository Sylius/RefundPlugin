document.addEventListener('DOMContentLoaded', function () {
     const refundButtons = document.querySelectorAll('[data-refund]');
    const refundAllButton = document.querySelector('[data-refund-all]');
    const refundClearAllButton = document.querySelector('[data-refund-clear]');
    const refundInputs = document.querySelectorAll('[data-refund-input]');

    refundButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const refundValue = button.getAttribute('data-refund');
            const refundInput = button.closest('tr').querySelector('[data-refund-input]');
            if (refundInput) {
                refundInput.value = refundValue;
            }
        });
    });

    if (refundAllButton) {
        refundAllButton.addEventListener('click', function () {
            refundButtons.forEach(function (button) {
                if (!button.disabled) {
                    button.click();
                }
            });
        });
    }

    if (refundClearAllButton) {
        refundClearAllButton.addEventListener('click', function () {
            refundInputs.forEach(function (input) {
                input.value = '';
            });
        });
    }
});
