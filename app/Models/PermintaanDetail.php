<?php
namespace App\Models;

use CodeIgniter\Model;

class PermintaanDetail extends Model{
    protected $table = 'permintaan_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['permintaan_id', 'bahan_id', 'jumlah_diminta'];
}
?>