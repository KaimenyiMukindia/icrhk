<?php declare(strict_types=1);

namespace App\Repositories\WordPress;

use App\Models\WordPress\EvtRegistration;
use Illuminate\Support\Collection;

final class WordPressRegistrationRepository
{
    public function __construct(private readonly EvtRegistration $model = new EvtRegistration())
    {
    }

    public function all(): Collection
    {
        return $this->model->newQuery()->orderByDesc('created_at')->get();
    }

    public function find(int $id): ?EvtRegistration
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(array $attributes): EvtRegistration
    {
        return $this->model->newQuery()->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {
        $registration = $this->find($id);
        if ($registration === null) {
            return false;
        }

        $registration->fill($attributes);
        return $registration->save();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->newQuery()->where('id', $id)->delete();
    }
}
