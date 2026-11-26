
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course List</title>
</head>
<body>
    <h1>Course List</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('courses.create') }}">Add Course</a>
    |
    <a href="{{ route('students.index') }}">Go to Students</a>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Duration (weeks)</th>
                <th>Fee</th>
                <th>Difficulty</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td>{{ $course->id }}</td>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->duration }}</td>
                    <td>{{ number_format((float) $course->fee, 2) }}</td>
                    <td>{{ $course->difficulty }}</td>
                    <td>{{ $course->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('courses.show', $course->id) }}">View</a> |
                        <a href="{{ route('courses.edit', $course->id) }}">Edit</a> |

                        <form action="{{ route('courses.destroy', $course->id) }}"
                              method="POST"
                              style="display:inline;"
                              onsubmit="return confirm('Delete this course?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No courses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>