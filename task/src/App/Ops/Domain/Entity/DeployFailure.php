<?php

declare(strict_types=1);

namespace App\Ops\Domain\Entity;

use App\Ops\Domain\ValueObject\DeployContainerName;
use App\Ops\Domain\ValueObject\DeployHostname;
use App\Ops\Domain\ValueObject\DeployMessage;
use App\Ops\Domain\ValueObject\DeployProjectName;
use App\Ops\Domain\ValueObject\DeployRepository;
use App\Ops\Domain\ValueObject\DeployStage;
use App\Shared\Domain\ValueObject\Uuid;

final class DeployFailure
{
    private ?Uuid $id = null;

    public function __construct(
        private DeployProjectName $project,
        private DeployRepository $repository,
        private ?DeployContainerName $container,
        private DeployStage $stage,
        private DeployMessage $message,
        private ?DeployHostname $hostname,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        DeployProjectName $project,
        DeployRepository $repository,
        ?DeployContainerName $container,
        DeployStage $stage,
        DeployMessage $message,
        ?DeployHostname $hostname,
    ): self {
        return new self(
            $project,
            $repository,
            $container,
            $stage,
            $message,
            $hostname,
        );
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getProject(): DeployProjectName
    {
        return $this->project;
    }

    public function getRepository(): DeployRepository
    {
        return $this->repository;
    }

    public function getContainer(): ?DeployContainerName
    {
        return $this->container;
    }

    public function getStage(): DeployStage
    {
        return $this->stage;
    }

    public function getMessage(): DeployMessage
    {
        return $this->message;
    }

    public function getHostname(): ?DeployHostname
    {
        return $this->hostname;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
