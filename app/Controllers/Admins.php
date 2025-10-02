<?php 

namespace App\Controllers;

use App\Models\BahanBaku;
use App\Models\Employee;
use App\Models\Takes;
use App\Models\User;
use App\Models\Students;
use App\Models\Course;
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
        $BahanBakuModel = new BahanBaku();
        $dataBahanBaku = $BahanBakuModel->findAll();
     
        $data = [
            'title' => 'Daftar Bahan Baku',
            'content' => view('list_bahan_baku', ['bahan_baku' => $dataBahanBaku])

        ];

        return view('template_admin', $data);
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
}

?>