<?php
namespace App\Models;

use CodeIgniter\Model;

Class BahanBaku extends Model{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'kategori', 'jumlah','satuan', 'tanggal_masuk', 'tanggal_kadaluarsa' ,'status', 'created_at'];

}

?>