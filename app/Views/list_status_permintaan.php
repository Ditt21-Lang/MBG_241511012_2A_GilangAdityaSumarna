<!DOCTYPE html>
<html>
<head>
    <title>CRUD Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @keyframes SlideOutRight {
            from { transform: translate(0); opacity: 1;}
            to { transform: translateY(100%); opacity: 0;}
        }

        .slide-out{
            animation: SlideInLeft 1.0s ease forwards;
        }

        .table th{
            color:rgb(253, 244, 227);
            background-color: rgb(19, 70, 134);
        }

    </style>
</head>
<body>
    <div class="text-center">
        <h2 class="mb-4">Daftar Status Permintaan</h2>
        
        <?php if (session()->getFlashdata('success')): ?>
        <div id="flash" style="color: green; text-align: center;">
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div id="flash" style="color: red; text-align: center;">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>
        
    </div>
    <div class="container mt-4">
        
        <?php foreach ($list_permintaan as $permintaan): ?>
        <div class="card mb-5">
            <div class="card-header bg-light border border-info border-5 text-black">
                Permintaan ID: <?= $permintaan['id'] ?> |
                <?php
                $status = strtolower($permintaan['status_permintaan']);
                if ($status == 'disetujui'){
                    $statusClass = 'text-success';
                } else if ($status == 'ditolak'){
                    $statusClass = 'text-danger';
                } else {
                    $statusClass = 'bg-warning';
                }
                ?> 
                Status: <span class="<?= $statusClass ?>"> 
                    <strong><?= strtoupper($permintaan['status_permintaan']) ?></strong>
                </span> | 
                Tgl Masak: <?= date('d M Y', strtotime($permintaan['tgl_masak'])) ?>
            </div>
            <div class="card-body">
                <p><strong>Menu Makanan:</strong> <?= esc($permintaan['menu_makan']) ?></p>
                
                <h5>Detail Bahan yang Diminta:</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th>Jumlah Diminta</th>
                        </tr>
                    </thead>
                    <tbody class="border border-black border-1">
                        <?php foreach ($permintaan['detail_bahan'] as $detail): ?>
                        <tr>
                            <td><?= esc($detail['nama_bahan']) ?></td>
                            <td><?= esc($detail['jumlah_diminta']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($list_permintaan)): ?>
            <div class="alert alert-info">Tidak ada data permintaan ditemukan.</div>
        <?php endif; ?>

    </div>
</body>
</html>