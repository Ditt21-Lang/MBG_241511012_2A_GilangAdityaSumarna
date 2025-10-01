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
    public function admin(){

        $data = [
            'title' => 'Home Admin',
            'content' => view('admin_home')             
        ];

        return view('template_admin', $data);
    }

    public function bahan_baku()
    {
        $BahanBakuModel = new BahanBaku();
        $dataBahanBaku = $BahanBakuModel->findAll();

        $data =[
            'title' => 'Students List',
            'content'=> view('list_bahan_baku', ['bahan_baku' => $dataBahanBaku])
        ];

        return view('template_admin', $data);
        
    }

    public function add_bahan_baku(){
        $data = [
            'title' => 'Home Admin',
            'content' => view('add_bahan_baku')             
        ];

        return view('template_admin', $data);
    }

    public function save_bahan_baku()
    {
        $BahanBakuModel = new BahanBaku();

        // Ambil data dari form
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