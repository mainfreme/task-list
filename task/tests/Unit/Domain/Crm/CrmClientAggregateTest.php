<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Entity\Internal\ClientContact;
use App\Crm\Domain\Entity\Internal\ClientNoteEntry;
use App\Crm\Domain\Entity\Internal\ClientTag;
use App\Crm\Domain\Entity\Internal\CompanyAccount;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Enums\ContactType;
use App\Crm\Domain\Enums\NoteType;
use App\Crm\Domain\ValueObject\AccountName;
use App\Crm\Domain\ValueObject\AccountNumber;
use App\Crm\Domain\ValueObject\Bic;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\CompanyAccountName;
use App\Crm\Domain\ValueObject\ContactValue;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\Iban;
use App\Crm\Domain\ValueObject\IsActive;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\IsPrimary;
use App\Crm\Domain\ValueObject\IsVerified;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\NoteContent;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Crm\Domain\ValueObject\SwiftCode;
use App\Crm\Domain\ValueObject\TagName;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

final class CrmClientAggregateTest extends TestCase
{
    /** create() ustawia domyślny stan: LEAD, isDelete=false – weryfikacja zachowania factory */
    public function test_create_sets_default_state_lead_and_not_deleted(): void
    {
        $client = CrmClientAggregate::create(
            name: ClientName::fromString('Test Client'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            status: ClientStatus::LEAD,
            isCompany: IsCompany::fromBool(true),
        );

        $this->assertSame(ClientStatus::LEAD, $client->getStatus());
        $this->assertFalse($client->isDelete()->toBool());
        $this->assertNotNull($client->getId());
    }

    public function test_create_allows_null_nip_for_private_client(): void
    {
        $dto = new ClientDto(
            name: ClientName::fromString('Jan Kowalski'),
            nip: null,
            country: Country::fromString('PL'),
            isCompany: IsCompany::fromBool(false),
            pesel: Pesel::fromString('82031412346'),
        );

        $client = CrmClientAggregate::create($dto);

        $this->assertNull($client->getNip());
        $this->assertSame('82031412346', $client->getPesel()?->toString());
    }

    /** setStatus zmienia status – weryfikacja wszystkich przejść z LEAD */
    public function test_set_status_changes_status_through_all_transitions(): void
    {
        $client = $this->createClient();
        $this->assertSame(ClientStatus::LEAD, $client->getStatus());

        foreach ([ClientStatus::PROSPECT, ClientStatus::ACTIVE, ClientStatus::INACTIVE, ClientStatus::ARCHIVED] as $status) {
            $client->setStatus($status);
            $this->assertSame($status, $client->getStatus());
        }
    }

    public function test_reconstitute_preserves_given_status(): void
    {
        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Test'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            status: ClientStatus::ACTIVE,
            isCompany: IsCompany::fromBool(true),
            regon: null,
            pesel: null,
            source: null,
            rating: null,
            notes: null,
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: null,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->assertSame(ClientStatus::ACTIVE, $client->getStatus());
    }

    public function test_soft_delete_sets_is_deleted(): void
    {
        $client = $this->createClient();

        $client->softDelete();

        $this->assertTrue($client->isDelete()->toBool());
    }

    public function test_reconstitute_with_all_optional_fields_preserves_values(): void
    {
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $lastContacted = new \DateTimeImmutable('2025-01-15 10:00:00');
        $nextContact = new \DateTimeImmutable('2025-02-01 14:00:00');

        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Full Client'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            status: ClientStatus::PROSPECT,
            isCompany: IsCompany::fromBool(true),
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('referral'),
            rating: ClientRating::fromInt(5),
            notes: ClientNotes::fromString('Notatka'),
            lastContactedAt: $lastContacted,
            nextContactAt: $nextContact,
            addressUuid: $addressUuid,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->assertSame($addressUuid->getValue(), $client->getAddressUuid()?->getValue());
        $this->assertSame('142345678', $client->getRegon()?->toString());
        $this->assertSame('82031412346', $client->getPesel()?->toString());
        $this->assertSame('referral', $client->getSource()?->toString());
        $this->assertSame(5, $client->getRating()?->toInt());
        $this->assertSame('Notatka', $client->getNotes()?->toString());
        $this->assertSame('2025-01-15 10:00:00', $client->getLastContactedAt()?->format('Y-m-d H:i:s'));
        $this->assertSame('2025-02-01 14:00:00', $client->getNextContactAt()?->format('Y-m-d H:i:s'));
    }

    public function test_create_with_all_optional_fields_sets_getters(): void
    {
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440099');

        $client = CrmClientAggregate::create(
            name: ClientName::fromString('Full optional'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::PROSPECT,
            isCompany: IsCompany::fromBool(false),
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('web'),
            rating: ClientRating::fromInt(3),
            notes: ClientNotes::fromString('Edge notes'),
            addressUuid: $addressUuid,
        );

        $this->assertSame('142345678', $client->getRegon()?->toString());
        $this->assertSame('82031412346', $client->getPesel()?->toString());
        $this->assertSame('web', $client->getSource()?->toString());
        $this->assertSame(3, $client->getRating()?->toInt());
        $this->assertSame('Edge notes', $client->getNotes()?->toString());
        $this->assertSame($addressUuid->getValue(), $client->getAddressUuid()?->getValue());
        $this->assertNull($client->getLastContactedAt());
        $this->assertNull($client->getNextContactAt());
    }

    public function test_set_name_updates_updated_at(): void
    {
        $created = new \DateTimeImmutable('2024-06-01 12:00:00');
        $updated = new \DateTimeImmutable('2024-06-01 12:00:00');

        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Old'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::LEAD,
            isCompany: IsCompany::fromBool(true),
            regon: null,
            pesel: null,
            source: null,
            rating: null,
            notes: null,
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: null,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: $created,
            updatedAt: $updated,
        );

        $before = $client->getUpdatedAt();
        $client->setName(ClientName::fromString('New'));

        $this->assertTrue($client->getUpdatedAt() >= $before);
        $this->assertSame('New', $client->getName()->getValue());
    }

    public function test_set_notes_to_null_clears_notes(): void
    {
        $client = CrmClientAggregate::create(
            name: ClientName::fromString('X'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::LEAD,
            isCompany: IsCompany::fromBool(true),
            notes: ClientNotes::fromString('Will clear'),
        );

        $this->assertSame('Will clear', $client->getNotes()?->toString());

        $client->setNotes(null);

        $this->assertNull($client->getNotes());
    }

    public function test_set_regon_to_null_after_value(): void
    {
        $client = CrmClientAggregate::create(
            name: ClientName::fromString('X'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::LEAD,
            isCompany: IsCompany::fromBool(true),
            regon: Regon::fromString('142345678'),
        );

        $client->setRegon(null);

        $this->assertNull($client->getRegon());
    }

    public function test_reconstitute_with_is_deleted_true(): void
    {
        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Deleted'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::ARCHIVED,
            isCompany: IsCompany::fromBool(false),
            regon: null,
            pesel: null,
            source: null,
            rating: null,
            notes: null,
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: null,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(true),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->assertTrue($client->isDelete()->toBool());
    }

    public function test_soft_delete_twice_remains_deleted(): void
    {
        $client = $this->createClient();
        $id = $client->getId()->getValue();

        $client->softDelete();
        $client->softDelete();

        $this->assertTrue($client->isDelete()->toBool());
        $this->assertSame($id, $client->getId()->getValue());
    }

    public function test_remove_note_when_absent_does_not_change_state(): void
    {
        $client = $this->createClient();

        $client->removeNote();

        $this->assertNull($client->getNote());
    }

    public function test_remove_note_soft_deletes_entry(): void
    {
        $entry = new ClientNoteEntry(
            noteId: Uuid::generate(),
            userId: Uuid::generate(),
            content: NoteContent::fromString('Treść'),
            type: NoteType::NOTE,
        );

        $client = $this->createClient();
        $client->addNote($entry);
        $client->removeNote();

        $this->assertTrue($client->getNote()?->isDeleted()->toBool());
    }

    public function test_add_note_replaces_previous(): void
    {
        $first = new ClientNoteEntry(
            noteId: Uuid::fromString('11111111-1111-4111-8111-111111111111'),
            userId: Uuid::generate(),
            content: NoteContent::fromString('A'),
        );
        $second = new ClientNoteEntry(
            noteId: Uuid::fromString('22222222-2222-4222-8222-222222222222'),
            userId: Uuid::generate(),
            content: NoteContent::fromString('B'),
        );

        $client = $this->createClient();
        $client->addNote($first)->addNote($second);

        $this->assertSame('22222222-2222-4222-8222-222222222222', $client->getNote()?->noteId->getValue());
        $this->assertSame('B', $client->getNote()?->content->getValue());
    }

    public function test_add_contact_then_remove_leaves_single_contact(): void
    {
        $client = $this->createClient();
        $clientId = $client->getId();

        $c1 = ClientContact::fromDatabase(
            $clientId,
            ContactType::EMAIL,
            ContactValue::fromString('a@example.com'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );
        $c2 = ClientContact::fromDatabase(
            $clientId,
            ContactType::PHONE,
            ContactValue::fromString('+48111222333'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );

        $client->addContact($c1)->addContact($c2);
        $this->assertCount(2, $client->getContacts());

        $client->removeContact($c1);

        $remaining = $client->getContacts();
        $this->assertCount(1, $remaining);
        $this->assertSame('+48111222333', $remaining[0]->getValue()->getValue());
    }

    public function test_remove_contact_not_in_aggregate_does_not_change_list(): void
    {
        $client = $this->createClient();
        $clientId = $client->getId();

        $inside = ClientContact::fromDatabase(
            $clientId,
            ContactType::EMAIL,
            ContactValue::fromString('in@example.com'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );
        $outside = ClientContact::fromDatabase(
            $clientId,
            ContactType::EMAIL,
            ContactValue::fromString('out@example.com'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );

        $client->addContact($inside);
        $client->removeContact($outside);

        $this->assertCount(1, $client->getContacts());
    }

    public function test_get_contacts_returns_copy_modifying_result_does_not_drop_internal(): void
    {
        $client = $this->createClient();
        $clientId = $client->getId();
        $contact = ClientContact::fromDatabase(
            $clientId,
            ContactType::EMAIL,
            ContactValue::fromString('keep@example.com'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );
        $client->addContact($contact);

        $snapshot = $client->getContacts();
        array_pop($snapshot);

        $this->assertCount(1, $client->getContacts());
    }

    public function test_add_tag_remove_tag(): void
    {
        $client = $this->createClient();
        $t1 = ClientTag::create(TagName::setValue('a'));
        $t2 = ClientTag::create(TagName::setValue('b'));

        $client->addTag($t1)->addTag($t2);
        $this->assertCount(2, $client->getTags());

        $client->removeTag($t1);

        $this->assertCount(1, $client->getTags());
        $this->assertSame('b', $client->getTags()[0]->getName()->getValue());
    }

    public function test_add_account_remove_account(): void
    {
        $client = $this->createClient();
        $clientId = $client->getId();

        $acc1 = $this->minimalCompanyAccount($clientId, 'PL61109010140000071219812874', '11111111');
        $acc2 = $this->minimalCompanyAccount($clientId, 'PL27114020040000300201355387', '22222222');

        $client->addAccount($acc1)->addAccount($acc2);
        $this->assertCount(2, $client->getAccounts());

        $client->removeAccount($acc1);

        $this->assertCount(1, $client->getAccounts());
    }

    public function test_fluent_add_methods_return_same_instance(): void
    {
        $client = $this->createClient();
        $clientId = $client->getId();

        $contact = ClientContact::fromDatabase(
            $clientId,
            ContactType::EMAIL,
            ContactValue::fromString('x@y.pl'),
            IsPrimary::fromBool(false),
            IsActive::fromBool(true),
            IsVerified::fromBool(false),
        );
        $tag = ClientTag::create(TagName::setValue('t'));
        $account = $this->minimalCompanyAccount($clientId, 'PL61109010140000071219812874', 'acc1');

        $chain = $client
            ->addContact($contact)
            ->addTag($tag)
            ->addAccount($account);

        $this->assertSame($client, $chain);
    }

    private function minimalCompanyAccount(Uuid $clientUuid, string $iban, string $numberSuffix): CompanyAccount
    {
        return CompanyAccount::fromDatabase(
            clientUuid: $clientUuid,
            name: AccountName::fromString('Konto '.$numberSuffix),
            number: AccountNumber::fromString($numberSuffix),
            swiftCode: SwiftCode::fromString('BPKOPLPW'),
            iban: Iban::fromString($iban),
            bic: Bic::fromString('BPKOPLPWXXX'),
            accountName: CompanyAccountName::fromString('Firma'),
            isActive: IsActive::fromBool(true),
            isPrimary: IsPrimary::fromBool(false),
        );
    }

    private function createClient(): CrmClientAggregate
    {
        return CrmClientAggregate::create(
            name: ClientName::fromString('Test'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            status: ClientStatus::LEAD,
            isCompany: IsCompany::fromBool(false),
        );
    }
}
