<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Jadwal;
use App\Models\User;

class Home extends BaseController
{
    public function student()
    {
        $session = session();
        $user_id = $session->get("user_id");

        $userModel = new User();
        $employeeModel = new Employee();

        $employee = $employeeModel->where('user_id', $user_id)->first();
        $employee_id = $employee['employee_id'];

        $JadwalModel = new Jadwal();
        $dataCourse['shift'] = $JadwalModel->get_shift_by_employee($employee_id);

        $data = [
            'title' => 'Home',
            'content' => view('student_home', $dataCourse)
        ];

        return view('template', $data);

        
    }
}
