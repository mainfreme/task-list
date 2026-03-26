<?php

declare(strict_types=1);

namespace App\Settings\Application\Mapper;

use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\DTO\IntegrationAccountSummaryDto;
use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Entity\SettingEntry;

final class SettingsEntityMapper
{
    public function __construct(
        private readonly IntegrationCredentialsMasker $credentialsMasker,
    ) {
    }

    public function toChartDefinitionDto(ChartDefinition $entity): ChartDefinitionDto
    {
        return new ChartDefinitionDto(
            id: $entity->getId()->getValue(),
            chartType: $entity->getChartType(),
            displayFields: $entity->getDisplayFields(),
            sqlQuery: $entity->getSqlQuery(),
            createdAt: $this->formatDate($entity->getCreatedAt()),
            updatedAt: $this->formatDate($entity->getUpdatedAt()),
        );
    }

    public function toIntegrationAccountDto(IntegrationAccount $entity): IntegrationAccountDto
    {
        return new IntegrationAccountDto(
            id: $entity->getId()->getValue(),
            name: $entity->getName(),
            enabled: $entity->isEnabled(),
            externalAccountId: $entity->getExternalAccountId(),
            provider: $entity->getProvider(),
            credentials: $entity->getCredentials(),
            createdAt: $this->formatDate($entity->getCreatedAt()),
            updatedAt: $this->formatDate($entity->getUpdatedAt()),
        );
    }

    public function toIntegrationAccountSummaryDto(IntegrationAccount $entity): IntegrationAccountSummaryDto
    {
        return new IntegrationAccountSummaryDto(
            id: $entity->getId()->getValue(),
            name: $entity->getName(),
            enabled: $entity->isEnabled(),
            externalAccountId: $entity->getExternalAccountId(),
            provider: $entity->getProvider(),
            credentialsMasked: $this->credentialsMasker->mask($entity->getCredentials()),
            createdAt: $this->formatDate($entity->getCreatedAt()),
            updatedAt: $this->formatDate($entity->getUpdatedAt()),
        );
    }

    public function toSettingEntryDto(SettingEntry $entity): SettingEntryDto
    {
        return new SettingEntryDto(
            id: $entity->getId()->getValue(),
            groupKey: $entity->getGroupKey(),
            fieldKey: $entity->getFieldKey(),
            fieldType: $entity->getFieldType()->value,
            value: $entity->getValue(),
            createdAt: $this->formatDate($entity->getCreatedAt()),
            updatedAt: $this->formatDate($entity->getUpdatedAt()),
        );
    }

    private function formatDate(?\DateTimeImmutable $date): ?string
    {
        return $date?->format(DATE_ATOM);
    }
}
