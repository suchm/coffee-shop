function setupSale() {
    const elements = {
        product: document.getElementById('product'),
        quantity: document.getElementById('quantity'),
        unitCost: document.getElementById('unit-cost'),
        sellingPriceOutput: document.querySelector('.selling-price-output'),
        sellingPriceSpinner: document.querySelector('.selling-price-spinner'),
        recordSaleBtn: document.getElementById('record-sale-btn'),
        errorMessage: document.getElementById('error-message'),
        salesTable: document.querySelector('table tbody'),
        salesTableElement: document.getElementById('sales-table'),
        noSalesMessage: document.getElementById('no-sales-message'),
    };

    let debounceTimeout = null;

    const isValidInteger = value => /^-?\d+$/.test(value);
    const isValidFloat = value => /^-?\d+(\.\d{1,2})?$/.test(value);
    const disableRecordSaleBtn = () => elements.recordSaleBtn.disabled = true;
    const enableRecordSaleBtn = () => elements.recordSaleBtn.disabled = false;

    const clearInputs = () => {
        elements.product.value = '';
        elements.quantity.value = '';
        elements.unitCost.value = '';
        elements.sellingPriceOutput.textContent = '';
    };

    const handleValidation = (product, quantity, unitCost) => {
        elements.sellingPriceSpinner.style.display = 'none';

        const validations = [
            { valid: isValidInteger(product), message: "Please select a product" },
            { valid: isValidInteger(quantity), message: "Quantity must be an integer" },
            { valid: isValidFloat(unitCost), message: "Unit cost must be a valid number with up to 2 decimal places." },
        ];

        for (const { valid, message } of validations) {
            if (!valid) {
                disableRecordSaleBtn();
                elements.errorMessage.textContent = message;
                elements.sellingPriceOutput.textContent = '';
                return false;
            }
        }

        elements.errorMessage.textContent = '';
        return true;
    };

    const handleFetchErrors = (response, data) => {
        elements.errorMessage.textContent = response.status === 422 && data.errors
            ? Object.values(data.errors).flat().join(' ')
            : (data.error || 'An unexpected error occurred.');
    };

    const postData = async (url, payload) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        return { response, data };
    };

    const getInputValues = () => ({
        product: elements.product.value.trim(),
        quantity: elements.quantity.value.trim(),
        unitCost: elements.unitCost.value.trim(),
    });

    const resetSellingPrice = () => {
        elements.sellingPriceSpinner.style.display = 'none';
        elements.sellingPriceOutput.textContent = '';
    };

    async function createSale(e) {
        e.preventDefault();
        disableRecordSaleBtn();

        const { product, quantity, unitCost } = getInputValues();

        if (!handleValidation(product, quantity, unitCost)) return;

        try {
            const { response, data } = await postData('/sales/create', {
                product_id: parseInt(product),
                quantity: parseInt(quantity),
                unit_cost: parseFloat(unitCost).toFixed(2),
            });

            if (!response.ok) {
                handleFetchErrors(response, data);
                return;
            }

            const newSaleRow = document.createElement('tr');
            newSaleRow.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">${data.product_name}</td>
                <td class="border border-gray-300 px-4 py-2">${data.quantity}</td>
                <td class="border border-gray-300 px-4 py-2">£${data.unit_cost.toFixed(2)}</td>
                <td class="border border-gray-300 px-4 py-2">£${data.selling_price.toFixed(2)}</td>
                <td class="border border-gray-300 px-4 py-2">${data.created_at}</td>
            `;
            elements.salesTable.insertBefore(newSaleRow, elements.salesTable.firstChild)

            if (elements.salesTableElement && elements.salesTableElement.classList.contains('hidden')) {
                elements.salesTableElement.classList.remove('hidden');
            }

            if (elements.noSalesMessage) {
                elements.noSalesMessage.remove();
            }

            clearInputs();
            resetSellingPrice();
        } catch (error) {
            elements.errorMessage.textContent = 'An unexpected error occurred.';
        }
    }

    async function calculateSellingPrice() {
        const { product, quantity, unitCost } = getInputValues();

        if (!handleValidation(product, quantity, unitCost)) {
            resetSellingPrice();
            return;
        }

        try {
            const { response, data } = await postData('/sales/calculate-selling-price', {
                product_id: parseInt(product),
                quantity: parseInt(quantity),
                unit_cost: parseFloat(unitCost).toFixed(2),
            });

            if (!response.ok) {
                handleFetchErrors(response, data);
                resetSellingPrice();
                return;
            }

            elements.sellingPriceSpinner.style.display = 'none';
            elements.errorMessage.textContent = '';
            elements.sellingPriceOutput.textContent = `£${data.selling_price.toFixed(2)}`;

        } catch (error) {
            elements.errorMessage.textContent = 'An unexpected error occurred.';
            resetSellingPrice();

        } finally {
            enableRecordSaleBtn(); // always re-enable button at the end
        }
    }

    const debounceCalculateSellingPrice = () => {
        const { product, quantity, unitCost } = getInputValues();

        elements.errorMessage.textContent = '';
        if (product && quantity && unitCost) {
            disableRecordSaleBtn();
            elements.sellingPriceOutput.textContent = '';
            elements.sellingPriceSpinner.style.display = 'block';
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(calculateSellingPrice, 1000);
        }
    };

    // When input fields change calculate selling price
    elements.product.addEventListener('change', debounceCalculateSellingPrice);
    elements.quantity.addEventListener('keyup', debounceCalculateSellingPrice);
    elements.unitCost.addEventListener('keyup', debounceCalculateSellingPrice);
    elements.recordSaleBtn.addEventListener('click', createSale);
}

export default setupSale;
