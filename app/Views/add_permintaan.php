<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Permintaan</title>
</head>
<body class="bg-light p-4">
    
    <div class="card p-4 mx-auto mb-4" style="max-width: 800px;">
        <h2 class="mb-3 text-success">✅ Bahan Baku Tersedia di Gudang</h2>
        
        <?php 
        // 1. Ambil data PHP (asumsi $list_bahan_baku ada - menyesuaikan kode Anda)
        $bahan_list_data = $list_bahan_baku ?? []; 
        ?>

        <?php if (empty($bahan_list_data)): ?>
            <div class="alert alert-warning">Tidak ada bahan baku yang tersedia saat ini (stok 0 atau status kadaluarsa).</div>
        <?php else: ?>
            <table class="table table-sm table-striped">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Menggunakan objek atau array, disesuaikan agar kompatibel
                    foreach ($bahan_list_data as $bahan): 
                        $id = $bahan->id ?? $bahan['id'] ?? 'N/A';
                        $nama = $bahan->nama ?? $bahan['nama'] ?? 'N/A';
                        $satuan = $bahan->satuan ?? $bahan['satuan'] ?? 'N/A';
                        $jumlah = $bahan->jumlah ?? $bahan['jumlah'] ?? 'N/A';
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $nama ?></td>
                        <td><?= $satuan ?></td>
                        <td><?= $jumlah ?></td> 
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <form action="<?= base_url('client/status_permintaan/save') ?>" method="post" id="addPermintaanForm">
        <div class="card p-4 mx-auto" style="max-width: 800px;"> 
            <h1 class="mb-4">Form Tambah Permintaan Bahan Baku</h1>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Tanggal Masak:</label>
                <input type="date" name="tgl_masak" class="form-control w-50" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Menu yang akan dimasak:</label>
                <input type="text" name="menu_makan" class="form-control w-75" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Porsi yang dibuat:</label>
                <input type="number" name="jumlah_porsi" class="form-control w-25" min="1" required>
            </div>

            <h2 class="mt-4">Daftar Bahan Baku yang diminta:</h2>
            
            <table class="table table-bordered" style="width: 100%;" id="bahan_baku_table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Bahan Baku</th>
                        <th style="width: 25%;">Jumlah Diminta</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-info" id="add_row_btn">Tambah Bahan</button>
                <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
            </div>
        </div>
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data PHP sekarang hanya digunakan untuk referensi atau validasi lanjutan di client-side (jika diperlukan)
        const availableBahan = <?= json_encode($list_bahan_baku ?? []); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#bahan_baku_table tbody');
            const addRowBtn = document.querySelector('#add_row_btn');
            let rowCount = 0;

            // Fungsi untuk membuat baris baru
            function createRow() {
                rowCount++;
                
                const newRow = tableBody.insertRow();
                
                // Kolom NO
                newRow.insertCell(0).textContent = rowCount;

                // --- Kolom Nama Bahan Baku (INPUT TEXT) ---
                const namaCell = newRow.insertCell(1);
                const namaInput = document.createElement('input');
                namaInput.type = 'text';
                // Penting: Menggunakan 'nama' untuk input teks
                namaInput.name = `bahan[${rowCount}][nama]`; 
                namaInput.classList.add('form-control');
                namaInput.required = true;
                namaCell.appendChild(namaInput);
                
                // --- Kolom Jumlah (Numerik Input) ---
                const jumlahCell = newRow.insertCell(2);
                const jumlahInput = document.createElement('input');
                jumlahInput.type = 'number';
                jumlahInput.name = `bahan[${rowCount}][jumlah]`; 
                jumlahInput.classList.add('form-control');
                jumlahInput.min = '1';
                jumlahInput.required = true;
                jumlahCell.appendChild(jumlahInput);

                // Kolom Aksi (Tombol Hapus)
                const actionCell = newRow.insertCell(3);
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.textContent = 'Hapus';
                deleteBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                deleteBtn.onclick = function() {
                    tableBody.removeChild(newRow);
                    updateRowNumbers();
                };
                actionCell.appendChild(deleteBtn);
            }
            
            // Fungsi untuk memperbarui nomor urut setelah penghapusan
            function updateRowNumbers() {
                const rows = tableBody.querySelectorAll('tr');
                rowCount = 0;
                rows.forEach(function(row) {
                    rowCount++;
                    row.cells[0].textContent = rowCount; // Update No.

                    // Update nama input/select agar indeks array PHP tetap konsisten
                    row.cells[1].querySelector('input').name = `bahan[${rowCount}][nama]`; // Input Nama
                    row.cells[2].querySelector('input').name = `bahan[${rowCount}][jumlah]`; // Input Jumlah
                });
            }

            // Tambahkan baris pertama saat halaman dimuat
            createRow(); 
            
            // Event listener untuk tombol "Tambah Bahan"
            addRowBtn.addEventListener('click', createRow);
        });
    </script>
</body>
</html>