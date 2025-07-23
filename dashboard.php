
<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
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

                    <div class="row">
                        <!-- Today Refills -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Today Refills</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                                <i class="mdi mdi-battery-charging text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="todayRefills" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Refills -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Pending Refills</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                                <i class="mdi mdi-alert-circle text-danger"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="pendingRefills" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Refills -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Upcoming Refills</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                <i class="mdi mdi-calendar-clock text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="upcomingRefills" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Total Refill Amount -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Refills Amount</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="mdi mdi-currency-inr text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary">₹<span id="totalRefillAmount" class="counter-value" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- This Month Sales -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Month Sales</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                <i class="mdi mdi-cart text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="monthSales" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- This Month Income -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Month Income</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="mdi mdi-bank text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary">₹<span id="monthIncome" class="counter-value" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Total Sales -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Sales</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                <i class="mdi mdi-sale text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="totalSales" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Total Income -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Income</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="mdi mdi-cash text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary">₹<span id="totalIncome" class="counter-value" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Total Category -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Category</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                                <i class="mdi mdi-format-list-bulleted text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="totalCategory" data-target="0"></span></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Total Products -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Products</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-secondary-subtle rounded fs-3">
                                                <i class="mdi mdi-package-variant text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="fs-22 fw-semibold ff-secondary"><span id="totalProducts" data-target="0"></span></h4>
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
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/theme.js"></script>

    <script>

        $(document).ready(function () {
            $.getJSON("dashboard_fetch.php", function (data) {
                $("#todayRefills").attr("data-target", data.todayRefills);
                $("#pendingRefills").attr("data-target", data.pendingRefills);
                $("#upcomingRefills").attr("data-target", data.upcomingRefills);
                $("#totalRefillAmount").attr("data-target", data.totalRefillAmount);
                $("#monthSales").attr("data-target", data.monthSales);
                $("#monthIncome").attr("data-target", data.monthIncome);
                $("#totalSales").attr("data-target", data.totalSales);
                $("#totalIncome").attr("data-target", data.totalIncome);
                $("#totalCategory").attr("data-target", data.totalCategory);
                $("#totalProducts").attr("data-target", data.totalProducts);
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

</body>

</html>