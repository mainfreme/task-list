<?php

declare(strict_types=1);

namespace App\Profile\Application\DTO;

use App\Profile\Domain\ValueObject\ProfileId;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;

final class ProfileDTO
{
    public function __construct(
        public readonly ProfileId $id,
        public readonly Uuid $userId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly Phone $phone,
        public readonly ?string $avatar = null,
        public readonly ?\DateTimeImmutable $birthDate = null,
    ) {
    }

    public function toJson(): string
    {
        return json_encode([
            'id' => $this->id->getValue(),
            'userId' => $this->userId->getValue(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone->getValue(),
            'avatar' => $this->avatar,
            'birthDate' => $this->birthDate->format('Y-m-d'),
        ]);
    }
}
