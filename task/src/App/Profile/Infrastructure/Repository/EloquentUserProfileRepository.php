<?php

declare(strict_types=1);

namespace App\Profile\Infrastructure\Repository;

use App\Profile\Domain\Repository\UserProfileRepository;
use App\Profile\Domain\ValueObject\ProfileId;
use App\Profile\Domain\Entity\Profile;
use App\Shared\Domain\ValueObject\Uuid;
use App\Profile\Infrastructure\Model\ProfileModel;
use App\Profile\UI\Http\Mappers\ProfileMapper;

final class EloquentUserProfileRepository implements UserProfileRepository
{
    public function findByUserId(Uuid $userId): ?Profile
    {
        $model = ProfileModel::query()
            ->where('user_id', $userId->getValue())
            ->first();

    return $model ? ProfileMapper::toDomain($model) : null;
    }

    public function findByProfileId(ProfileId $profileId): ?Profile
    {
        $model = ProfileModel::query()
            ->where('id', $profileId->value())
            ->first();

        return $model ? ProfileMapper::toDomain($model) : null;
    }

    public function save(Profile $profile): void
    {
        try {
            $model = ProfileModel::find($profile->getId()->getValue()) ?? new ProfileModel();

            $model->id = $profile->getId()->getValue();
            $model->user_id = $profile->getUserId()->getValue();
            $model->first_name = $profile->getFirstName();
            $model->last_name = $profile->getLastName();
            $model->phone = $profile->getPhone()->getValue();
            $model->avatar = $profile->getAvatar();
            $model->birth_date = $profile->getBirthDate()->format('Y-m-d');

            $model->save();
        } catch (\Exception $e) {
            throw new \Exception('Failed to save profile: ' . $e->getMessage());
        }
    }
}
