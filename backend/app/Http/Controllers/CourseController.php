<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $courseService) {}

    public function index(): AnonymousResourceCollection
    {
        return CourseResource::collection(
            Course::query()->orderBy(Course::SORTABLE_COLUMN)->get()
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CourseStoreRequest $request): CourseResource
    {
        $course = $this->courseService->create($request->validated());

        return new CourseResource($course);
    }

    public function show(Course $course): CourseResource
    {
        return new CourseResource($course);
    }

    /**
     * @throws Throwable
     */
    public function update(CourseUpdateRequest $request, Course $course): CourseResource
    {
        $course = $this->courseService->update(
            course: $course,
            attributes: $request->validated(),
        );

        return new CourseResource($course);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Course $course): Response
    {
        $this->courseService->delete($course);

        return response()->noContent();
    }
}
