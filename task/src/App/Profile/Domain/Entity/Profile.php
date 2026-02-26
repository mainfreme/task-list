<?php

declare(strict_types=1);

namespace App\Profile\Domain\Entity;

use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;

final class Profile
{
    public function __construct(
        private ?Uuid $id = null,
        private Uuid $userId,
        private string $firstName,
        private string $lastName,
        private Phone $phone,
        private string $avatar,
        private \DateTimeImmutable $birthDate,
    ) {
    }

    public static function create(
        Uuid $userId,
        string $firstName,
        string $lastName,
        Phone $phone,
        string $avatar,
        \DateTimeImmutable $birthDate,
    ): self {
        $profileId = Uuid::generate();

        return new self($profileId, $userId, $firstName, $lastName, $phone, $avatar, $birthDate);
    }

    public static function fromDatabase(
        Uuid $profileId,
        Uuid $userId,
        string $firstName,
        string $lastName,
        Phone $phone,
        string $avatar,
        \DateTimeImmutable $birthDate,
    ): self {
        return new self($profileId, $userId, $firstName, $lastName, $phone, $avatar, $birthDate);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
        * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }

    /**
     * @param Phone $phone
     */
    public function setPhone(Phone $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTimeImmutable $birthDate
     */
    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
