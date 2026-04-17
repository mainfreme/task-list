<?php

declare(strict_types=1);

namespace App\Crm\Domain\Aggregate;

use App\Crm\Domain\Entity\Internal\Address;
use App\Crm\Domain\Entity\Internal\ClientContact;
use App\Crm\Domain\Entity\Internal\ClientNoteEntry;
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

/**
 * Agregat klienta CRM z powiązanymi encjami (adresy, kontakty, tagi, konta).
 */
final class CrmClientAggregate
{
    /**
     * @param list<Address>           $addresses
     * @param list<ClientContact>     $contacts
     * @param list<ClientTag>         $tags
     * @param list<CompanyAccount>      $accounts
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
        private array $addresses,
        private array $contacts,
        private array $tags,
        private ?ClientNoteEntry $clientNote,
        private array $accounts,
        private IsDeleted $isDeleted,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        ClientName $name,
        Nip $nip,
        Country $country,
        ClientStatus $status,
        IsCompany $isCompany,
        ?Regon $regon = null,
        ?Pesel $pesel = null,
        ?ClientSource $source = null,
        ?ClientRating $rating = null,
        ?ClientNotes $notes = null,
        ?Uuid $addressUuid = null,
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            id: Uuid::generate(),
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
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: $addressUuid,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<Address>       $addresses
     * @param list<ClientContact> $contacts
     * @param list<ClientTag>     $tags
     * @param list<CompanyAccount>  $accounts
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
        array $addresses,
        array $contacts,
        array $tags,
        ?ClientNoteEntry $clientNote,
        array $accounts,
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
            clientNote: $clientNote,
            accounts: $accounts,
            isDeleted: $isDeleted,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

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
     * @return list<ClientContact>
     */
    public function getContacts(): array
    {
        return array_values($this->contacts);
    }

    /**
     * @return list<ClientTag>
     */
    public function getTags(): array
    {
        return array_values($this->tags);
    }

    public function getNote(): ?ClientNoteEntry
    {
        return $this->clientNote;
    }

    /**
     * @return list<CompanyAccount>
     */
    public function getAccounts(): array
    {
        return array_values($this->accounts);
    }

    public function addNote(ClientNoteEntry $clientNote): self
    {
        $this->clientNote = $clientNote;

        return $this;
    }

    public function removeNote(): self
    {
        if ($this->clientNote === null) {
            return $this;
        }

        $this->clientNote->softDelete();

        return $this;
    }

    public function addAddress(Address $address): self
    {
        $this->addresses[] = $address;

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        $this->addresses = array_values(array_filter(
            $this->addresses,
            static fn (Address $a) => $a !== $address
        ));

        return $this;
    }

    public function addContact(ClientContact $contact): self
    {
        $this->contacts[] = $contact;

        return $this;
    }

    public function removeContact(ClientContact $contact): self
    {
        $this->contacts = array_values(array_filter(
            $this->contacts,
            static fn (ClientContact $c) => $c !== $contact
        ));

        return $this;
    }

    public function addTag(ClientTag $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function removeTag(ClientTag $tag): self
    {
        $this->tags = array_values(array_filter(
            $this->tags,
            static fn (ClientTag $t) => $t !== $tag
        ));

        return $this;
    }

    public function addAccount(CompanyAccount $account): self
    {
        $this->accounts[] = $account;

        return $this;
    }

    public function removeAccount(CompanyAccount $account): self
    {
        $this->accounts = array_values(array_filter(
            $this->accounts,
            static fn (CompanyAccount $a) => $a !== $account
        ));

        return $this;
    }
}
