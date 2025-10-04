<!DOCTYPE html>
<html>
<head>
    <title>List Permintaan dari Dapur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Daftar Permintaan Bahan Baku (Menunggu Persetujuan)</h3>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (empty($list_permintaan)): ?>
            <div class="alert alert-info">Tidak ada permintaan bahan baku dengan status 'menunggu' saat ini.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Permintaan</th>
                            <th>Pemohon</th>
                            <th>Tgl Masak</th>
                            <th>Menu Makanan</th>
                            <th>Jml Porsi</th>
                            <th>Diajukan Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_permintaan as $permintaan): ?>
                            <tr>
                                <td><?= $permintaan['id'] ?></td>
                                <td><?= $permintaan['name'] ?></td>
                                <td><?= date('d M Y', strtotime($permintaan['tgl_masak'])) ?></td>
                                <td><?= $permintaan['menu_makan'] ?></td>
                                <td><?= $permintaan['jumlah_porsi'] ?></td>
                                <td><?= date('d M Y H:i', strtotime($permintaan['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/list_persetujuan/proses/' . $permintaan['id']) ?>" class="btn btn-sm btn-primary">
                                        Proses
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
    </div>
</body>
</html>
