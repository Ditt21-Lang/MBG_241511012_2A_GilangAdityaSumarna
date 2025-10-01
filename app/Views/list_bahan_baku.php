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
        <table class="table table-bordered table-striped-columns mx-auto" style="width: 80%; border-radius: 10px;">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>jumlah</th>
            <th>satuan</th>
            <th>tanggal_masuk</th>
            <th>tanggal_kadaluarsa</th>
            <th>status</th>
            <th>created_at</th>
        </tr>
        <tbody id="studentTableBody"> </tbody>
        </table><br><br>
        <a href="<?= base_url('admin/bahan_baku/add') ?>" class="btn btn-info"> Tambah Bahan Baku </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let bahan_baku = <?= json_encode($bahan_baku) ?>;
        
        function renderStudent(){
            let tbody = document.getElementById("studentTableBody");
            tbody.innerHTML = "";
            bahan_baku.forEach((b, index) => {
                let tr = document.createElement("tr");

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${b.nama}</td>
                    <td>${b.kategori}</td>
                    <td>${b.jumlah}</td>
                    <td>${b.satuan}</td>
                    <td>${b.tanggal_masuk}</td>
                    <td>${b.tanggal_kadaluarsa}</td>
                    <td>${b.created_at}</td>
                    <td>
                        <a href="editStudent/${b.id}" class="btn btn-warning">Edit</a>
                        <form action="<?= base_url('admin/deleteStudent/') ?>${b.id}" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus mahasiswa ${b.nama}?')"> 
                            <?= csrf_field() ?> 
                            <button type="submit" class="btn btn-danger wb">Delete</button> 
                        </form>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    renderStudent();

    const flash = document.getElementById('flash');
    if(flash){
        setTimeout(() => {
            flash.remove();
        }, 3000);
    }
    </script>
</body>
</html>
