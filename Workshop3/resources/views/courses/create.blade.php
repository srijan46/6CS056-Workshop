
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
</head>
<body>
    <h1>Add Course</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('courses.store') }}" method="POST">
        @csrf

        <p>
            <label for="name">Course Name:</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name') }}" required>
        </p>

        <p>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
        </p>

        <p>
            <label for="duration">Duration (weeks):</label>
            <input id="duration" name="duration" type="number"
                   min="1" value="{{ old('duration') }}" required>
        </p>

        <p>
            <label for="fee">Fee:</label>
            <input id="fee" name="fee" type="number"
                   min="0" max="99999999.99" step="0.01"
                   value="{{ old('fee') }}" required>
        </p>

        <p>
            <label for="difficulty">Difficulty:</label>
            <select id="difficulty" name="difficulty" required>
                <option value="">Select difficulty</option>
                <option value="Easy" {{ old('difficulty') == 'Easy' ? 'selected' : '' }}>Easy</option>
                <option value="Medium" {{ old('difficulty') == 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="Hard" {{ old('difficulty') == 'Hard' ? 'selected' : '' }}>Hard</option>
            </select>
        </p>

        <p>
            <label for="is_active">Status:</label>
            <select id="is_active" name="is_active" required>
                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </p>

        <button type="submit">Save Course</button>
    </form>

    <br>
    <a href="{{ route('courses.index') }}">Back to Courses</a>
</body>
</html>