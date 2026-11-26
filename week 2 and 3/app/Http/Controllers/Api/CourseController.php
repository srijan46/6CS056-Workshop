<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CourseResource::collection(Course::all());
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = Course::create($request->validated());

        return (new CourseResource($course))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Course $course): CourseResource
    {
        return new CourseResource($course);
    }

    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        $course->update($request->validated());

        return new CourseResource($course->fresh());
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json(null, 204);
    }
}
