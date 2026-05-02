<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AttendanceRecord;
use App\Models\MarkRecord;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffPortalController extends Controller
{
    public function dashboard()
    {
        $staff = Auth::user()->load('staffProfile', 'taughtSubjects.semester');
        $subjects = $staff->taughtSubjects()->with('semester')->orderBy('title')->get();
        $students = User::with('studentProfile.currentSemester')
            ->where('role', 'student')
            ->orderBy('name')
            ->get();
        $recentMaterials = StudyMaterial::with('subject')->latest()->take(6)->get();
        $recentAlerts = Alert::latest()->take(6)->get();

        return view('staff.dashboard', compact(
            'staff',
            'subjects',
            'students',
            'recentMaterials',
            'recentAlerts',
        ));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'student_user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late,medical'],
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        AttendanceRecord::updateOrCreate(
            [
                'student_user_id' => $validated['student_user_id'],
                'subject_id' => $subject->id,
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'semester_id' => $subject->semester_id,
                'status' => $validated['status'],
                'marked_by' => Auth::id(),
            ]
        );

        return back()->with('status', 'Attendance saved successfully.');
    }

    public function storeMarks(Request $request)
    {
        $validated = $request->validate([
            'student_user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'assessment_name' => ['required', 'string', 'max:255'],
            'max_marks' => ['required', 'numeric', 'min:1'],
            'marks_obtained' => ['required', 'numeric', 'min:0'],
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        MarkRecord::create([
            ...$validated,
            'semester_id' => $subject->semester_id,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('status', 'Marks uploaded successfully.');
    }

    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:xls,xlsx,pdf,doc,docx,ppt,pptx'],
        ]);

        $file = $request->file('file');
        $path = $file->store('materials');

        StudyMaterial::create([
            'subject_id' => $validated['subject_id'],
            'uploaded_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_path' => $path,
        ]);

        return back()->with('status', 'Study material uploaded successfully.');
    }

    public function storeAlert(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'audience' => ['required', 'in:all,student,staff'],
            'level' => ['required', 'in:info,warning,critical'],
        ]);

        Alert::create([
            ...$validated,
            'created_by' => Auth::id(),
            'is_instant' => true,
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('status', 'Instant alert published.');
    }
}
