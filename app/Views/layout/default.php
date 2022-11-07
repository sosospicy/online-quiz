<?php
$session = session();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>

    <link href="/assets/layout.css" rel="stylesheet" />
    <link href="/assets/app.css" rel="stylesheet" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary element-pc">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Quiz System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/exam">Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/subscription">Subscription</a>
                    </li>
                </ul>
                <div class="uinfo">
                    <?php if ($session->is_logged_in) : ?>
                        <span style="color: white;">Hi, <?= $session->name ?> | </span>
                        <a href="/sign-out">Sign Out</a>
                    <?php else : ?>
                        <a href="/sign-in">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


    <nav class="navbar navbar-expand-lg navbar-dark bg-primary element-mobile">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Quiz System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                <nav class="nav flex-column">
                    <?php if ($session->is_logged_in) : ?>
                        <a class="nav-link" href="/sign-out">Hi, <?= $session->name ?> | Sign Out</a>
                    <?php else : ?>
                        <a class="nav-link" href="/sign-in">Sign In</a>
                    <?php endif; ?>

                    <a class="nav-link" aria-current="page" href="/">Home</a>
                    <a class="nav-link" href="/exam">Exams</a>
                    <a class="nav-link" href="/subscription">Subscription</a>
                </nav>
            </div>
        </div>
    </nav>


    <main class="flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>
    
    <footer>
        copyright@2022
    </footer>
</body>

</html>