
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
                                <h4 class="mb-sm-0">Category List</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Category List</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-primary btn-label right rounded-pill" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="ri-add-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                            Add Category
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="category" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Category Name</th>
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

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="addCategoryForm" class="row g-3 needs-validation" novalidate>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add-name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" name="name" id="add-name" required>
                                <div class="invalid-feedback">
                                    Please enter a category name.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                                <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-label right rounded-pill">
                                <i class="ri-save-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editCategoryForm" class="row g-3 needs-validation" novalidate>
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" name="name" id="edit-name" required>
                            <div class="invalid-feedback">
                                Please enter a category name.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                        <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-warning btn-label right rounded-pill">
                        <i class="ri-edit-2-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                        Update
                    </button>
                    </div>
                </div>
                </form>
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
        let categoryTable;

        $(document).ready(function () {
            categoryTable = $('#category').DataTable({
                ajax: 'action/category/get_category.php',
                columns: [
                    { title: "ID" },
                    { title: "Category Name" },
                    { title: "Action", orderable: false, searchable: false }
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

    </script>

    <script>
        $(document).ready(function () {

            $('#addCategoryModal').on('show.bs.modal', function () {
                const form = $('#addCategoryForm')[0];
                form.reset(); 
                form.classList.remove('was-validated'); 
            });

            $('#addCategoryForm').submit(function (e) {
                e.preventDefault();

                $(this).find('input[type="text"]').each(function () {
                    this.value = this.value.trim();
                });

                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }

                $.post('action/category/add_category.php', $(this).serialize(), function (res) {
                    let icon = 'success';
                    let title = 'Success!';
                    let reload = true;

                    if (res.includes('already exists')) {
                        icon = 'warning';
                        title = 'Duplicate!';
                        reload = false; 
                    } else if (res.includes('required') || res.includes('Failed')) {
                        icon = 'error';
                        title = 'Error!';
                        reload = false;
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: res,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance($('#addCategoryModal')[0]).hide();

                    if (reload) {
                        categoryTable.ajax.reload(null, false); 
                    }
                });
            });

            $(document).on('click', '.editBtn', function () {
                $('#edit-id').val($(this).data('id'));
                $('#edit-name').val($(this).data('name')).removeClass('is-invalid is-valid');

                const form = $('#editCategoryForm')[0];
                form.classList.remove('was-validated');

                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            });

            $('#editCategoryForm').submit(function (e) {
                e.preventDefault();

                $(this).find('input[type="text"]').each(function () {
                    this.value = this.value.trim();
                });

                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }

                $.post('action/category/update_category.php', $(this).serialize(), function (res) {
                    let icon = 'success';
                    let title = 'Updated!';
                    let reload = true;

                    // Handle specific responses
                    if (res.includes('already exists')) {
                        icon = 'warning';
                        title = 'Duplicate!';
                        reload = false;
                    } else if (res.includes('Error')) {
                        icon = 'error';
                        title = 'Error!';
                        reload = false;
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: res,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance($('#editCategoryModal')[0]).hide();

                    if (reload) {
                        categoryTable.ajax.reload(null, false);
                    }
                });
            });
        });

        $(document).on('click', '.deleteBtn', function () {
            const categoryId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('action/category/delete_category.php', { id: categoryId }, function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Category Deleted',
                            text: res,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        categoryTable.ajax.reload(null, false);
                    });
                }
            });
        });
    </script>

</body>

</html>