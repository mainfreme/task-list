<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Infrastructure\Model\UserModel;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(Uuid $id): ?User
    {
        $model = UserModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::where('email', $email->getValue())->first();

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(User $user): void
    {
        $model = UserModel::find($user->getId()->getValue()) ?? new UserModel();

        $model->id = $user->getId()->getValue();
        $model->name = $user->getName();
        $model->email = $user->getEmail()->getValue();
        $model->password = $user->getPassword();
        $model->roles = $user->getRole()->value;

        $model->save();
    }

    public function exists(Email $email): bool
    {
        return UserModel::where('email', $email->getValue())->exists();
    }

    private function toEntity(UserModel $model): User
    {
        return User::fromDatabase(
            id: Uuid::fromString($model->id),
            name: $model->name ?? '',
            email: Email::fromString($model->email),
            password: $model->password,
            role: UserRoleEnum::from($model->roles),
            createdAt: $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            updatedAt: $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );
    }
}
