
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

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0">Product List</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Product List</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-primary btn-label right rounded-pill" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="ri-add-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                            Add Product
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="product" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Refill Duration</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->

                
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include "footer.php" ?>
        </div>
        <!-- end main content-->

        <?php
        $categories = [];
        $result = mysqli_query($conn, "SELECT id, name FROM category WHERE status='Active'");
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        ?>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="addProductForm" class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Category Dropdown -->
                            <div class="mb-3">
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

                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="add-name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" id="add-name" required>
                                <div class="invalid-feedback">
                                    Please enter a product name.
                                </div>
                            </div>

                            <!-- Customer Rate -->
                            <div class="mb-3">
                                <label for="customer-rate" class="form-label">Rate</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">₹</span>
                                    <input type="text" class="form-control" name="customer_rate" id="customer-rate" onkeyup="formatINR(this)" inputmode="decimal" required>
                                    <div class="invalid-feedback">
                                        Please enter customer price.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Refill Duration -->
                            <div class="mb-3">
                                <label for="refill-duration" class="form-label">Refill Duration (Days)</label>
                                <input type="number" class="form-control" name="refill_duration" id="refill-duration" min="1" required>
                                <div class="invalid-feedback">
                                    Please enter refill duration.
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Product Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                <div class="invalid-feedback">
                                    Please enter product description.
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                                <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-label right rounded-pill" id="saveProductBtn">
                                <i class="ri-save-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editProductForm" class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Category Dropdown -->
                            <div class="mb-3">
                                <label for="edit-category-id" class="form-label">Category</label>
                                <select class="form-select" name="category_id" id="edit-category-id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>

                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" id="edit-name" required>
                                <div class="invalid-feedback">Please enter a product name.</div>
                            </div>

                            <!-- Customer Rate -->
                            <div class="mb-3">
                                <label for="edit-customer-rate" class="form-label">Rate</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">₹</span>
                                    <input type="text" class="form-control" name="customer_rate" id="edit-customer-rate" onkeyup="formatINR(this)" inputmode="decimal" required>
                                    <div class="invalid-feedback">Please enter customer price.</div>
                                </div>
                            </div>

                            <!-- Refill Duration -->
                            <div class="mb-3">
                                <label for="edit-refill-duration" class="form-label">Refill Duration (in days)</label>
                                <input type="number" class="form-control" name="refill_duration" id="edit-refill-duration" min="1" required>
                                <div class="invalid-feedback">Please enter refill duration.</div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Product Description</label>
                                <textarea class="form-control" name="description" id="edit-description" rows="3"></textarea>
                                <div class="invalid-feedback">Please enter product description.</div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                                <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning btn-label right rounded-pill" id="updateProductBtn">
                                <i class="ri-edit-2-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Product Modal -->
        <div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-4">View Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row mb-3 align-items-center fs-5">
                            <div class="col-5 fw-semibold">
                                <i class="mdi mdi-tag-outline me-1 text-primary"></i> Product Name
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-6" id="view-name"></div>
                        </div>

                        <div class="row mb-3 align-items-center fs-5">
                            <div class="col-5 fw-semibold">
                                <i class="mdi mdi-shape me-1 text-primary"></i> Category
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-6" id="view-category"></div>
                        </div>

                        <div class="row mb-3 align-items-center fs-5">
                            <div class="col-5 fw-semibold">
                                <i class="mdi mdi-currency-inr me-1 text-success"></i> Customer Rate
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-6 fw-bold text-success" id="view-customer-rate"></div>
                        </div>

                        <div class="row mb-3 align-items-center fs-5">
                            <div class="col-5 fw-semibold">
                                <i class="mdi mdi-timer-sand me-1 text-warning"></i> Refill Duration
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-6 text-danger" id="view-refill-duration"></div>
                        </div>

                        <div class="row mb-3 align-items-center fs-5">
                            <div class="col-5 fw-semibold">
                                <i class="mdi mdi-text-box-outline me-1 text-primary"></i> Description
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-6" id="view-description"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

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

    <script>
        let productTable;

        $(document).ready(function () {
            $('#product').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: 'action/product/get_product.php',
                    type: 'POST'
                },
                columns: [  
                    { data: 'serial', title: 'ID' },
                    { data: 'name', title: 'Product Name' },
                    { data: 'category_name', title: 'Category' },
                    { data: 'customer_rate', title: 'Price', className: 'text-center'  },
                    { data: 'refill_duration', title: 'Refill Duration', className: 'text-center'  },
                    { data: 'action', title: 'Action', orderable: false, searchable: false }
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

        function viewProduct(id) {
            $.ajax({
                url: 'action/product/view_product.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        $('#view-name').text(data.name);
                        $('#view-category').text(data.category_name);
                        $('#view-customer-rate').text('₹ ' + parseFloat(data.rate).toLocaleString('en-IN'));
                        $('#view-refill-duration').text(data.refill_duration + ' days');
                        $('#view-description').text(data.description || '-');
                        // Show the modal
                        new bootstrap.Modal(document.getElementById('viewProductModal')).show();
                    } else {
                        Swal.fire("Error", "Product not found!", "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Failed to fetch product data.", "error");
                }
            });
        }

        function editProduct(id) {
            $.ajax({
                type: 'POST',
                url: 'action/product/edit_product.php',
                data: { id },
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        const form = $('#editProductForm')[0];
                        form.classList.remove('was-validated');
                        form.reset();
                        $('#edit-id').val(id);
                        $('#edit-name').val(data.name);
                        $('#edit-customer-rate').val(data.rate);
                        $('#edit-refill-duration').val(data.refill_duration);
                        $('#edit-description').val(data.description);
                        formatINR($('#edit-customer-rate')[0]);
                        
                        $('#editProductModal').modal('show');
                        $('#edit-category-id').val(data.cat_id).trigger('change');
                    } else {
                        Swal.fire('Error', 'Product not found.', 'error');
                    }
                }
            });
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the product!",
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
                        url: 'action/product/delete_product.php',
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

                            $('#product').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }

    </script>

    <script>
        $(document).ready(function () {

            $('#addProductModal').on('show.bs.modal', function () {
                const form = $('#addProductForm')[0];
                form.reset(); 
                form.classList.remove('was-validated'); 
            });

                $('#addProductForm').submit(function (e) {
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

                    const $submitBtn = $('#saveProductBtn'); 
                    $submitBtn.prop('disabled', true);

                    let formData = new FormData(this);

                    $.ajax({
                        type: 'POST',
                        url: 'action/product/add_product.php',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            let icon = 'success', title = 'Success!', reload = true;

                            if (res.includes('already exists')) {
                                icon = 'warning';
                                title = 'Duplicate!';
                                reload = false;
                            } else if (res.includes('required') || res.includes('Failed')) {
                                icon = 'error';
                                title = 'Error!';
                                reload = false;
                            }

                            Swal.fire({ icon, title, text: res, timer: 2000, showConfirmButton: false });
                            bootstrap.Modal.getInstance($('#addProductModal')[0]).hide();

                            if (reload) {
                                $('#product').DataTable().ajax.reload(null, false);
                            }
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