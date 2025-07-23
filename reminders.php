
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
                                <h4 class="mb-sm-0">Refill List</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Refill List</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="refill" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="col-1 text-center">ID</th>
                                                <th class="col-1 text-center">Days Left</th>
                                                <th class="col-2 text-center">Client Name</th>
                                                <th class="col-2 text-center">Client Phone</th>
                                                <th class="col-3 text-center">Client Address</th>
                                                <th class="col-2 text-center">Product Name</th>
                                                <th class="col-1 text-center">Action</th>
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

        <!-- Update Refill Date Modal -->
        <div class="modal fade" id="refillDateModal" tabindex="-1" aria-labelledby="refillDateLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="refillDateForm" class="row g-3 needs-validation" novalidate>
                    <input type="hidden" name="reminder_id" id="reminder_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Refill Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="refill-date" class="form-label">Refill Date</label>
                                <input type="date" class="form-control" name="refill_date" id="refill-date" max="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a refill date.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="refill-amount" class="form-label">Refill Amount</label>
                                <input type="number" class="form-control" name="refill_amount" id="refill-amount" min="0" placeholder="Enter refill amount" required>
                                <div class="invalid-feedback">
                                    Please enter a refill amount.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="refill-notes" class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" id="refill-notes" rows="3" placeholder="Enter any notes or details about the refill"></textarea>
                                <div class="invalid-feedback">
                                    Please enter some notes.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-label right rounded-pill" data-bs-dismiss="modal">
                                <i class="ri-close-circle-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-label right rounded-pill">
                                <i class="ri-save-line label-icon align-middle rounded-pill fs-16 ms-2" id="saveRefillDate"></i>
                                Save
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
            $('#refill').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: 'action/reminder/get_reminder.php',
                    type: 'POST'
                },
                columns: [  
                    { data: 'serial', title: 'ID' },
                    { data: 'days_left', title: 'Days Left', className: 'text-center' },
                    { data: 'client_name', title: 'Client Name', className: 'text-center' },
                    { data: 'client_phone', title: 'Client Phone', className: 'text-center' },
                    { data: 'client_address', title: 'Client Address', className: 'text-wrap' },
                    { data: 'product_name', title: 'Product Name', className: 'text-center' },
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

        function openRefillModal(id) {
            $('#refillDateModal').modal('show');
            $('#refillDateForm')[0].reset();
            $('#refillDateForm').removeClass('was-validated');
            $('#reminder_id').val(id);
        }

    </script>

    <script>
        $(document).ready(function () {

            $('#refillDateForm').submit(function (e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }
                $('#saveRefillDate').prop('disabled', true);
                const formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: 'action/reminder/update_reminder.php',
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

                            bootstrap.Modal.getInstance($('#refillDateModal')[0]).hide();

                            if (reload) {
                                $('#refill').DataTable().ajax.reload(null, false);
                            }
                        },
                    complete: function () {
                        $('#saveRefillDate').prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>

</html>