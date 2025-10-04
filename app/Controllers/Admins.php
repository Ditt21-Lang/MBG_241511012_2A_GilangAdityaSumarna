<?php 

namespace App\Controllers;

use App\Models\BahanBaku;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\User;
use CodeIgniter\I18n\Time; 
use CodeIgniter\Cache\Handlers\WincacheHandler;
use CodeIgniter\HTTP\Exceptions\RedirectException;

class Admins extends BaseController{

    /** 
      * Menampilkan page home dari admin 
      */
    public function admin(){

        $data = [
            'title' => 'Home Admin',
            'content' => view('admin_home')             
        ];

        return view('template_admin', $data);
    }

    /**
     *  Menampilkan list bahan baku 
     */
    public function bahan_baku()
    {

        $this->update_status_bahan_baku();
        $BahanBakuModel = new BahanBaku();
        $dataBahanBaku = $BahanBakuModel->findAll();
     
        $data = [
            'title' => 'Daftar Bahan Baku',
            'content' => view('list_bahan_baku', ['bahan_baku' => $dataBahanBaku])

        ];

        return view('template_admin', $data);
    }

    private function update_status_bahan_baku()
    {
        $bahanBakuModel = new BahanBaku();
        $db = \Config\Database::connect();
        $currentDate = Time::now()->toDateString(); // Mengambil tanggal hari ini (Y-m-d)
        
        // 1. Update status menjadi 'habis' jika jumlah = 0 dan status bukan 'habis'
        $db->table('bahan_baku')
           ->set('status', 'habis')
           ->where('jumlah <=', 0)
           ->where('status !=', 'habis')
           ->update();
        
        // 2. Update status menjadi 'kadaluarsa' jika tanggal kadaluarsa sudah terlewati
        $db->table('bahan_baku')
           ->set('status', 'kadaluarsa')
           ->where('tanggal_kadaluarsa <', $currentDate)
           ->where('status !=', 'kadaluarsa')
           ->update();
           
        // 3. Update status kembali menjadi 'tersedia' jika sudah ada stok (jumlah > 0) dan belum kadaluarsa
        // Penting: Status ini akan mengembalikan item yang sebelumnya 'habis' tapi baru diisi stoknya
        $db->table('bahan_baku')
           ->set('status', 'tersedia')
           ->where('jumlah >', 0)
           ->where('tanggal_kadaluarsa >=', $currentDate)
           ->whereIn('status', ['habis', 'segera_kadaluarsa']) // Hanya kembalikan yang sebelumnya 'habis' atau 'kadaluarsa_segera'
           ->update();
    }

    /**
     * Menampilkan form tambah bahan baku
     */
    public function add_bahan_baku(){
        $data = [
            'title' => 'Home Admin',
            'content' => view('add_bahan_baku')             
        ];

        return view('template_admin', $data);
    }

