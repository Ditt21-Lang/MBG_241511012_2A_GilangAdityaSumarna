<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Persetujuan Permintaan</title>
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Proses Permintaan #<?= $permintaan['id'] ?></h3>
        <p><strong>Status:</strong> <span class="badge badge-warning text-dark">MENUNGGU</span></p>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Informasi Dasar Permintaan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Pemohon:</strong> <?= $permintaan['name'] ?? 'N/A' ?></p>
                        <p><strong>Tanggal Masak:</strong> <?= date('d M Y', strtotime($permintaan['tgl_masak'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Menu Makanan:</strong> <?= $permintaan['menu_makan'] ?></p>
                        <p><strong>Jumlah Porsi:</strong> <?= $permintaan['jumlah_porsi'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                Detail Bahan yang Diminta
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Bahan</th>
                                <th>Jml Diminta</th>
                                <th>Stok Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_bahan as $detail): ?>
                                <tr>
                                    <td><?= $detail['nama'] ?></td>
                                    <td><?= $detail['jumlah_diminta'] ?> <?= $detail['satuan'] ?></td>
                                    <td><?= $detail['jumlah']?></td> 
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <form action="<?= base_url('admin/list_persetujuan/save/'. $permintaan['id']) ?>" method="post">       
                    <?= csrf_field() ?>

                    <div class="d-flex justify-content-around p-3">
                        <button 
                            type="submit" 
                            name="aksi" 
                            value="disetujui" 
                            class="btn btn-success btn-lg"
                            onclick="return confirm('Yakin setujui permintaan ini dan kurangi stok?');"
                        >
                            <i class="fas fa-check-circle"></i> Setujui & Kurangi Stok
                        </button>
                                
                        <button 
                            type="submit" 
                            name="aksi" 
                            value="tolak" 
                            class="btn btn-danger btn-lg" 
                            onclick="return confirm('Anda yakin menolak permintaan ini?');"
                        >
                            <i class="fas fa-times-circle"></i> Tolak Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <a href="<?= base_url('admin/list_persetujuan') ?>" class="btn btn-secondary">Kembali ke Daftar Permintaan</a>
    </div>
</body>
</html>