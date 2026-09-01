<?php declare(strict_types=1);

namespace App\Services\WordPress;

use App\Repositories\WordPress\WordPressRegistrationRepository;
use Illuminate\Support\Collection;

final class WordPressBridgeService
{
    public function __construct(private readonly WordPressRegistrationRepository $repository)
    {
    }

    public function getRegistrations(): Collection
    {
        return $this->repository->all()->each(static fn ($registration) => $registration->decryptPii());
    }

    public function createRegistration(array $data): array
    {
        $registration = $this->repository->create($data);
        return $registration->toArray();
    }

    public function updateRegistration(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteRegistration(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
