<?php

declare(strict_types=1);

namespace App\Profile\Domain\Repository;

use App\Shared\Domain\ValueObject\Uuid;
use App\Profile\Domain\ValueObject\ProfileId;
use App\Profile\Domain\Entity\Profile;

interface UserProfileRepository
{
    public function findByUserId(Uuid $userId): ?Profile;

    public function findByProfileId(ProfileId $profileId): ?Profile;

    public function save(Profile $profile): void;
}
