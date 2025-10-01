<?php 

namespace App\Controllers;

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

    public function students()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.user_id, users.first_name, users.last_name, employee.pekerjaan ,employee.no_telepon');
        $builder->join('employee', 'users.user_id = employee.user_id'); 
        $query = $builder->get()->getResultArray();

        $data =[
            'title' => 'Students List',
            'content'=> view('list_di_admin', ['employee' => $query])
        ];

        return view('template_admin', $data);
        
    }


    public function courses()
    {
        $courseModel = new Course();
        $dataCourses['courses'] = $courseModel->findAll();

        $data = [
            'title' => 'List Mahasiswa',
            'content' => view('course_admin', $dataCourses)
        ];

        return view('template_admin', $data);
    }

    public function add_course()
    {
        $data = [
            'title' => 'Add Course',
            'content' => view('add_course')
        ];
        return view('template_admin', $data);
    }

    public function save_course()
    {
        $courseModel = new Course();

        $data = [
            'course_name' => $this->request->getPost('course_name'),
            'credits' => $this->request->getPost('credits'),
        ];

        if ($courseModel->insert($data)){
            return redirect()->to(base_url('admin/courses'))->with('success','Course: ' . $data['course_name'] . ' Berhasil ditambahkan!');
        } else {
            return redirect()->back()->with('error','Gagal menambahkan course!');
        }
    }

    public function delete_course($id)
    {
        $courseModel = new Course();
        $takesModel = new Takes();

        // cari student berdasarkan user_id
        $take = $takesModel->where('course_id', $id)->delete();

        if ($courseModel->delete($id)) {
            return redirect()->to(base_url('admin/courses'))
                ->with('success', 'Data course berhasil dihapus!');
        } else {
            return redirect()->to(base_url('admin/courses'))
                ->with('error', 'Data course gagal dihapus!');
        }

    }

    public function edit_course($id)
    {
        $courseModel = new Course();
        $course = $courseModel->find($id);

        $data = [
            'title' => 'Edit Course',
            'content' => view('edit_course', ['course' => $course])
        ];

        return view('template_admin', $data);
    }

    public function update_course($id)
    {
        $courseModel = new Course();
        
        $course_name = trim($this->request->getPost('course_name'));
        $credits = trim($this->request->getPost('credits'));


        $data = [
            'course_name' => $this->request->getPost('course_name'),
            'credits' => $this->request->getPost('credits'),
        ];

        if (empty($course_name) && empty($credits)) {
            return redirect()->back()->with('error', 'Tidak ada data yang diubah, minimal isi salah satu field!');
        }

        if($courseModel->update($id, $data)) {
            return redirect()->to(base_url('admin/courses'))->with('success','Data course: ' . $data['course_name'] . ' Berhasil diupdate!');
        } else {
            return redirect()->to(base_url('admin/courses'))->with('error', 'Data course: ' . $data['course_name'] . 'gagal diupdate!');
        }
    }

    public function add_student()
    {
        $data = [
            'title' => 'Add Students',
            'content' => view('add_student')
        ];

        return view('template_admin', $data);
    }

    public function save_student()
    {
        $userModel = new User();
        $employeeModel = new Employee();

        // Ambil data dari form
        $userData = [
            'first_name'  => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'username'   => $this->request->getPost('username'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => 'pekerja'
        ];

        // Insert ke tabel users
        if ($userModel->insert($userData)) {
            $userId = $userModel->getInsertID(); // Ambil ID user yang baru dibuat

            // Insert ke tabel students (pakai user_id sebagai FK)
            $employeeData = [
                'user_id'    => $userId,
                'pekerjaan' => $this->request->getPost('pekerjaan'),
                'no_telepon' => $this->request->getPost('no_telepon')
            ];

            if ($employeeModel->insert($employeeData)) {
                return redirect()->to(base_url('admin/mahasiswa'))
                                ->with('success', 'Mahasiswa ' . $userData['first_name'] . ' berhasil ditambahkan!');
            } else {
                return redirect()->back()
                                ->with('error', 'Gagal menambahkan data mahasiswa!');
            }

        } else {
            return redirect()->back()
                            ->with('error', 'Gagal menambahkan data user!');
        }
    }

    public function edit_student($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('students');
        $builder->select('students.student_id, students.entry_year, users.user_id, users.username, users.full_name');
        $builder->join('users', 'users.user_id = students.user_id');
        $builder->where('users.user_id', $id); // ambil data sesuai id user
        $student = $builder->get()->getRowArray();

        $data = [
            'title' => 'Edit Student',
            'content'=> view('edit_student', ['student' => $student]),
        ];

        return view('template_admin', $data);
    }


    public function update_student()
    {
        $id = $this->request->getPost('user_id');

        $userModel = new User();
        $studentModel = new Students();

        $userData= [
            'username'=> $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
        ];

        if ($userModel->update($id,$userData)) 
        {
            $studentData = [
                'entry_year'=> $this->request->getPost('entry_year')
            ];

             if($studentModel->where('user_id', $id)->set($studentData)->update())
            {
                return redirect()->to(base_url('admin/mahasiswa'))->with('success', 'Data mahasiswa dengan nama ' . $userData['full_name'] . ' berhasil diupdate!');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui data mahasiswa!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data mahasiswa');
        }
       
    } 

    public function delete_student($id)
    {
        $studentModel = new Students();
        $userModel = new User();

        // cari student berdasarkan user_id
        $student = $studentModel->where('user_id', $id)->first();

        if ($student) {
            $studentModel->where('user_id', $id)->delete();

            $userModel->delete($id);

            return redirect()->to(base_url('admin/mahasiswa'))
                ->with('success', 'Data mahasiswa berhasil dihapus!');
        } else {
            return redirect()->to(base_url('admin/mahasiswa'))
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }
    }
}

?>