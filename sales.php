
<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
    include "db/dbConnection.php";
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<?php include "head.php" ?>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

    <?php include "top.php" ?>
        <!-- ========== App Menu ========== -->
      <?php include "left.php"; ?>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <div id="salesTableSection">
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                    <h4 class="mb-sm-0">Sales List</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Sales List</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-primary btn-label right rounded-pill" data-bs-toggle="modal" data-bs-target="#addSalesModal">
                                <i class="ri-add-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Add Sales
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="sales" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Sales Date</th>
                                                    <th>Client Name</th>
                                                    <th>Client Phone</th>
                                                    <th>Product</th>
                                                    <th>Total Amount</th>
                                                    <th>Refill Date</th>
                                                    <th>Current Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div>

                    <?php
                    $categories = [];
                    $result = mysqli_query($conn, "SELECT id, name FROM category WHERE status='Active'");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $categories[] = $row;
                    }
                    ?>

                    <!-- Add Sales Form Section -->
                    <div class="modal fade" id="addSalesModal" tabindex="-1" aria-labelledby="addSalesLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="addSalesForm" class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                                <input type="hidden" name="next_refill_date" id="next-refill-date">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Sales</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Sales Date -->
                                            <div class="col-md-6 mb-3">
                                                <label for="sales-date" class="form-label">Sales Date</label>
                                                <input type="date" class="form-control" name="date" id="sales-date" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                                                <div class="invalid-feedback">
                                                    Please select a sales date.
                                                </div>
                                            </div>

                                            <!-- Category Dropdown -->
                                            <div class="col-md-6 mb-3">
                                                <label for="category-id" class="form-label">Category</label>
                                                <select class="form-select" name="category_id" id="category-id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select a category.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Product Name -->
                                            <div class="col-md-6 mb-3">
                                                <label for="product-id" class="form-label">Product Name</label>
                                                <select class="form-select" name="product_id" id="product-id" required>
                                                    <option value="">Select Product</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select a product.
                                                </div>
                                            </div>

                                            <!-- Customer Rate -->
                                            <div class="col-md-6 mb-3">
                                                <label for="customer-rate" class="form-label">Rate</label>
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="text" class="form-control" name="customer_rate" id="customer-rate" onkeyup="formatINR(this)" required>
                                                    <div class="invalid-feedback">
                                                        Please enter customer price.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <!-- Customer Name -->
                                            <div class="col-md-6 mb-3">
                                                <label for="customer-name" class="form-label">Customer Name</label>
                                                <input type="text" class="form-control" name="customer_name" id="customer-name" required>
                                                <div class="invalid-feedback">
                                                    Please enter customer name.
                                                </div>
                                            </div>

                                            <!-- Customer Phone -->
                                            <div class="col-md-6 mb-3">
                                                <label for="customer-phone" class="form-label">Customer Phone</label>
                                                <input type="tel" pattern="[0-9]{10}" class="form-control" name="customer_phone" id="customer-phone" required>
                                                <div class="invalid-feedback">
                                                    Please enter customer phone.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Address -->
                                        <div class="mb-3">
                                            <label for="customer-address" class="form-label">Customer Address</label>
                                            <textarea class="form-control" name="customer_address" id="customer-address" rows="3"></textarea>
                                            <div class="invalid-feedback">
                                                Please enter customer address.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                                            <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success btn-label right rounded-pill" id="saveSalesBtn">
                                            <i class="ri-save-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div id="salesViewSection" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                    <h4 class="mb-sm-0">Sales View</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="sales.php">Sales List</a></li>
                                            <li class="breadcrumb-item active">Sales View</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-danger btn-label right rounded-pill" onclick="viewSalesTable()">
                                <i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Back to List
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <h5 class="card-title flex-grow-1 mb-0">🛒 Sales Details</h5>
                                            <div class="flex-shrink-0">
                                                <strong>Invoice No:</strong> <span class="badge bg-secondary-subtle text-secondary fs-6" id="viewInvoiceNo">Invoice</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="salesView" class="table table-bordered table-striped align-middle" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="col-1 text-center">#</th>
                                                    <th class="col-2 text-center">Category</th>
                                                    <th class="col-4 text-center">Product</th>
                                                    <th class="col-2 text-center">Rate</th>
                                                    <th class="col-1 text-center">Quantity</th>
                                                    <th class="col-2 text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="viewProductTable">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">📝 Notes</h5>
                                    </div>
                                    <div class="card-body">
                                        <p id="viewNotes">--</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 d-flex flex-column">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">🧑‍💼 <span id="viewClientType">Client</span> Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled vstack gap-2">
                                            <li class="d-flex justify-content-between mb-2"><strong>Name :</strong> <span id="viewClientName">--</span></li>
                                            <li class="d-flex justify-content-between mb-2"><strong>Date :</strong> <span id="viewSalesDate">--</span></li>
                                            <li class="d-flex justify-content-between"><strong>GST % :</strong> <span id="viewGstPercentage">--</span></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">💰 Amount Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex justify-content-between mb-2">
                                                <span>Subtotal</span>
                                                <span><span id="viewSubtotal">0.00</span></span>
                                            </li>
                                            <li class="d-flex justify-content-between mb-2">
                                                <span>Discount</span>
                                                <span class="text-danger">- <span id="viewDiscount">0.00</span></span>
                                            </li>
                                            <li class="d-flex justify-content-between mb-2">
                                                <span>New Subtotal</span>
                                                <span><span id="viewNewSubtotal">0.00</span></span>
                                            </li>
                                            <li class="d-flex justify-content-between mb-2">
                                                <span>GST</span>
                                                <span><span id="viewGstAmount">0.00</span></span>
                                            </li>
                                            <hr>
                                            <li class="d-flex justify-content-between fw-bold fs-5">
                                                <span>Total</span>
                                                <span class="text-success"><span id="viewTotal">0.00</span></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include "footer.php" ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/libs/glightbox/js/glightbox.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!--datatable js-->
    <script src="assets/js/datatable/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/datatable/dataTables.responsive.min.js"></script>

    <script src="assets/js/pages/datatables.init.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script src="assets/js/pages/form-validation.init.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="assets/js/pages/select2.init.js"></script>

    <script>
        let salesTable;

        $(document).ready(function () {
            $('#sales').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: 'action/sales/get_sales.php',
                    type: 'POST'
                },
                columns: [  
                    { data: 'serial', title: 'ID' },
                    { data: 'sales_date', title: 'Sales Date', className: 'text-center' },
                    { data: 'name', title: 'Client Name', className: 'text-center' },
                    { data: 'phone', title: 'Client Phone', className: 'text-center' },
                    { data: 'product_name', title: 'Product', className: 'text-center' },
                    { data: 'total_amount', title: 'Total Amount', className: 'text-center' },
                    { data: 'refill_date', title: 'Refill Date', className: 'text-center' },
                    { data: 'current_status', title: 'Current Status', className: 'text-center' },
                    { data: 'action', title: 'Action', orderable: false, searchable: false, className: 'text-center' }
                ],
                pageLength: 10,
                responsive: true
            });
        });

        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 100) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });

        function formatINR(el) {
            let val = el.value.replace(/,/g, '').replace(/\D/g, '');
            let last3 = val.slice(-3), rest = val.slice(0, -3);
            if (rest) last3 = ',' + last3;
            el.value = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + last3;
        }

        function viewSales(salesId) {
            $('#salesTableSection').hide();
            $('#salesViewSection').show();
            $.ajax({
                type: 'POST',
                url: 'action/sales/view_sales.php',
                data: { id: salesId },
                dataType: 'json',
                success: function (data) {
                    $('#viewInvoiceNo').text(data.invoice_no);
                    $('#viewClientType').text(data.client_type);
                    $('#viewClientName').text(data.client_name);
                    $('#viewSalesDate').text(data.date);
                    $('#viewGstPercentage').text(data.gst_percentage + '%');
                    $('#viewSubtotal').text(`₹ ${parseFloat(data.subtotal).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
                    $('#viewDiscount').text(`₹ ${parseFloat(data.discount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
                    $('#viewNewSubtotal').text(`₹ ${parseFloat(data.discounted_subtotal).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
                    $('#viewGstAmount').text(`₹ ${parseFloat(data.tax).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
                    $('#viewTotal').text(`₹ ${parseFloat(data.total).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
                    $('#viewNotes').text(data.notes || '--');

                    if ($.fn.DataTable.isDataTable('#salesView')) {
                        $('#salesView').DataTable().clear().destroy();
                    }

                    const rows = data.products.map((p, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${p.category_name || '-'}</td>
                            <td>${p.product_name || '-'}</td>
                            <td class="text-end">₹ ${parseFloat(p.rate).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                            <td class="text-center">${p.qty}</td>
                            <td class="text-end">₹ ${parseFloat(p.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    `);
                    $('#viewProductTable').html(rows.join(''));

                    $('#salesView').DataTable({
                        paging: false,
                        searching: false,
                        info: false,
                        ordering: false,
                        responsive: true
                    });
                },
                error: function () {
                    Swal.fire('Error', 'Unable to load sales details', 'error');
                }
            });
        }

        function editSales(salesId) {
            $('#salesTableSection').hide();
            $('#addSalesSection').show();

            const $form = $('#invoice_form');
            $form[0].reset();
            $form.removeClass('was-validated');
            $('.js-example-basic-single').val('').trigger('change');
            $('#editSalesId').val(salesId);
            $('#newlink').html('');

            $.ajax({
                type: 'POST',
                url: 'action/sales/fetch_sales.php',
                data: { id: salesId },
                dataType: 'json',
                success: function (data) {
                    $('#clientTypeSelect').val(data.client_type).trigger('change');
                    $('#date-field').val(data.date);
                    $('#gst-percentage').val(data.gst_percentage);
                    const productCount = data.products.length;
                    let completedCount = 0;
                    $('#newlink').html('');
                    productIndex = 0;

                    data.products.forEach((product, idx) => {
                        new_link(); 
                        const rowId = productIndex;
                        $(`#productCategory-${rowId}`).val(product.category_id).trigger('change');
                        const interval = setInterval(() => {
                            const $productDropdown = $(`#productName-${rowId}`);
                            if ($productDropdown.find(`option[value="${product.product_id}"]`).length) {
                                $productDropdown.val(product.product_id).trigger('change');
                                $(`#productRate-${rowId}`).val(product.rate);
                                $(`#product-qty-${rowId}`).val(product.qty);
                                $(`#productPrice-${rowId}`).val(product.amount);
                                clearInterval(interval);
                                completedCount++;

                                if (completedCount === productCount) {
                                    setTimeout(() => {
                                        $('#cart-discount').val(data.discount);
                                        $('#cart-subtotal').val(data.subtotal);
                                        $('#cart-new-subtotal').val(data.discounted_subtotal);
                                        $('#cart-tax').val(data.tax);
                                        $('#cart-total').val(data.total);
                                        $('#exampleFormControlTextarea1').val(data.notes);
                                        calculateCartTotals()
                                    }, 100); 
                                }
                            }
                        }, 100);
                    }); 
                    const clientInterval = setInterval(() => {
                        if ($('#clientSelect option[value="' + data.client_id + '"]').length) {
                            $('#clientSelect').val(data.client_id).trigger('change');
                            clearInterval(clientInterval);
                        }
                    }, 100); 
                },
                error: function () {
                    Swal.fire('Error', 'Failed to load sales data', 'error');
                    showSalesTable();
                }
            });
        }

        function deleteSales(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the sales!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: 'action/sales/delete_sales.php',
                        data: { id: id },
                        success: function (response) {
                            let icon = 'success', title = 'Deleted!';
                            if (response.includes("Failed")) {
                                icon = 'error';
                                title = 'Error';
                            }
                            Swal.fire({
                                icon: icon,
                                title: title,
                                text: response,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#sales').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }

    </script>

    <script>
        let productDetails = {};

        $('#category-id').on('change', function () {
            const categoryId = $(this).val();
            $('#product-id').html('<option value="">Loading...</option>');
            $('#customer-rate').val('');
            $('#next-refill-date').val('');

            if (categoryId) {
                $.ajax({
                    url: 'action/sales/fetch_products.php',
                    method: 'POST',
                    data: { category_id: categoryId },
                    dataType: 'json',
                    success: function (response) {
                        let options = '<option value="">Select Product</option>';
                        productDetails = {};
                        response.forEach(p => {
                            options += `<option value="${p.id}">${p.name}</option>`;
                            productDetails[p.id] = {
                                rate: p.rate,
                                refill_duration: p.refill_duration
                            };
                        });
                        $('#product-id').html(options);
                    }
                });
            } else {
                $('#product-id').html('<option value="">Select Product</option>');
            }
        });

        $('#product-id').on('change', function () {
            const selectedId = $(this).val();
            if (productDetails[selectedId]) {
                $('#customer-rate').val(productDetails[selectedId].rate);
                formatINR($('#customer-rate')[0]);
                updateNextRefillDate();
            } else {
                $('#customer-rate').val('');
                $('#next-refill-date').val('');
            }
        });

        $('#sales-date').on('change', updateNextRefillDate);

        function updateNextRefillDate() {
            const selectedId = $('#product-id').val();
            const saleDate = $('#sales-date').val();
            if (selectedId && saleDate && productDetails[selectedId]) {
                const duration = parseInt(productDetails[selectedId].refill_duration);
                const sale = new Date(saleDate);
                sale.setDate(sale.getDate() + duration);
                const refillDate = sale.toISOString().split('T')[0];
                $('#next-refill-date').val(refillDate);
            }
            else {
                $('#next-refill-date').val('');
            }
        }
    </script>

    <script>
        $(document).ready(function () {

            $('#addSalesModal').on('show.bs.modal', function () {
                const form = $('#addSalesForm')[0];
                form.reset();
                $('#next-refill-date').val('');
                form.classList.remove('was-validated');
            });

                $('#addSalesForm').submit(function (e) {
                    e.preventDefault();

                    $(this).find('input[type="text"], textarea').each(function () {
                        this.value = this.value.trim();
                    });

                    if (!this.checkValidity()) {
                        this.classList.add('was-validated');
                        return;
                    }

                    $(this).find('input[name="customer_rate"]').each(function () {
                        this.value = this.value.replace(/,/g, '');
                    });

                    const $submitBtn = $('#saveSalesBtn'); 
                    $submitBtn.prop('disabled', true);

                    let formData = new FormData(this);

                    $.ajax({
                        type: 'POST',
                        url: 'action/sales/add_sales.php',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            let icon = 'success', title = 'Success!', reload = true;

                            if (res.status === 'warning') {
                                icon = 'warning';
                                title = 'Warning!';
                                reload = false;
                            } else if (res.status === 'error') {
                                icon = 'error';
                                title = 'Error!';
                                reload = false;
                            }

                            Swal.fire({
                                icon: icon,
                                title: title,
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            bootstrap.Modal.getInstance($('#addSalesModal')[0]).hide();

                            if (reload) {
                                $('#sales').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Request Failed!',
                                text: 'Unable to reach the server.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        complete: function () {
                            $submitBtn.prop('disabled', false); 
                        }
                    });
                });

            $('#editProductForm').submit(function (e) {
                e.preventDefault();

                const form = this;
                $(form).find('input[type="text"], textarea').each(function () {
                    this.value = this.value.trim();
                });
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }
                $(form).find('input[name="customer_rate"]').each(function () {
                    this.value = this.value.replace(/,/g, '');
                });
                $('#updateProductBtn').prop('disabled', true);

                const formData = new FormData(form);

                $.ajax({
                    type: 'POST',
                    url: 'action/product/update_product.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        let icon = 'success', title = 'Success!', reload = true;

                        if (res.includes('already exists')) {
                            icon = 'warning';
                            title = 'Duplicate!';
                            reload = false;
                        } else if (res.includes('Failed') || res.includes('required')) {
                            icon = 'error';
                            title = 'Error!';
                            reload = false;
                        }

                        Swal.fire({ icon, title, text: res, timer: 2000, showConfirmButton: false });
                        bootstrap.Modal.getInstance($('#editProductModal')[0]).hide();

                        if (reload) {
                            $('#product').DataTable().ajax.reload(null, false);
                        }
                    },
                    complete: function () {
                        $('#updateProductBtn').prop('disabled', false);
                    }
                });
            });
        });

    </script>

</body>

</html>