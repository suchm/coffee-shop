function setupSale() {
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit-cost');
    const sellingPriceOutput = document.querySelector('.selling-price-output');
    const sellingPriceSpinner = document.querySelector('.selling-price-spinner');
    const recordSaleBtn = document.getElementById('record-sale-btn');
    const errorMessage = document.getElementById('error-message');
    const salesTable = document.querySelector('table tbody');

    let debounceTimeout = null;

    const isValidInteger = (value) => /^-?\d+$/.test(value);
    const isValidFloat = (value) => /^-?\d+(\.\d{1,2})?$/.test(value);

    const clearInputs = () => {
        quantityInput.value = '';
        unitCostInput.value = '';
        sellingPriceOutput.textContent = '';
    };

    const handleValidation = (quantity, unitCost, clearSellingPrice = false) => {
        sellingPriceSpinner.style.display = 'none';
        if (!isValidInteger(quantity)) {
            errorMessage.textContent = "Quantity must be a valid integer.";
            if (clearSellingPrice) sellingPriceOutput.textContent = '';
            return false;
        }
        if (!isValidFloat(unitCost)) {
            errorMessage.textContent = "Unit cost must be a valid number with up to 2 decimal places.";
            if (clearSellingPrice) sellingPriceOutput.textContent = '';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    };

    const handleFetchErrors = (response, data) => {
        if (response.status === 422 && data.errors) {
            errorMessage.textContent = Object.values(data.errors).flat().join(' ');
        } else if (data.error) {
            errorMessage.textContent = data.error;
        } else {
            errorMessage.textContent = 'An unexpected error occurred.';
        }
    };

    const postData = async (url, payload) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        return { response, data };
    };

    async function createSale(e) {
        e.preventDefault();

        const quantity = quantityInput.value.trim();
        const unitCost = unitCostInput.value.trim();

        if (!handleValidation(quantity, unitCost)) return;

        try {
            const { response, data } = await postData('/sales/create', {
                quantity: parseInt(quantity),
                unit_cost: parseFloat(unitCost).toFixed(2)
            });

            if (!response.ok) {
                handleFetchErrors(response, data);
                return;
            }

            const newSaleRow = document.createElement('tr');
            newSaleRow.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">${data.quantity}</td>
                <td class="border border-gray-300 px-4 py-2">£${data.unit_cost.toFixed(2)}</td>
                <td class="border border-gray-300 px-4 py-2">£${data.selling_price.toFixed(2)}</td>
            `;
            salesTable.insertBefore(newSaleRow, salesTable.firstChild);

            clearInputs();
        } catch (error) {
            errorMessage.textContent = 'An unexpected error occurred.';
        }
    }

    async function calculateSellingPrice() {
        const quantity = quantityInput.value.trim();
        const unitCost = unitCostInput.value.trim();

        if (!handleValidation(quantity, unitCost, true)) return;

        try {
            const { response, data } = await postData('/sales/calculate-selling-price', {
                quantity: parseInt(quantity),
                unit_cost: parseFloat(unitCost).toFixed(2)
            });

            if (!response.ok) {
                handleFetchErrors(response, data);
                return;
            }

            sellingPriceSpinner.style.display = 'none';
            sellingPriceOutput.textContent = `£${data.selling_price.toFixed(2)}`;

        } catch (error) {
            sellingPriceSpinner.style.display = 'none';
            sellingPriceOutput.textContent = '';
            errorMessage.textContent = 'An unexpected error occurred.';
        }
    }

    const debounceCalculateSellingPrice = () => {
        // Show spinner while loading
        sellingPriceOutput.textContent = '';
        errorMessage.textContent = '';
        sellingPriceSpinner.style.display = 'block';
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(calculateSellingPrice, 1000);
    };

    quantityInput.addEventListener('keyup', debounceCalculateSellingPrice);
    unitCostInput.addEventListener('keyup', debounceCalculateSellingPrice);
    recordSaleBtn.addEventListener('click', createSale);
}

export default setupSale;
