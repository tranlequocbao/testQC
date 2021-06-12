<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Admin DashBoard Quoc Bao" />
    <meta name="author" content="Quoc Bao" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
    <link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
    <link rel="stylesheet" href="./alertifyjs/css/themes/semantic.min.css">
    <link rel="stylesheet" href="./css/datepicker.min.css">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="./alertifyjs/alertify.min.js"></script>
    <script src="./js/datepicker.min.js"></script>
</head>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="index.html">DASHBOARD</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
            <div class="input-group-append">
                <button class="btn btn-primary loadPageArea" type="button" page="search"><i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">Settings</a><a class="dropdown-item" href="#">Activity Log</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="http://10.40.12.6:8080/QC/login/logout.php">Logout</a>
            </div>
        </li>
    </ul>
</nav>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link loadPageArea" href="#" page="dashboard">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-columns"></i>
                        </div>
                        ERROR BY DAY
                    </a>
                    <a class="nav-link loadPageArea" href="#" page="user">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        USER MANAGEMENT
                    </a>
                    <a class="nav-link confirmExport" href="#" page="export">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-columns"></i>
                        </div>
                        SEARCH AND EXPORT
                    </a>
                    <a class="nav-link loadPageArea" href="#" page="detail">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-columns"></i>
                        </div>
                        VIN BY DAY
                    </a>
                    <a class="nav-link loadPageArea" href="#" page="sealer_dashboard">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-columns"></i>
                        </div>
                        DASHBOARD SEALER
                    </a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?=$_SESSION['logined']['fullname']?>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main></main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; IT Administrator Dept - Passenger Car's Assembly Plants Build 20200511</div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="./js/admin.js"></script>
<script src="./js/custom.js"></script>
<script>
    $(document).ready(function(){
        admin.loadPage('dashboard');
        // admin.loadPage('user',{'goto' : '1'}, $(".loadPageArea[page=user]"));
        $(".confirmExport").on('click', function(){
            alertify.confirm('Chuyển sang chế độ export?', function(){

                window.open('../export', '_blank');
            }).set('title', 'Confirm');
            return false;
        })
    })
</script>
</body>

</html>