<?php

declare(strict_types=1);

namespace App\Profile\UI\Http\Mappers;

use App\Profile\Application\DTO\ProfileDTO;
use App\Profile\Domain\Entity\Profile;
use App\Profile\Domain\ValueObject\ProfileId;
use App\Profile\Infrastructure\Model\ProfileModel;
use App\Profile\UI\Http\Requests\UpdateProfileRequest;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;

final class ProfileMapper
{
    public static function fromRequest(UpdateProfileRequest $request): ProfileDTO
    {
        return new ProfileDTO(
            id: ProfileId::fromString($request['id']),
            userId: Uuid::fromString($request['user_id']),
            firstName: $request['first_name'],
            lastName: $request['last_name'],
            phone: $request['phone'] === '' ? null : Phone::fromString($request['phone']),
            avatar: $request['avatar'] === '' ? null : $request['avatar'],
            birthDate: $request['birth_date'] === '' ? null : \DateTimeImmutable::createFromFormat('Y-m-d', $request['birth_date']),
        );
    }

    public static function toDomain(ProfileModel $model): Profile
    {
        return new Profile(
            id: ProfileId::fromString($model->id),
            userId: Uuid::fromString($model->user_id),
            firstName: $model->first_name,
            lastName: $model->last_name,
            phone: Phone::fromString($model->phone),
            avatar: $model->avatar,
            birthDate: $model->birth_date ? \DateTimeImmutable::createFromFormat('Y-m-d', $model->birth_date) : null,
        );
    }

    public static function toDTO(Profile $profile): ProfileDTO
    {
        return new ProfileDTO(
            id: $profile->getId(),
            userId: $profile->getUserId(),
            firstName: $profile->getFirstName(),
            lastName: $profile->getLastName(),
            phone: $profile->getPhone(),
            avatar: $profile->getAvatar(),
            birthDate: $profile->getBirthDate(),
        );
    }
}
