<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Mail\PasswordMail;
use App\Models\Information;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $info = Information::updateOrCreate(
                ['email_address' => $row['email_address']],
                [
                    'last_name' => $row['last_name'],
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'],
                    'gender' => $row['gender'],
                    'contact_number' => $row['contact_number'],
                ]
            );

            $user = User::whereHas('student', function ($query) use ($info) {
                $query->where('info_id', $info->id);
            })->first();

            if (!$user) {
                $password = Str::random(8);
                $user = User::create([
                    'password' => bcrypt($password),
                    'role' => 'student',
                ]);

                Mail::to($row['email_address'])->send(new PasswordMail($password));
            }

            Student::updateOrCreate(
                ['info_id' => $info->id],
                [
                    'user_id' => $user->id,
                    'id_number' => $row['id_number'],
                    'course' => $row['course'],
                    'student_type' => $row['student_type'],
                ]
            );
        }
    }
}
