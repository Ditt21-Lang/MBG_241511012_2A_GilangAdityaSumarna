<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Bahan Baku</title>
</head>
<body class="bg-light justify-content-center align-items-center" style="height:100vh;">
    <?php if(session()->getFlashdata('error')): ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <div id="alertMessage" class="alert d-none" role="alert"></div>

    <form action="<?= base_url('admin/bahan_baku/save') ?>" method="post" id="addStudentForm">
        
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama"  class="form-control w-25" required>
                <div  class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori"  class="form-control w-25" required>
                <div  class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label  class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control w-25" required>
                <div class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label  class="form-label">Satuan</label>
                <input type="text" name="satuan" class="form-control w-25" required>
                <div class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk </label>
                <input type="date" name="tanggal_masuk" class="form-control w-25" required>
                <div  class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Kadaluarsa </label>
                <input type="date" name="tanggal_kadaluarsa" class="form-control w-25" required>
                <div  class="text-danger mt-1"></div>
            </div>

        <button type="submit" class="btn btn-success wb">Simpan</button>
        <a href="<?= base_url('admin/bahan_baku') ?>" class="btn btn-secondary">Kembali</a>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>