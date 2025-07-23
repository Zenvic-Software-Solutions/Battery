let categoryOptionsHTML = '';

$(document).ready(function () {
    $.ajax({
        url: 'action/purchase/fetch_category.php',
        type: 'GET',
        dataType: 'json',
        success: function (categories) {
            categoryOptionsHTML = '<option value="">Select Category</option>';
            categories.forEach(cat => {
                categoryOptionsHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
            $('#productCategory-1').html(categoryOptionsHTML).trigger('change');
        }
    });
});

let productIndex = 1;

function new_link() {
    productIndex++;

    const newRow = `
    <tr id="${productIndex}" class="product">
        <th scope="row" class="product-id">${productIndex}</th>

        <td class="text-start">
            <div class="mb-2">
                <select class="js-example-basic-single" id="productCategory-${productIndex}" onchange="loadProductsByCategory(${productIndex})" required>
                    ${categoryOptionsHTML} 
                </select>
                <div class="invalid-feedback">Please select a category</div>
            </div>
        </td>

        <td>
            <div class="mb-2">
                <select class="js-example-basic-single" id="productName-${productIndex}" onchange="loadProductPrice(${productIndex})" required>
                    <option value="">Select Product</option>
                </select>
                <div class="invalid-feedback">Please select a product</div>
            </div>
        </td>

        <td>
            <input type="number" class="form-control product-price bg-light border-0" id="productRate-${productIndex}" min="0" step="0.01" placeholder="0.00" required />
            <div class="invalid-feedback">Please enter a rate</div>
        </td>

        <td>
            <div class="input-step">
                <button type="button" class="minus" id="minus-${productIndex}">–</button>
                <input type="number" class="product-quantity" id="product-qty-${productIndex}" min="1" value="0" step="1" required
                       oninput="this.value = this.value < 1 ? 1 : this.value" />
                <button type="button" class="plus" id="plus-${productIndex}">+</button>
                <div class="invalid-feedback">Quantity is required</div>
            </div>
        </td>

        <td class="text-end">
            <input type="text" class="form-control bg-light border-0 product-line-price" id="productPrice-${productIndex}" placeholder="₹ 0.00" readonly />
        </td>

        <td class="product-removal">
            <a href="javascript:void(0)" class="btn btn-danger" onclick="remove_row(${productIndex})">Delete</a>
        </td>
    </tr>
    `;

    $('#newlink').append(newRow);

    $(`#productCategory-${productIndex}, #productName-${productIndex}`).select2();
}

function remove_row(id) {
    $(`#${id}`).remove();
    $('#newlink .product').each(function (index) {
        const newId = index + 1;
        const $row = $(this).attr('id', newId);
        $row.find('.product-id').text(newId);
        const elements = [
            { prefix: 'productCategory', attr: 'onchange', handler: `loadProductsByCategory(${newId})` },
            { prefix: 'productName', attr: 'onchange', handler: `loadProductPrice(${newId})` },
            { prefix: 'productRate' },
            { prefix: 'product-qty' },
            { prefix: 'plus' },
            { prefix: 'minus' },
            { prefix: 'productPrice' }
        ];
        elements.forEach(({ prefix, attr, handler }) => {
            const $el = $row.find(`[id^="${prefix}-"]`);
            $el.attr('id', `${prefix}-${newId}`);
            if (attr && handler) $el.attr(attr, handler);
        });
        $row.find('.product-removal a').attr('onclick', `remove_row(${newId})`);
    });
    productIndex = $('#newlink .product').length;
    $('.js-example-basic-single').select2();
    calculateCartTotals();
}

function loadProductsByCategory(rowId) {
    const categoryId = $(`#productCategory-${rowId}`).val();
    const clientType = $('#clientTypeSelect').length > 0 ? $('#clientTypeSelect').val() : 'Retailer';

    if (categoryId === '') {
        $(`#productName-${rowId}`).html('<option value="">Select Product</option>');
        $(`#productRate-${rowId}`).val('');
        $(`#productPrice-${rowId}`).val('₹ 0.00');
        $(`#product-qty-${rowId}`).val(0);
        calculateCartTotals();
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'action/purchase/fetch_products.php',
        data: { category_id: categoryId, client_type: clientType },
        success: function (response) {
            $(`#productName-${rowId}`).html(response);
            $(`#productRate-${rowId}`).val('');
            $(`#productPrice-${rowId}`).val('₹ 0.00');
            $(`#product-qty-${rowId}`).val(0);
            calculateCartTotals();
        },
        error: function () {
            Swal.fire('Error', 'Failed to load products.', 'error');
        }
    });
}

function loadProductPrice(rowId) {
    const $product = $(`#productName-${rowId}`);
    const price = $product.find('option:selected').data('price');

    if (price !== undefined) {
        $(`#productRate-${rowId}`).val(parseFloat(price).toFixed(2));
        const $qty = $(`#product-qty-${rowId}`);
        if (+$qty.val() < 1 || isNaN(+$qty.val())) $qty.val(1);
        const total = (parseInt($qty.val(), 10) || 0) * price;
        $(`#productPrice-${rowId}`).val(`₹ ${total.toFixed(2)}`);
        calculateCartTotals();
    } else {
        $(`#productRate-${rowId}`).val('');
        $(`#productPrice-${rowId}`).val('₹ 0.00');
        $(`#product-qty-${rowId}`).val(0);
        calculateCartTotals();
    }
}

function calculateTotal(rowId) {
    const qty = +$(`#product-qty-${rowId}`).val() || 1;
    const rate = +$(`#productRate-${rowId}`).val() || 0;
    const total = (qty * rate).toFixed(2);
    $(`#productPrice-${rowId}`).val(`₹ ${total}`);
}

$(document).on('click', '.plus', function () {
    const rowId = this.id.split('-')[1];
    const $qty = $(`#product-qty-${rowId}`);
    let qty = parseInt($qty.val(), 10) || 0;
    qty++;
    $qty.val(qty);
    calculateTotal(rowId);
    calculateCartTotals();
});

$(document).on('click', '.minus', function () {
    const rowId = this.id.split('-')[1];
    const $qty = $(`#product-qty-${rowId}`);
    let qty = parseInt($qty.val(), 10) || 1;
    if (qty > 1) qty--;
    else qty = 1;
    $qty.val(qty);
    calculateTotal(rowId);
    calculateCartTotals();
});

$(document).on('input', '.product-quantity, .product-price', function () {
    const rowId = $(this).closest('tr').attr('id');
    const $qty = $(`#product-qty-${rowId}`);
    if (+$qty.val() < 1 || isNaN(+$qty.val())) $qty.val(1);
    calculateTotal(rowId);
    calculateCartTotals();
});

$(document).on('input', '#cart-discount, #gst-percentage', function () {
    calculateCartTotals();
});

function calculateCartTotals() {
    let subtotal = 0;

    $('.product-line-price').each(function () {
        const value = parseFloat($(this).val().replace(/[^\d.-]/g, '')) || 0;
        subtotal += value;
    });

    let discount = parseFloat($('#cart-discount').val().replace(/[^\d.-]/g, '')) || 0;

    if (discount > subtotal) discount = subtotal;

    const discountedSubtotal = subtotal - discount;

    const gstRate = parseFloat($('#gst-percentage').val()) || 0;
    const tax = (discountedSubtotal * gstRate) / 100;

    const total = discountedSubtotal + tax;

    $('#cart-subtotal').val(`₹ ${subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
    $('#cart-discount').val(`₹ ${discount.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
    $('#cart-new-subtotal').val(`₹ ${discountedSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
    $('#cart-tax').val(`₹ ${tax.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
    $('#cart-total').val(`₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
    $('#gst-label').text(`Estimated Tax (${gstRate}%)`);
}
