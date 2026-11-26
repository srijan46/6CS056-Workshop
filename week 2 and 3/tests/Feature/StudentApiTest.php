<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_api_resource_supports_crud_and_returns_expected_json(): void
    {
        $attributes = [
            'name' => 'Morgan Student',
            'email' => 'morgan@example.test',
            'phone' => '555-0201',
            'address' => '34 Oak Avenue',
            'date_of_birth' => '2000-01-02',
        ];

        $this->travelTo(now()->setDate(2025, 1, 1), function () use ($attributes): void {
            $this->postJson(route('api.students.store'), $attributes)
                ->assertCreated()
                ->assertJsonPath('data.name', 'Morgan Student')
                ->assertJsonPath('data.email', 'morgan@example.test')
                ->assertJsonPath('data.date_of_birth', '2000-01-02')
                ->assertJsonPath('data.age', 24)
                ->assertJsonStructure([
                    'data' => ['id', 'name', 'email', 'phone', 'address', 'date_of_birth', 'age'],
                ]);
        });

        $student = Student::query()->where('email', $attributes['email'])->firstOrFail();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Morgan Student',
            'email' => 'morgan@example.test',
        ]);

        $this->getJson(route('api.students.index'))
            ->assertOk()
            ->assertJsonPath('data.0.email', 'morgan@example.test');

        $this->getJson(route('api.students.show', $student))
            ->assertOk()
            ->assertJsonPath('data.name', 'Morgan Student');

        $updatedAttributes = [
            ...$attributes,
            'name' => 'Morgan Updated',
        ];

        $this->putJson(route('api.students.update', $student), $updatedAttributes)
            ->assertOk()
            ->assertJsonPath('data.name', 'Morgan Updated');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Morgan Updated',
            'email' => $attributes['email'],
        ]);

        $this->deleteJson(route('api.students.destroy', $student))
            ->assertNoContent();

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->getJson(route('api.students.show', $student))->assertNotFound();
    }

    public function test_student_api_returns_422_when_required_fields_are_missing(): void
    {
        $this->postJson(route('api.students.store'), [])
            ->assertUnprocessable()
            ->assertInvalid(['name', 'email', 'phone']);

        $this->assertDatabaseCount('students', 0);
    }

    public function test_student_api_returns_422_when_email_is_already_in_use(): void
    {
        $student = Student::create([
            'name' => 'Another Student',
            'email' => 'another@example.test',
            'phone' => '555-0204',
        ]);
        Student::create([
            'name' => 'Existing Student',
            'email' => 'existing@example.test',
            'phone' => '555-0202',
        ]);

        $this->postJson(route('api.students.store'), [
            'name' => 'Duplicate Student',
            'email' => 'existing@example.test',
            'phone' => '555-0203',
        ])
            ->assertUnprocessable()
            ->assertInvalid(['email' => 'The email has already been taken.']);

        $this->putJson(route('api.students.update', $student), [
            'name' => 'Another Student',
            'email' => 'existing@example.test',
            'phone' => '555-0204',
        ])
            ->assertUnprocessable()
            ->assertInvalid(['email' => 'The email has already been taken.']);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'email' => 'another@example.test',
        ]);
        $this->assertDatabaseCount('students', 2);
    }
}
