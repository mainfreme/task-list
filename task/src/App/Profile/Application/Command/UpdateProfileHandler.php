<?php

declare(strict_types=1);

namespace App\Profile\Application\Command;

use App\Profile\Domain\Repository\UserProfileRepository;

final class UpdateProfileHandler
{
    public function __construct(
        private readonly UserProfileRepository $repository
    ) {
    }

    public function handle(UpdateProfileCommand $command): void
    {
        $profile = $command->profileDTO;

        $userProfile = $this->repository->findByProfileId($profile->id);

        if ($profile->firstName !== null) {
            $userProfile->setFirstName($profile->firstName);
        }

        if ($profile->lastName !== null) {
            $userProfile->setLastName($profile->lastName);
        }
        
        if ($profile->phone !== null) {
            $userProfile->setPhone($profile->phone);
        }

        if ($profile->avatar !== null) {
            $userProfile->setAvatar($profile->avatar);
        }

        if ($profile->birthDate !== null) {
            $userProfile->setBirthDate($profile->birthDate);
        }

        $this->repository->save($userProfile);
    }
}