    /**
     * Proses memasukkan data dari form tambah bahan baku ke
     * dalam tabel users
     */
    public function save_bahan_baku()
    {
        $BahanBakuModel = new BahanBaku();

        // Ambil data dari form tambah bahan baku
        $userData = [
            'nama'   => $this->request->getPost('nama'),
            'kategori'  => $this->request->getPost('kategori'),
            'jumlah'  => $this->request->getPost('jumlah'),
            'satuan'   => $this->request->getPost('satuan'),
            'tanggal_masuk'       => $this->request->getPost('tanggal_masuk'), 
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            'status' => 'tersedia',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert ke tabel users
        if ($BahanBakuModel->insert($userData)) {
            return redirect()->to(base_url('admin/bahan_baku'))
                            ->with('success', 'Bahan Baku: ' . $userData['nama'] . ' berhasil ditambahkan!');
        } else {
            return redirect()->back()
                            ->with('error', 'Gagal menambahkan data bahan baku');
        }
    }

    /**
     * Proses menyimpan upodate jumlah stok 
     */
    public function update_stok(){
        $bahanBakuModel = new BahanBaku();

        // Mengambil id dan jumlah pada form
        $id = $this->request->getPost('id');
        $jumlahStokBaru = $this->request->getPost('jumlah');

        // Mengambil nama bahan baku untuk ditampilkan pada pesan nantinya
        $bahanBakuLama = $bahanBakuModel->find($id);
        $namaBahanBaku = $bahanBakuLama['nama'];

        // Cek apakah bahan baku ada
        if (!$id){
            return redirect()->back()
                            ->with('error', 'Bahan baku tidak ditemukan');
        }

        $dataUpdate = [
            'jumlah' => (int)$jumlahStokBaru
        ];

        // Update bahan baku dengan acuan id dan mengupdate kolom jumlah ($dataUpdate)
        if ($bahanBakuModel->update($id, $dataUpdate)){
            return redirect()->to(base_url('admin/bahan_baku'))->with('success', 'Stok bahan baku: ' . $namaBahanBaku . ' Berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate stok bahan baku');
        }
    }

    /**
     * Proses menghapus bahan baku yang expired
     */
    public function delete_expired($id){
        $bahanBakuModel = new BahanBaku();

        $bahanBakuExpired = $bahanBakuModel->find($id);
        
        if(!$bahanBakuExpired){
            return redirect()->back()->with('error', 'Gagal mengambil data bahan baku');
        }
        $namaBahanBaku = $bahanBakuExpired['nama'];
        $isKadaluarsa = $bahanBakuExpired['status'];

        // Proses pengecekan validasi ketika delete
        // Cek apakah bahan baku sudah kadaluarsa
        if ($isKadaluarsa !== 'kadaluarsa'){
            return redirect()->back()->with('error', 'Bahan baku: ' . $namaBahanBaku . ' belum kadaluarsa!');
        }

        if ($bahanBakuModel->delete($id)){
            return redirect()->to(base_url('admin/bahan_baku'))
                            ->with('success', 'Data bahan baku: ' . $namaBahanBaku . ' Berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus Bahan Baku: ' . $namaBahanBaku . '.');
        }
    }

    /**
     * Menampilkan list permintaan dari akun dapur untuk admin
     */
    public function list_permintaan_admin()
    {
        $permintaanModel = new Permintaan();
        $userModel = new User();

        // Ambil semua permintaan dengan status 'menunggu'
        // JOIN ke tabel user untuk mendapatkan nama pemohon
        $dataPermintaan = $permintaanModel
            ->select('permintaan.*, user.name')
            ->where('permintaan.status', 'menunggu')
            ->join('user', 'user.id = permintaan.pemohon_id')
            ->orderBy('permintaan.created_at', 'ASC') // Urutkan yang terlama
            ->findAll();

        $data = [
            'title' => 'Daftar Permintaan Menunggu Persetujuan',
            'list_permintaan' => $dataPermintaan,
            'content' => view('admin_list_permintaan_menunggu', ['list_permintaan' => $dataPermintaan])
        ];

        return view('template_admin', $data); 
    }

    public function proses_permintaan_view($id)
    {
        $permintaanModel = new Permintaan();
        $permintaanDetailModel = new PermintaanDetail();
        

        // Ambil informasi user yang meminta beserta menu makanan dan porsinya
        $permintaanInduk = $permintaanModel->select('permintaan.*, user.name')
                                            ->join('user', 'permintaan.pemohon_id = user.id')
                                            ->find($id);

        if (!$permintaanInduk || $permintaanInduk['status'] !== 'menunggu') {
            return redirect()->to(base_url('admin/list_permintaan_admin'))->with('error', 'Permintaan tidak ditemukan atau sudah diproses.');
        }

        // Ambil detail bahan baku
        $detailBahan = $permintaanDetailModel
            ->select('permintaan_detail.*, bahan_baku.nama, bahan_baku.satuan, bahan_baku.jumlah')
            ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id')
            ->where('permintaan_id', $id)
            ->findAll();

        // Array data untuk passing ke View
        $viewData = [
            'title' => 'Proses Permintaan',
            'permintaan' => $permintaanInduk,
            'detail_bahan' => $detailBahan,
        ];

        // Array data utama untuk template (koreksi baris ini)
        $data = [
            'title' => 'Proses Permintaan ID ' . $id,
            'content' => view('admin_form_proses_permintaan', $viewData) 
        ];

        return view('template_admin', $data);
    }

    /**
     * Proses mengubah status di database sesuai aksi (setuju / tidak setuju)
     */
    public function submit_proses_permintaan($id){
        $permintaanModel = new Permintaan();
        $bahanBakuModel = new BahanBaku();

        $db = \Config\Database::connect();

        // Mengambil aksi yang dilakukan pada form
        $aksi = $this->request->getPost('aksi');

        $permitaan = $permintaanModel->find($id);

        $db->transBegin();

        // try - catch untuk memastikan jika ada flow yang salah / error terjadi
        try{

            // Jika aksi ditolak, update status ke table permintaan
            if ($aksi == 'tolak'){
                $permintaanModel->update($id, [
                    'status' => 'ditolak'
                ]);

            // Jika aksi disetujui
            } elseif ($aksi == 'disetujui') {

                // Mengambil data dalam database yang akan terpangaruh jika permintaan disetujui
                $detailBahan = $db->table('permintaan_detail pd')
                    ->select('pd.bahan_id, pd.jumlah_diminta, bb.jumlah')
                    ->join('bahan_baku bb', 'bb.id = pd.bahan_id')
                    ->where('pd.permintaan_id', $id)
                    ->get()
                    ->getResultArray();

                // Looping pada setiap bahan yang diminta
                foreach ($detailBahan as $detail){
                    $bahan_id = $detail['bahan_id'];
                    $jumlah_diminta = $detail['jumlah_diminta'];
                    $stok_lama = $detail['jumlah'];
                    $stok_baru = $stok_lama - $jumlah_diminta;
                
                    // Pengecekan jika jumlah yang diminta lebih dari stok yang tersedia
                    if ($stok_baru < 0){
                        throw new \Exception("Stok tidak mencukupi untuk bahan ID {$bahan_id}.");
                    }

                    $updateData = ['jumlah' => $stok_baru];

                    // Pengubaha status pada tabel bahan baku jika stok habis setelah disetujui
                    if($stok_baru == 0){
                        $updateData['status'] = 'habis';
                    }

                    // Update ke tabel bahan baku
                    $bahanBakuModel->update($bahan_id, $updateData);
                }

                // Update ke tabel permintaan
                $permintaanModel->update($id, ['status' => 'disetujui']);

            } else {
                throw new \Exception('Aksi tidak valid.');
            }

            // Meyimpan perubahan data
            $db->transCommit();
            session()->setFlashdata('success', "Permintaan ID {$id} berhasil diproses sebagai **{$aksi}**.");
            return redirect()->to(base_url('admin/list_persetujuan'));
        } catch (\Exception $e){

            // Membatalkan perubahan data
            $db->transRollback();
            session()->setFlashdata('error', 'Gagal memproses permintaan: ' . $e->getMessage());
            return redirect()->back(); 
        }
    }

}

?>