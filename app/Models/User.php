<?php
namespace App\Models;

use CodeIgniter\Model;

Class User extends Model{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password','role', 'created_at'];

}

?>