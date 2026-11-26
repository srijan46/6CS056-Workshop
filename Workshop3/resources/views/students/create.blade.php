<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
</head>
<body>
    <h1>Add Student</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('students.store') }}" method="POST">
        @csrf

        <p>
            <label for="name">Name:</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name') }}" required>
        </p>

        <p>
            <label for="email">Email:</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}" required>
        </p>

        <p>
            <label for="phone">Phone:</label>
            <input id="phone" name="phone" type="text"
                   value="{{ old('phone') }}" required>
        </p>

        <p>
            <label for="address">Address:</label>
            <textarea id="address" name="address">{{ old('address') }}</textarea>
        </p>

        <p>
            <label for="date_of_birth">Date of Birth:</label>
            <input id="date_of_birth" name="date_of_birth" type="date"
                   value="{{ old('date_of_birth') }}"
                   max="{{ date('Y-m-d') }}">
        </p>

        <button type="submit">Save Student</button>
    </form>

    <br>
    <a href="{{ route('students.index') }}">Back to Students</a>
</body>
</html>