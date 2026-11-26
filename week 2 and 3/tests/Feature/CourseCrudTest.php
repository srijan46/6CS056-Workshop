<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_resource_routes_render_the_shared_layout_and_complete_crud(): void
    {
        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Training Institute Management System')
            ->assertSee('No courses found.');

        $this->get(route('courses.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="difficulty"', false)
            ->assertSee('name="is_active"', false);

        $attributes = [
            'name' => 'Laravel Basics',
            'description' => 'A course description',
            'duration' => 4,
            'fee' => '125.50',
            'difficulty' => 'Easy',
            'is_active' => 1,
        ];

        $this->post(route('courses.store'), $attributes)
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Course Laravel Basics created successfully!');

        $course = Course::query()->where('name', $attributes['name'])->firstOrFail();

        $this->assertDatabaseHas('courses', $attributes);

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Laravel Basics')
            ->assertSee('Training Institute Management System');

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('125.50');

        $this->get(route('courses.edit', $course))
            ->assertOk()
            ->assertSee('value="Laravel Basics"', false);

        $updatedAttributes = [
            ...$attributes,
            'name' => 'Laravel Advanced',
            'is_active' => 0,
        ];

        $this->put(route('courses.update', $course), $updatedAttributes)
            ->assertRedirect(route('courses.show', $course))
            ->assertSessionHas('success', 'Course updated successfully!');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Laravel Advanced',
            'is_active' => false,
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Course deleted successfully!');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_validation_rejects_invalid_duration_fee_and_difficulty(): void
    {
        $this->from(route('courses.create'))
            ->followingRedirects()
            ->post(route('courses.store'), [
                'name' => 'Invalid Course',
                'duration' => 0,
                'fee' => -1,
                'difficulty' => 'Impossible',
            ])
            ->assertOk()
            ->assertSee('The duration field must be at least 1.')
            ->assertSee('The fee field must be at least 0.')
            ->assertSee('The selected difficulty is invalid.')
            ->assertSee('value="Invalid Course"', false);

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_course_resource_binding_returns_not_found_for_missing_courses(): void
    {
        $this->get(route('courses.show', 999999))->assertNotFound();
    }

    public function test_course_content_is_escaped_in_the_details_page(): void
    {
        $scriptTag = '<script>alert(1)</script>';
        $course = Course::create([
            'name' => $scriptTag,
            'description' => $scriptTag,
            'duration' => 1,
            'fee' => '0.00',
            'difficulty' => 'Easy',
        ]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee($scriptTag, false);
    }
}
