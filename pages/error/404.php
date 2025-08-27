<?php require_once PARTIALS_PATH . '/errorHeader.php'; ?>

<body class="error-body">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h1 class="panel-title">Error 404 - Page Not Found</h1>
        </div>
        <div class="panel-body">
            <h2 class="text-danger">Oops!</h2>
            <p class="lead">The page you are looking for doesn’t exist.</p>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-danger btn-lg">
                <span class="glyphicon glyphicon-arrow-left"></span> Go Back
            </a>
        </div>
    </div>
</body>