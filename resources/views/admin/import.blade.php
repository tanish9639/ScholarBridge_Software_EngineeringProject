@extends('layouts.app', ['title' => 'Thapar LMS · Spreadsheet Import'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">Admin · Import</span>
                <h1>Bulk import from spreadsheet</h1>
                <p class="muted">Upload CSV or XLSX files to add departments, students, or teachers in bulk.</p>
            </div>
            <a class="btn small secondary" href="{{ route('admin.dashboard') }}">Back to Admin Dashboard</a>
        </div>

        <div class="cards-2">
            <div class="panel surface-accent">
                <h2>Upload spreadsheet</h2>
                <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label for="import_type">Import type</label>
                        <select id="import_type" name="import_type" required>
                            <option value="departments">Departments</option>
                            <option value="students">Students</option>
                            <option value="staff">Teachers</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="sheet">Spreadsheet file</label>
                        <input id="sheet" type="file" name="sheet" required>
                    </div>
                    <div class="field">
                        <button type="submit">Import spreadsheet</button>
                    </div>
                </form>
            </div>

            <div class="panel">
                <h2>Expected headers</h2>
                <table>
                    <tr><th>Departments</th><td><code>name</code>, <code>code</code></td></tr>
                    <tr><th>Students</th><td><code>first_name</code>, <code>middle_name</code>, <code>last_name</code>, <code>email</code>, <code>password</code>, <code>roll_number</code>, <code>course</code>, <code>department_code</code>, <code>semester_code</code>, <code>section</code>, <code>program_level</code>, <code>phone</code>, <code>birthday</code>, <code>blood_group</code>, <code>matriculation</code>, <code>guardian_phone</code>, <code>mother_name</code>, <code>father_name</code>, <code>address</code>, <code>pin_code</code>, <code>state</code>, <code>city</code>, <code>secondary_education</code>, <code>graduation</code></td></tr>
                    <tr><th>Teachers</th><td><code>first_name</code>, <code>middle_name</code>, <code>last_name</code>, <code>email</code>, <code>password</code>, <code>employee_code</code>, <code>designation</code>, <code>department_code</code>, <code>phone</code>, <code>blood_group</code>, <code>matriculation</code>, <code>guardian_phone</code>, <code>mother_name</code>, <code>father_name</code>, <code>address</code>, <code>pin_code</code>, <code>state</code>, <code>city</code>, <code>secondary_education</code>, <code>graduation</code></td></tr>
                </table>
            </div>
        </div>
    </section>
@endsection
