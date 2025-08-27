<?php require_once PARTIALS_PATH . '/errorHeader.php'; ?>

<body class="error-body">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h1 class="panel-title">Error 403 - Forbidden</h1>
        </div>
        <div class="panel-body">
            <h2 class="text-danger">Access Denied!</h2>
            <p class="lead">You don’t have permission to access this page.</p>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-danger btn-lg">
                <span class="glyphicon glyphicon-arrow-left"></span> Go Back
            </a>
        </div>
    </div>
</body>