@extends('layouts.app', ['title' => 'Staff Dashboard'])

@section('content')
    <section class="shell">
        <div class="grid">
            <div class="panel">
                <span class="badge">Staff</span>
                <h1>{{ $staff->name }}</h1>
                <p class="muted">{{ $staff->staffProfile?->designation }} · {{ $staff->staffProfile?->department }}</p>
                <p><strong>Employee code:</strong> {{ $staff->staffProfile?->employee_code }}</p>
            </div>
            <div class="panel">
                <h3>Subjects assigned</h3>
                <p class="metric">{{ $subjects->count() }}</p>
            </div>
            <div class="panel">
                <h3>Students available</h3>
                <p class="metric">{{ $students->count() }}</p>
            </div>
            <div class="panel">
                <h3>Recent alerts</h3>
                <p class="metric">{{ $recentAlerts->count() }}</p>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Mark attendance</h2>
                <form method="POST" action="{{ route('staff.attendance.store') }}">
                    @csrf
                    <div class="field">
                        <label for="attendance_student">Student</label>
                        <select id="attendance_student" name="student_user_id" required>
                            <option value="">Select student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->studentProfile?->roll_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="attendance_subject">Subject</label>
                        <select id="attendance_subject" name="subject_id" required>
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }} ({{ $subject->semester?->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="attendance_date">Date</label>
                        <input id="attendance_date" type="date" name="attendance_date" required>
                    </div>
                    <div class="field">
                        <label for="attendance_status">Status</label>
                        <select id="attendance_status" name="status" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="medical">Medical</option>
                        </select>
                    </div>
                    <button type="submit">Save attendance</button>
                </form>
            </div>

            <div class="panel">
                <h2>Upload marks</h2>
                <form method="POST" action="{{ route('staff.marks.store') }}">
                    @csrf
                    <div class="field">
                        <label for="marks_student">Student</label>
                        <select id="marks_student" name="student_user_id" required>
                            <option value="">Select student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->studentProfile?->roll_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="marks_subject">Subject</label>
                        <select id="marks_subject" name="subject_id" required>
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="assessment_name">Assessment name</label>
                        <input id="assessment_name" type="text" name="assessment_name" required>
                    </div>
                    <div class="field">
                        <label for="max_marks">Max marks</label>
                        <input id="max_marks" type="number" step="0.01" name="max_marks" required>
                    </div>
                    <div class="field">
                        <label for="marks_obtained">Marks obtained</label>
                        <input id="marks_obtained" type="number" step="0.01" name="marks_obtained" required>
                    </div>
                    <button type="submit">Upload marks</button>
                </form>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Upload subject content</h2>
                <p class="muted">Accepted file formats: Excel, PDF, Word, and PowerPoint.</p>
                <form method="POST" action="{{ route('staff.materials.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label for="material_subject">Subject</label>
                        <select id="material_subject" name="subject_id" required>
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="material_title">Title</label>
                        <input id="material_title" type="text" name="title" required>
                    </div>
                    <div class="field">
                        <label for="material_description">Description</label>
                        <textarea id="material_description" name="description"></textarea>
                    </div>
                    <div class="field">
                        <label for="material_file">File</label>
                        <input id="material_file" type="file" name="file" required>
                    </div>
                    <button type="submit">Upload study material</button>
                </form>
            </div>

            <div class="panel">
                <h2>Publish instant alert</h2>
                <form method="POST" action="{{ route('staff.alerts.store') }}">
                    @csrf
                    <div class="field">
                        <label for="alert_title">Title</label>
                        <input id="alert_title" type="text" name="title" required>
                    </div>
                    <div class="field">
                        <label for="audience">Audience</label>
                        <select id="audience" name="audience" required>
                            <option value="student">Students</option>
                            <option value="staff">Staff</option>
                            <option value="all">Everyone</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="level">Level</label>
                        <select id="level" name="level" required>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="alert_message">Message</label>
                        <textarea id="alert_message" name="message" required></textarea>
                    </div>
                    <button type="submit">Send alert</button>
                </form>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Assigned subjects</h2>
                <table>
                    <thead>
                        <tr><th>Code</th><th>Subject</th><th>Semester</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->title }}</td>
                                <td>{{ $subject->semester?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No subjects assigned yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <h2>Recent uploads and alerts</h2>
                <table>
                    <thead>
                        <tr><th>Type</th><th>Title</th><th>Context</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMaterials as $material)
                            <tr>
                                <td>Material</td>
                                <td>{{ $material->title }}</td>
                                <td>{{ $material->subject?->title }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No material uploads yet.</td></tr>
                        @endforelse
                        @foreach ($recentAlerts as $alert)
                            <tr>
                                <td>Alert</td>
                                <td>{{ $alert->title }}</td>
                                <td>{{ ucfirst($alert->audience) }} · {{ ucfirst($alert->level) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
