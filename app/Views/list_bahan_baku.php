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
    <div class="text-center">
        <h2 class="mb-4">List Bahan Baku</h2>
        
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
        
        <br>
        
        <table class="table table-bordered table-striped-columns mx-auto" style="width: 90%; border-radius: 10px;">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tgl. Masuk</th>
                <th>Tgl. Kadaluarsa</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            <tbody>
                <?php 
                $hariIni = new DateTime();
                $no = 1;
                foreach ($bahan_baku as $item): 
                    $expired = new DateTime($item['tanggal_kadaluarsa']);
                    $stok = $item['jumlah'];
                    $isKadaluarsa = $hariIni > $expired;
                    
                    // Hitung selisih HANYA jika tanggal expired belum terlewati
                    $selisih = 999; 
                    if ($expired > $hariIni) {
                        $selisih = $hariIni->diff($expired)->days;
                    }
                    
                    $statusTampil = 'Tersedia'; // Default
                    
                    // Logika Penentuan Status
                    if ($stok <= 0){
                        $statusTampil = 'Habis';
                    } else if ($isKadaluarsa){
                        $statusTampil = 'Kadaluarsa';
                    } else if ($selisih <= 3){
                        $statusTampil = 'Segera Kadaluarsa';
                    }
                    
                    // Menentukan warna badge/text untuk Bootstrap (Opsional)
                    $statusClass = '';
                    if ($statusTampil == 'Habis') $statusClass = 'text-danger';
                    else if ($statusTampil == 'Kadaluarsa') $statusClass = 'text-black bg-danger-subtle';
                    else if ($statusTampil == 'Segera Kadaluarsa') $statusClass = 'text-warning';
                    else $statusClass = 'text-success';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($item['nama']) ?></td>
                    <td><?= esc($item['kategori']) ?></td>
                    <td><?= esc($item['jumlah']) ?></td>
                    <td><?= esc($item['satuan']) ?></td>
                    <td><?= esc($item['tanggal_masuk']) ?></td>
                    <td><?= esc($item['tanggal_kadaluarsa']) ?></td>
                    <td class="<?= $statusClass ?>">
                        <strong><?= $statusTampil ?></strong>
                    </td>
                    <td><?= esc($item['created_at']) ?></td>
                    <td>
                        <a href="<?= base_url('admin/bahan_baku/edit/' . $item['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="<?= base_url('admin/bahan_baku/delete/' . $item['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus bahan baku <?= esc($item['nama']) ?>?')"> 
                            <?= csrf_field() ?> 
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button> 
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br><br>
        <a href="<?= base_url('admin/bahan_baku/add') ?>" class="btn btn-info"> Tambah Bahan Baku </a>
    </div>

    <script>
        // Script untuk menghilangkan flash message setelah 3 detik
        const flash = document.getElementById('flash');
        if(flash){
            setTimeout(() => {
                flash.style.opacity = '0';
                flash.style.transition = 'opacity 1s';
                // Menghapus elemen setelah animasi selesai (misalnya 1 detik setelah opacity 0)
                setTimeout(() => {
                    flash.remove();
                }, 1000); 
            }, 3000);
        }
        
        // Karena Anda memproses data di PHP, kode JavaScript renderStudent sudah TIDAK diperlukan.
        // Jika Anda ingin menggunakan AJAX, baru kode JavaScript rendering itu relevan.
    </script>
</body>
</html>
