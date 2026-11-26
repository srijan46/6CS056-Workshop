<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_resource_routes_render_the_shared_layout_and_complete_crud(): void
    {
        $this->get(route('students.index'))
            ->assertOk()
            ->assertSee('Training Institute Management System')
            ->assertSee('No students found.');

        $this->get(route('students.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="date_of_birth"', false);

        $attributes = [
            'name' => 'Avery Student',
            'email' => 'avery@example.test',
            'phone' => '555-0101',
            'address' => '12 Main Street',
            'date_of_birth' => '2000-01-02',
        ];

        $this->post(route('students.store'), $attributes)
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success', 'Student Avery Student created successfully!');

        $student = Student::query()->where('email', $attributes['email'])->firstOrFail();

        $this->assertDatabaseHas('students', [
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'phone' => $attributes['phone'],
            'address' => $attributes['address'],
        ]);

        $this->get(route('students.index'))
            ->assertOk()
            ->assertSee('Avery Student')
            ->assertSee('Training Institute Management System');

        $this->travelTo(now()->setDate(2025, 1, 1), function () use ($student): void {
            $this->get(route('students.show', $student))
                ->assertOk()
                ->assertSee('02 Jan 2000')
                ->assertSee('<strong>Age:</strong> 24', false);
        });

        $this->get(route('students.edit', $student))
            ->assertOk()
            ->assertSee('value="2000-01-02"', false);

        $updatedAttributes = [
            ...$attributes,
            'name' => 'Avery Updated',
        ];

        $this->put(route('students.update', $student), $updatedAttributes)
            ->assertRedirect(route('students.show', $student))
            ->assertSessionHas('success', 'Student updated successfully!');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Avery Updated',
            'email' => $attributes['email'],
        ]);

        $this->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success', 'Student deleted successfully!');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_validation_rejects_duplicate_emails_without_changing_records(): void
    {
        $existingStudent = Student::create([
            'name' => 'Existing Student',
            'email' => 'existing@example.test',
            'phone' => '555-0102',
        ]);
        Student::create([
            'name' => 'Another Student',
            'email' => 'taken@example.test',
            'phone' => '555-0104',
        ]);

        $this->from(route('students.create'))
            ->followingRedirects()
            ->post(route('students.store'), [
                'name' => 'Duplicate Student',
                'email' => 'existing@example.test',
                'phone' => '555-0103',
            ])
            ->assertOk()
            ->assertSee('The email has already been taken.')
            ->assertSee('value="Duplicate Student"', false);

        $this->from(route('students.edit', $existingStudent))
            ->followingRedirects()
            ->put(route('students.update', $existingStudent), [
                'name' => 'Changed Student',
                'email' => 'taken@example.test',
                'phone' => '555-0102',
            ])
            ->assertOk()
            ->assertSee('The email has already been taken.')
            ->assertSee('value="taken@example.test"', false);

        $this->assertDatabaseHas('students', [
            'id' => $existingStudent->id,
            'name' => 'Existing Student',
            'email' => 'existing@example.test',
        ]);
    }

    public function test_student_resource_binding_returns_not_found_for_missing_students(): void
    {
        $this->get(route('students.show', 999999))->assertNotFound();
    }

    public function test_student_content_is_escaped_in_the_details_page(): void
    {
        $scriptTag = '<script>alert(1)</script>';
        $student = Student::create([
            'name' => $scriptTag,
            'email' => 'script@example.test',
            'phone' => '555-0105',
            'address' => $scriptTag,
        ]);

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee($scriptTag, false);
    }
}
