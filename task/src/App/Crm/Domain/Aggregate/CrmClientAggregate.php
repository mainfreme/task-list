<?php

declare(strict_types=1);

namespace App\Crm\Domain\Aggregate;

use App\Crm\Domain\Dto\ClientDto;
use App\Crm\Domain\Dto\ClientNoteDto;
use App\Crm\Domain\Entity\Internal\Address;
use App\Crm\Domain\Entity\Internal\ClientContact;
use App\Crm\Domain\Entity\Internal\ClientTag;
use App\Crm\Domain\Entity\Internal\CompanyAccount;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Collection;

/**
 * CrmClientAggregate - Aggregate root representing Client with all related entities
 *
 * This aggregate follows DDD principles:
 * - Contains collections of related entities (addresses, contacts, tags, accounts)
 * - Uses Value Objects for all properties
 * - Mutations via setters for update operations
 */
final class CrmClientAggregate
{
    /**
     * @param Collection<Address> $addresses
     * @param Collection<ClientContact> $contacts
     * @param Collection<ClientTag> $tags
     * @param Collection<CompanyAccount> $accounts
     */
    private function __construct(
        private readonly Uuid $id,
        private ClientName $name,
        private Nip $nip,
        private Country $country,
        private ClientStatus $status,
        private IsCompany $isCompany,
        private ?Regon $regon,
        private ?Pesel $pesel,
        private ?ClientSource $source,
        private ?ClientRating $rating,
        private ?ClientNotes $notes,
        private ?\DateTimeImmutable $lastContactedAt,
        private ?\DateTimeImmutable $nextContactAt,
        private ?Uuid $addressUuid,
        private Collection $addresses,
        private Collection $contacts,
        private Collection $tags,
        private ?ClientNoteDto $clientNoteDto,
        private Collection $accounts,
        private IsDeleted $isDeleted,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * Create a new client aggregate
     * Generates Uuid with UUID v7
     */
    public static function create(ClientDto $dto): self
    {
        $now = new \DateTimeImmutable();

        return new self(
            id: Uuid::generate(),
            name: $dto->name,
            nip: $dto->nip,
            country: $dto->country,
            status: $dto->status,
            isCompany: $dto->isCompany,
            regon: $dto->regon,
            pesel: $dto->pesel,
            source: $dto->source,
            rating: $dto->rating,
            notes: $dto->notes,
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: $dto->addressUuid,
            addresses: new Collection(),
            contacts: new Collection(),
            tags: new Collection(),
            clientNoteDto: null,
            accounts: new Collection(),
            isDeleted: new IsDeleted(false),
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * Reconstitute aggregate from persistence
     * Used to rebuild aggregate from database/repository with all data
     *
     * @param Collection<Address> $addresses
     * @param Collection<ClientContact> $contacts
     * @param Collection<ClientTag> $tags
     * @param Collection<CompanyAccount> $accounts
     */
    public static function reconstitute(
        Uuid $id,
        ClientName $name,
        Nip $nip,
        Country $country,
        ClientStatus $status,
        IsCompany $isCompany,
        ?Regon $regon,
        ?Pesel $pesel,
        ?ClientSource $source,
        ?ClientRating $rating,
        ?ClientNotes $notes,
        ?\DateTimeImmutable $lastContactedAt,
        ?\DateTimeImmutable $nextContactAt,
        ?Uuid $addressUuid,
        Collection $addresses,
        Collection $contacts,
        Collection $tags,
        Collection $accounts,
        ?ClientNoteDto $clientNoteDto,
        IsDeleted $isDeleted,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            name: $name,
            nip: $nip,
            country: $country,
            status: $status,
            isCompany: $isCompany,
            regon: $regon,
            pesel: $pesel,
            source: $source,
            rating: $rating,
            notes: $notes,
            lastContactedAt: $lastContactedAt,
            nextContactAt: $nextContactAt,
            addressUuid: $addressUuid,
            addresses: $addresses,
            contacts: $contacts,
            tags: $tags,
            clientNoteDto: $clientNoteDto,
            accounts: $accounts,
            isDeleted: $isDeleted,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    // Getters for main properties
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ClientName
    {
        return $this->name;
    }

    public function getNip(): Nip
    {
        return $this->nip;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getStatus(): ClientStatus
    {
        return $this->status;
    }

    public function getIsCompany(): IsCompany
    {
        return $this->isCompany;
    }

    public function getRegon(): ?Regon
    {
        return $this->regon;
    }

    public function getPesel(): ?Pesel
    {
        return $this->pesel;
    }

    public function getSource(): ?ClientSource
    {
        return $this->source;
    }

    public function getRating(): ?ClientRating
    {
        return $this->rating;
    }

    public function getNotes(): ?ClientNotes
    {
        return $this->notes;
    }

    public function getLastContactedAt(): ?\DateTimeImmutable
    {
        return $this->lastContactedAt;
    }

    public function getNextContactAt(): ?\DateTimeImmutable
    {
        return $this->nextContactAt;
    }

    public function getAddressUuid(): ?Uuid
    {
        return $this->addressUuid;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isDelete(): IsDeleted
    {
        return $this->isDeleted;
    }

    public function setName(ClientName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function setNip(Nip $nip): void
    {
        $this->nip = $nip;
        $this->touch();
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
        $this->touch();
    }

    public function setStatus(ClientStatus $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    public function setIsCompany(IsCompany $isCompany): void
    {
        $this->isCompany = $isCompany;
        $this->touch();
    }

    public function setRegon(?Regon $regon): void
    {
        $this->regon = $regon;
        $this->touch();
    }

    public function setPesel(?Pesel $pesel): void
    {
        $this->pesel = $pesel;
        $this->touch();
    }

    public function setSource(?ClientSource $source): void
    {
        $this->source = $source;
        $this->touch();
    }

    public function setRating(?ClientRating $rating): void
    {
        $this->rating = $rating;
        $this->touch();
    }

    public function setNotes(?ClientNotes $notes): void
    {
        $this->notes = $notes;
        $this->touch();
    }

    public function setAddressUuid(?Uuid $addressUuid): void
    {
        $this->addressUuid = $addressUuid;
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(): self
    {
        $this->isDeleted = IsDeleted::fromBool(true);

        return $this;
    }

    /**
     * @return Collection<ClientContact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @return Collection<ClientTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getNote(): ?ClientNoteDto
    {
        return $this->clientNoteDto;
    }

    /**
     * @return Collection<CompanyAccount>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    /**
     * Add a note to the client aggregate
     * Returns new aggregate instance with the note (immutability)
     */
    public function addNote(ClientNoteDto $clientNoteDto): self
    {
        $this->clientNoteDto = $clientNoteDto;

        return $this;
    }

    public function removeNote(): self
    {
        if ($this->clientNoteDto === null) {
            return $this;
        }

        $this->clientNoteDto->softDelete();

        return $this;
    }

    public function addAddress(Address $address): self
    {
        $this->addresses->add($address);

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        $this->addresses->remove($address);

        return $this;
    }

    public function addContact(ClientContact $contact): self
    {
        $this->contacts->add($contact);

        return $this;
    }

    public function removeContact(ClientContact $contact): self
    {
        $this->contacts->remove($contact);

        return $this;
    }

    public function addTag(ClientTag $tag): self
    {
        $this->tags->add($tag);

        return $this;
    }

    public function removeTag(ClientTag $tag): self
    {
        $this->tags->remove($tag);

        return $this;
    }

    public function addAccount(CompanyAccount $account): self
    {
        $this->accounts->add($account);

        return $this;
    }

    public function removeAccount(CompanyAccount $account): self
    {
        $this->accounts->remove($account);

        return $this;
    }
}
