<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageStoreRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\ContactMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ContactMessageController extends Controller
{
    public function __construct(private readonly ContactMessageService $contactMessages) {}

    public function form(): JsonResponse
    {
        return $this->success([
            'form_token' => $this->contactMessages->issueToken(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(ContactMessageStoreRequest $request): JsonResponse
    {
        $this->authorize('create', ContactMessage::class);

        $this->contactMessages->accept($request->validated());

        return $this->success(
            message: 'Thanks, your message was sent.',
            status: Response::HTTP_CREATED,
        );
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ContactMessage::class);

        return ContactMessageResource::collection(
            ContactMessage::query()->latest()->get()
        );
    }

    public function show(ContactMessage $contactMessage): ContactMessageResource
    {
        $this->authorize('view', $contactMessage);

        return new ContactMessageResource($contactMessage);
    }
}
