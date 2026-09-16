<?php declare(strict_types=1);

namespace App\Http\Controllers\WordPress;

use App\Services\WordPress\WordPressBridgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WordPressBridgeController
{
    public function __construct(private readonly WordPressBridgeService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->service->getRegistrations());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'registration_uuid' => 'nullable|string',
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'status' => 'nullable|string',
            'created_at' => 'nullable|string',
        ]);

        return response()->json($this->service->createRegistration($data), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => 'nullable|string',
        ]);

        return response()->json(['updated' => $this->service->updateRegistration($id, $data)]);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['deleted' => $this->service->deleteRegistration($id)]);
    }
}
