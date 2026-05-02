@extends('layouts.app', ['title' => 'Student Dashboard'])

@section('content')
    <section class="shell">
        <div class="grid">
            <div class="panel">
                <span class="badge">Student</span>
                <h1>{{ $student->name }}</h1>
                <p class="muted">{{ $student->studentProfile?->course }} · {{ $student->studentProfile?->department }} · Roll No {{ $student->studentProfile?->roll_number }}</p>
                <p><strong>Current semester:</strong> {{ $currentSemester?->name ?? 'Not assigned' }}</p>
                <p><strong>Section:</strong> {{ $student->studentProfile?->section ?? 'N/A' }}</p>
            </div>
            <div class="panel">
                <h3>Attendance overview</h3>
                <p class="metric">{{ number_format($attendanceSummary->avg('percentage') ?? 0, 1) }}%</p>
                <p class="muted">Average attendance across your current subjects.</p>
            </div>
            <div class="panel">
                <h3>Instant alerts</h3>
                <p class="metric">{{ $alerts->count() }}</p>
                <p class="muted">Active academic or administrative notifications.</p>
            </div>
            <div class="panel">
                <h3>Study materials</h3>
                <p class="metric">{{ $studyMaterials->count() }}</p>
                <p class="muted">Teacher-uploaded files available for download.</p>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <div class="section-title">
                    <h2>Attendance by subject</h2>
                    <span class="badge">{{ $attendanceSummary->count() }} subjects</span>
                </div>
                <table>
                    <thead>
                        <tr><th>Subject</th><th>Present</th><th>Total</th><th>Percentage</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($attendanceSummary as $subject => $summary)
                            <tr>
                                <td>{{ $subject }}</td>
                                <td>{{ $summary['present'] }}</td>
                                <td>{{ $summary['total'] }}</td>
                                <td>{{ $summary['percentage'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No attendance records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <div class="section-title">
                    <h2>Recent marks</h2>
                    <span class="badge">{{ $recentMarks->count() }} entries</span>
                </div>
                <table>
                    <thead>
                        <tr><th>Assessment</th><th>Subject</th><th>Score</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMarks as $mark)
                            <tr>
                                <td>{{ $mark->assessment_name }}</td>
                                <td>{{ $mark->subject?->title }}</td>
                                <td>{{ rtrim(rtrim(number_format($mark->marks_obtained, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($mark->max_marks, 2), '0'), '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No marks uploaded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Semester-wise grade cards</h2>
                <table>
                    <thead>
                        <tr><th>Semester</th><th>GPA</th><th>CGPA</th><th>Summary</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($student->gradeCards as $gradeCard)
                            <tr>
                                <td>{{ $gradeCard->semester?->name }}</td>
                                <td>{{ $gradeCard->gpa }}</td>
                                <td>{{ $gradeCard->cgpa }}</td>
                                <td>{{ $gradeCard->grade_summary }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No grade cards available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <h2>Calendar and birthday reminders</h2>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Event</th><th>Message</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($calendarEvents as $event)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($event->event_date)->format('d M Y') }}</td>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->dialog_message ?? 'No popup message' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No calendar events configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Medical leave request</h2>
                <form method="POST" action="{{ route('student.medical-leaves.store') }}">
                    @csrf
                    <div class="field">
                        <label for="from_date">From date</label>
                        <input id="from_date" type="date" name="from_date" required>
                    </div>
                    <div class="field">
                        <label for="to_date">To date</label>
                        <input id="to_date" type="date" name="to_date" required>
                    </div>
                    <div class="field">
                        <label for="reason">Reason</label>
                        <textarea id="reason" name="reason" required></textarea>
                    </div>
                    <button type="submit">Apply for medical leave</button>
                </form>

                <table>
                    <thead>
                        <tr><th>From</th><th>To</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($student->medicalLeaves as $leave)
                            <tr>
                                <td>{{ $leave->from_date->format('d M Y') }}</td>
                                <td>{{ $leave->to_date->format('d M Y') }}</td>
                                <td>{{ ucfirst($leave->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No leave requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <h2>Choose electives</h2>
                <form method="POST" action="{{ route('student.electives.store') }}">
                    @csrf
                    <div class="field">
                        <label for="elective_id">Available elective</label>
                        <select id="elective_id" name="elective_id" required>
                            <option value="">Select an elective</option>
                            @foreach ($electives as $elective)
                                <option value="{{ $elective->id }}">{{ $elective->title }} ({{ $elective->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit">Confirm elective</button>
                </form>

                <table>
                    <thead>
                        <tr><th>Elective</th><th>Semester</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($student->electiveEnrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->elective?->title }}</td>
                                <td>{{ $enrollment->elective?->semester?->name }}</td>
                                <td>{{ ucfirst($enrollment->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No electives chosen yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Apply for auxiliary exam</h2>
                <form method="POST" action="{{ route('student.auxiliary-exams.store') }}">
                    @csrf
                    <div class="field">
                        <label for="subject_id">Subject</label>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">Select a subject</option>
                            @foreach ($auxiliaryEligibleSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="aux_reason">Reason</label>
                        <textarea id="aux_reason" name="reason" required></textarea>
                    </div>
                    <button type="submit">Submit auxiliary exam request</button>
                </form>
            </div>

            <div class="panel">
                <h2>Semester-wise fees payment</h2>
                <table>
                    <thead>
                        <tr><th>Semester</th><th>Due</th><th>Paid</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($student->feePayments as $fee)
                            <tr>
                                <td>{{ $fee->semester?->name }}</td>
                                <td>{{ number_format($fee->amount_due, 2) }}</td>
                                <td>{{ number_format($fee->amount_paid, 2) }}</td>
                                <td>{{ ucfirst($fee->status) }}</td>
                                <td>
                                    @if ($fee->status !== 'paid')
                                        <form method="POST" action="{{ route('student.fees.pay', $fee) }}">
                                            @csrf
                                            <button type="submit">Pay now</button>
                                        </form>
                                    @else
                                        Paid
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No fee records available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <h2>Notifications and alerts</h2>
                @forelse ($alerts as $alert)
                    <div class="panel alert-card alert-{{ $alert->level }}" style="padding: 1rem; margin-bottom: 0.8rem;">
                        <div class="section-title">
                            <strong>{{ $alert->title }}</strong>
                            <span class="badge">{{ strtoupper($alert->level) }}</span>
                        </div>
                        <p>{{ $alert->message }}</p>
                    </div>
                @empty
                    <p class="muted">No alerts available.</p>
                @endforelse
            </div>

            <div class="panel">
                <h2>Study materials</h2>
                <table>
                    <thead>
                        <tr><th>Title</th><th>Subject</th><th>Type</th><th>Download</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($studyMaterials as $material)
                            <tr>
                                <td>{{ $material->title }}</td>
                                <td>{{ $material->subject?->title }}</td>
                                <td>{{ strtoupper($material->file_type) }}</td>
                                <td><a class="btn secondary" href="{{ route('student.materials.download', $material) }}">Download</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No study materials uploaded yet. Ask staff to upload PDF, Word, Excel, or PowerPoint files.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @if ($birthdayDialog)
        <dialog id="birthday-dialog">
            <div class="dialog-body">
                <h2>Birthday Reminder</h2>
                <p>{{ $birthdayDialog }}</p>
                <button type="button" onclick="document.getElementById('birthday-dialog').close()">Close</button>
            </div>
        </dialog>
    @endif
@endsection

@section('scripts')
    @if ($birthdayDialog)
        <script>
            window.addEventListener('load', () => {
                document.getElementById('birthday-dialog')?.showModal();
            });
        </script>
    @endif
@endsection
