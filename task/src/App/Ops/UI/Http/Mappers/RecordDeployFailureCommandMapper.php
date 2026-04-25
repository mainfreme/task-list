<?php

declare(strict_types=1);

namespace App\Ops\UI\Http\Mappers;

use App\Ops\Application\Command\RecordDeployFailure\RecordDeployFailureCommand;
use App\Ops\Domain\ValueObject\DeployContainerName;
use App\Ops\Domain\ValueObject\DeployHostname;
use App\Ops\Domain\ValueObject\DeployMessage;
use App\Ops\Domain\ValueObject\DeployProjectName;
use App\Ops\Domain\ValueObject\DeployRepository;
use App\Ops\Domain\ValueObject\DeployStage;

final class RecordDeployFailureCommandMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public static function fromValidatedArray(array $data): RecordDeployFailureCommand
    {
        $text = (string) ($data['message'] ?? $data['error'] ?? '');

        return new RecordDeployFailureCommand(
            DeployProjectName::fromString($data['project']),
            DeployRepository::fromString($data['repository']),
            DeployContainerName::fromNullable($data['container'] ?? null),
            DeployStage::fromString($data['stage']),
            DeployMessage::fromString($text),
            DeployHostname::fromNullable($data['hostname'] ?? null),
        );
    }
}
