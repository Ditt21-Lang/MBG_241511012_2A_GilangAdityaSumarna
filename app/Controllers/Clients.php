<?php 

namespace App\Controllers;

use App\Models\BahanBaku;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use CodeIgniter\Controller;
use CodeIgniter\Model;
use function PHPUnit\Framework\returnArgument;

class Clients extends Controller{

    /**
     * Menampilkan halaman home dari client (dapur)
     */
    public function client(){
        $data = [
            'title' => 'Home Client',
            'content' => view('client_home')
        ];

        return view('template', $data);

    }

    /**
     * Menampilkan halaman list permintaan oleh client
     */
    public function permintaan(){
        $PermintaanModel = new Permintaan();
        $session = session();
        $userId = $session->get('id');

        if (!$userId){
            return redirect()->to(base_url('/'))->with('error', 'Anda harus login dulu untuk mengakses halaman ini');
        }

        $dataPermintaan = $PermintaanModel->select('permintaan.id, permintaan.tgl_masak, permintaan.menu_makan, permintaan.status, bahan_baku.nama, permintaan_detail.jumlah_diminta')
                                        ->where('permintaan.pemohon_id', $userId)
                                        
                                        // HINDARI DUPLIKASI DATA DETAIL:
                                        // GROUP BY akan menggabungkan baris yang identik
                                        ->groupBy('permintaan.id, permintaan.tgl_masak, permintaan.menu_makan, permintaan.status, bahan_baku.nama, permintaan_detail.jumlah_diminta')

                                        // ... sisa JOIN dan ORDER BY tetap sama ...
                                        ->join('permintaan_detail', 'permintaan_detail.permintaan_id = permintaan.id')
                                        ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id')
                                        ->orderBy('permintaan.created_at', 'DESC')
                                        ->findAll();
    
        $permintaan_grouped = [];
        foreach ($dataPermintaan as $row) {
            $id_permintaan = $row['id'];
            
            if (!isset($permintaan_grouped[$id_permintaan])) {
                // Buat entri baru untuk permintaan induk
                $permintaan_grouped[$id_permintaan] = [
                    'id' => $row['id'],
                    'tgl_masak' => $row['tgl_masak'],
                    'menu_makan' => $row['menu_makan'],
                    'status_permintaan' => $row['status'],
                    'detail_bahan' => [], // Wadah untuk detail bahan
                ];
            }
            
            // Masukkan detail bahan ke dalam permintaan induk
            $permintaan_grouped[$id_permintaan]['detail_bahan'][] = [
                'nama_bahan' => $row['nama'],
                'jumlah_diminta' => $row['jumlah_diminta'],
            ];
        }

        $data = [
            'title' => 'Status Permintaan bahan baku',
            'list_permintaan' => $permintaan_grouped,
            'content' => view('list_status_permintaan', ['list_permintaan' => array_values($permintaan_grouped)])
        ];

        return view('template', $data);
    }

    /**
     * Menampilkan form tambah permintaan
     */
    public function add_permintaan(){
        $bahanBakuModel = new BahanBaku();

        $statusDiizinkan = ['tersedia', 'segera_kadaluarsa'];

        $dataBahanBaku = $bahanBakuModel->select('id, nama, satuan,jumlah')
                                        ->where('jumlah >', 0)
                                        ->whereIn('status', $statusDiizinkan)
                                        ->findAll();


        $data = [
            'title' => 'Tambah Permintaan',
            'content' => view('add_permintaan', ['list_bahan_baku' => $dataBahanBaku])
        ];

        return view('template', $data);
    }

    /**
     * Proses menyimpan status permintaan baru ke database
     */
    public function save_permintaan(){
        $session = session();

        $permintaanModel = new Permintaan();
        $permintaanDetailModel = new PermintaanDetail();
        $bahanBakuModel = new BahanBaku();

        $error_messages = [];
        $dataUntukSimpan = []; 
        $bahanDimintaIdUnik = [];

        $pemohon_id = session()->get('id');

        // Ambil data induk permintaan
        $data_permintaan = [
            'pemohon_id' => $pemohon_id,
            'tgl_masak' => $this->request->getPost('tgl_masak'),
            'menu_makan' => $this->request->getPost('menu_makan'),
            'jumlah_porsi' => $this->request->getPost('jumlah_porsi'),
            'status' => 'menunggu'
        ];

        // Ambil detail bahan baku yang diminta
        $bahanDiminta = $this->request->getPost('bahan');

        if(empty($bahanDiminta)){
            $error_messages[] = "Permintaan bahan baku harus ada minimal satu item!";
        } else {
            // Hapus $BahanDimintaDuplikat yang tidak terpakai

            foreach ($bahanDiminta as $index => $detail){
                $nomorBaris = $index + 1;
                $namaInput = trim($detail['nama']);
                // Menggunakan filter_var untuk konversi integer yang aman
                $jumlahDiminta = (int)filter_var($detail['jumlah'], FILTER_SANITIZE_NUMBER_INT); 
                
                // Cari bahan di DB
                $bahan_db = $bahanBakuModel
                            ->where('nama', $namaInput)
                            ->first();

                if (!$bahan_db) {
                    $error_messages[] = "Baris #{$nomorBaris} ({$namaInput}): Bahan baku tidak terdaftar. Cek daftar di atas.";
                    continue;
                }

                $stokTersedia = (int)($bahan_db['jumlah'] ?? 0); 
                $bahan_id = $bahan_db['id'];
                $satuan_db = $bahan_db['satuan'];

                // Cek Stok harus > 0
                if ($stokTersedia <= 0){
                    $error_messages[] = "Baris #{$nomorBaris} ({$namaInput}): Stok bahan baku ini sudah habis (0 {$satuan_db}).";
                    continue;
                }

                // Cek Jumlah input melebih stok
                if ($jumlahDiminta > $stokTersedia){
                    $error_messages[] = "Baris #{$nomorBaris} ({$namaInput}): Stok tidak cukup. Diminta: {$jumlahDiminta}, Tersedia: {$stokTersedia} {$satuan_db}.";
                    continue;
                }

                // Cek MenCegah permintaan duplikat ID bahan baku
                if (in_array($bahan_id, $bahanDimintaIdUnik)) {
                    $error_messages[] = "Baris #{$nomorBaris} ({$namaInput}): Bahan baku ini sudah diminta pada baris sebelumnya.";
                    continue;
                }

                // Simpan ID yang lolos ke array pelacak duplikat
                $bahanDimintaIdUnik[] = $bahan_id;
                
                // Simpan detail yang lolos validasi
                $dataUntukSimpan[] = [
                    'bahan_id' => $bahan_id,
                    'jumlah_diminta' => $jumlahDiminta,
                ];
            }
        }

        // Penanganan Error (Validasi Gagal)
        if (!empty($error_messages)){
            session()->setFlashdata('error', implode('<br>', $error_messages));
            return redirect()->back()->withInput();
        }

        // Proses Penyimpanan
        if ($permintaanModel->insert($data_permintaan)){
            $permintaan_id = $permintaanModel->getInsertID();

            foreach($dataUntukSimpan as $detail){
                $dataDetail = [
                    'permintaan_id' => $permintaan_id,
                    'bahan_id' => $detail['bahan_id'],
                    'jumlah_diminta' => $detail['jumlah_diminta'] 
                ];
                $permintaanDetailModel->insert($dataDetail);
            }
            
            session()->setFlashdata('success', 'Permintaan bahan baku berhasil dikirim dan menunggu persetujuan');
            return redirect()->to(base_url('client/status_permintaan'));
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan data induk permintaan!');
            return redirect()->back()->withInput();
        }
    }
}
