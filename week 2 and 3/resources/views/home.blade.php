<x-layouts.app title="Home">
    <h1>Training Institute Management System</h1>
    <p>Manage students and courses.</p>
    <a class="btn" href="{{ route('students.index') }}">Manage Students</a>
    <a class="btn" href="{{ route('courses.index') }}">Manage Courses</a>
</x-layouts.app>
