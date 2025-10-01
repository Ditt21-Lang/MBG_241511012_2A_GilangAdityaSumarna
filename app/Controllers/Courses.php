<?php 

namespace App\Controllers;

use App\Models\Course;
use App\Models\Shift;
use App\Models\Students;
use App\Models\Takes;

class Courses extends BaseController{
    public function enroll(){
        $shiftModel = new Shift();
        $dataCourse['shift'] = $shiftModel->findAll();

        $data = [
            'title' => 'Home',
            'content' => view('enroll_course', $dataCourse)
        ];

        return view('template', $data);
    }

    public function enrollProcess(){
        $takesModel = new Takes();
        $courseModel = new Course();
        $studentModel = new Students();

        $user_id = session()->get('user_id');
        $student = $studentModel->where('user_id', $user_id)->first();
        $student_id = $student['student_id'];

        $selectedCourses = $this->request->getPost('course_id');

        if (!$selectedCourses || !is_array($selectedCourses)) {
            return redirect()->to('student/enroll')
                ->with('error', 'Silahkan pilih minimal 1 course untuk enroll.');
        }

        $enrolled = [];
        $skipped = [];

        foreach ($selectedCourses as $course_id){
            $course = $courseModel->find($course_id);

            $already = $takesModel->where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->first();
            
            if ($already){
                $skipped[] = $course['course_name'];
                continue;
            }

            $takesModel->insert([
                'course_id' => $course_id,
                'student_id' => $student_id,
                'enroll_date' => date('Y-m-d H:i:s'),
            ]);

            $enrolled[] = $course['course_name'];
        }
        if ($enrolled) {
            session()->setFlashdata('success', 'Berhasil enroll ke course: ' . implode(', ', $enrolled));
        }
        if ($skipped) {
            session()->setFlashdata('error', 'Course sudah pernah di-enroll: ' . implode(', ', $skipped));
        }
        return redirect()->to(base_url('student/enroll'));
    }
}

?>