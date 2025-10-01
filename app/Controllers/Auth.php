<?php 

namespace App\Controllers;

use App\Models\User;

class Auth extends BaseController{

    public function login(){
        return view('login');
    }

    public function processLogin(){
        $session = session();
        $userModel = new User();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email',$email)->first();

        // Cek pakah email ada
        if($user){
            if(md5($password) === $user['password']){
                $session->set([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email'=> $user['email'],
                    'role' => $user['role'],
                    'logged_in' => true
                ]);

                // Redirect sesuai role
                if ($user['role'] === 'gudang'){
                    return redirect('')->to(base_url('admin/home'));
                } else if ($user['role'] === 'dapur'){
                    return redirect('')->to(base_url('client/home'));
                }

            } else{
                $session->setFlashdata('error', 'Password Salah!');
                return redirect()->to(base_url('/'));
            }
        } else {
            $session->setFlashdata('error','Email tidak ditemukan!');
            return redirect()->to(base_url('/'));
        }
    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}

?>