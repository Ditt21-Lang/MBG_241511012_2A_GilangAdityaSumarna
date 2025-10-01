<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
    <style>
        .headerP{
            color: rgb(254, 178, 26);
            background-color: white;
        }
    </style>
</head>
<body>
    <?php if (session()->getFlashdata('error')):?>
        <div class="alert alert-danger text-center"   role="alert">
            <p><?= session()->getFlashdata('error') ?></p>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="card shadow p-5 mt-5 text-center">
            <h1 class="headerP">Selamat Datang,  <?= esc(session()->get('name')) ?></h1>
            <p class="text-muted">Masak apa hari ini ?</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

