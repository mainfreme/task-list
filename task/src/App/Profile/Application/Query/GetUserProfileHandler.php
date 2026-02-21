<?php

namespace App\Profile\Application\Query;

use App\Profile\Domain\Repository\UserProfileRepository;
use App\Profile\Application\DTO\ProfileDTO;

final class GetUserProfileHandler
{
    public function __construct(
        private readonly UserProfileRepository $repository
    ) {
    }

    public function handle(GetUserProfileQuery $query): ProfileDTO
    {
        $user = $this->repository->findByUserId($query->userId);
        return $user->toDTO();
    }
}
