<?php 

namespace App\Controllers;

use CodeIgniter\Controller;

class Clients extends Controller{
    public function client(){
        $data = [
            'title' => 'Home Client',
            'content' => view('client_home')
        ];

        return view('template', $data);

    }
}