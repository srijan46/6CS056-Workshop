<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_api_resource_supports_crud_and_returns_expected_json(): void
    {
        $attributes = [
            'name' => 'API Course',
            'description' => 'Course description',
            'duration' => 6,
            'fee' => '250.50',
            'difficulty' => 'Medium',
            'is_active' => false,
        ];

        $this->postJson(route('api.courses.store'), $attributes)
            ->assertCreated()
            ->assertJsonPath('data.name', 'API Course')
            ->assertJsonPath('data.fee', '250.50')
            ->assertJsonPath('data.is_active', false)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'duration', 'fee', 'difficulty', 'is_active'],
            ]);

        $course = Course::query()->where('name', $attributes['name'])->firstOrFail();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'API Course',
            'is_active' => false,
        ]);

        $this->getJson(route('api.courses.index'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'API Course');

        $this->getJson(route('api.courses.show', $course))
            ->assertOk()
            ->assertJsonPath('data.difficulty', 'Medium');

        $updatedAttributes = [
            ...$attributes,
            'name' => 'Updated API Course',
            'is_active' => true,
        ];

        $this->putJson(route('api.courses.update', $course), $updatedAttributes)
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated API Course')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated API Course',
            'is_active' => true,
        ]);

        $this->deleteJson(route('api.courses.destroy', $course))
            ->assertNoContent();

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
        $this->getJson(route('api.courses.show', $course))->assertNotFound();
    }

    public function test_course_api_returns_422_when_values_are_outside_allowed_ranges(): void
    {
        $this->postJson(route('api.courses.store'), [
            'name' => 'Invalid API Course',
            'duration' => 0,
            'fee' => -1,
            'difficulty' => 'Expert',
        ])
            ->assertUnprocessable()
            ->assertInvalid([
                'duration' => 'The duration field must be at least 1.',
                'fee' => 'The fee field must be at least 0.',
                'difficulty' => 'The selected difficulty is invalid.',
            ]);

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_course_api_defaults_new_courses_to_active(): void
    {
        $this->postJson(route('api.courses.store'), [
            'name' => 'Default Active Course',
            'duration' => 3,
            'fee' => '75.00',
            'difficulty' => 'Easy',
        ])
            ->assertCreated()
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('courses', [
            'name' => 'Default Active Course',
            'is_active' => true,
        ]);
    }
}
