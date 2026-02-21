<?php

declare(strict_types=1);

namespace App\Profile\Domain\Repository;

use App\Profile\Domain\Entity\Profile;
use App\Profile\Domain\ValueObject\ProfileId;
use App\Shared\Domain\ValueObject\Uuid;

interface UserProfileRepository
{
    public function findByUserId(Uuid $userId): ?Profile;

    public function findByProfileId(ProfileId $profileId): ?Profile;

    public function save(Profile $profile): void;
}
