<?php 

namespace App\Controllers;

use App\Models\Permintaan;
use CodeIgniter\Controller;
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
}