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
                        <button 
                            type="button" 
                            class="btn btn-sm btn-warning btn-edit-stok"
                            data-bs-toggle="modal" 
                            data-bs-target="#editStokModal"
                            data-id="<?= esc($item['id']) ?>"
                            data-nama="<?= esc($item['nama']) ?>"
                            data-jumlah="<?= esc($item['jumlah']) ?>"
                            data-satuan="<?= esc($item['satuan']) ?>"
                        >
                            Edit Stok
                        </button>
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
    <div class="modal fade" id="editStokModal" tabindex="-1" aria-labelledby="editStokModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStokModalLabel">Edit Jumlah Stok: <span id="bahan_baku_nama"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUpdateStok" action="<?= base_url('admin/bahan_baku/update') ?>" method="post">
                    <div class="modal-body">
                        <?= csrf_field() ?> 
                        <input type="hidden" name="id" id="edit_id"> 
                        
                        <div class="mb-3">
                            <label for="edit_jumlah" class="form-label">Jumlah Stok Baru</label>
                            <input type="number" class="form-control" name="jumlah" id="edit_jumlah" required min="0" step="1">
                        </div>
                        
                        <p class="mb-0">Satuan: <strong id="bahan_baku_satuan"></strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Stok</button>
                    </div>
                </form>
            </div>
        </div>
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

        // Script untuk mengisi data ke modal saat tombol Edit Stok diklik
        document.addEventListener('DOMContentLoaded', function () {
            const editStokModal = document.getElementById('editStokModal');
            
            editStokModal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                const button = event.relatedTarget; 

                // Ambil data dari atribut data-* tombol
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const jumlah = button.getAttribute('data-jumlah');
                const satuan = button.getAttribute('data-satuan');

                // Isi data ke dalam elemen-elemen di modal
                
                // Judul modal
                document.getElementById('bahan_baku_nama').textContent = nama;
                
                // Input tersembunyi untuk ID
                document.getElementById('edit_id').value = id; 
                
                // Input jumlah
                document.getElementById('edit_jumlah').value = jumlah;
                
                // Teks satuan
                document.getElementById('bahan_baku_satuan').textContent = satuan;
            });
        });
        
    </script>
</body>
</html>
