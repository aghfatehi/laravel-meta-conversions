<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Services;

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\DTOs\UserData;
use Aghfatehi\LaravelMetaConversions\Exceptions\InvalidEventDataException;
use Ramsey\Uuid\Uuid;

class EventBuilder
{
    private string $eventName;

    private ?float $value = null;

    private ?string $currency = null;

    private ?array $contentIds = null;

    private ?string $contentType = null;

    private ?string $contentCategory = null;

    private ?string $contentName = null;

    private ?int $quantity = null;

    private ?string $searchString = null;

    private ?string $status = null;

    private ?string $eventSourceUrl = null;

    private string $actionSource = 'website';

    private ?UserData $userData = null;

    private ?string $eventId = null;

    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function name(string $eventName): self
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function value(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function currency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function contentIds(?array $contentIds): self
    {
        $this->contentIds = $contentIds;

        return $this;
    }

    public function contentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function contentCategory(?string $contentCategory): self
    {
        $this->contentCategory = $contentCategory;

        return $this;
    }

    public function contentName(?string $contentName): self
    {
        $this->contentName = $contentName;

        return $this;
    }

    public function quantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function searchString(?string $searchString): self
    {
        $this->searchString = $searchString;

        return $this;
    }

    public function status(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function eventSourceUrl(?string $eventSourceUrl): self
    {
        $this->eventSourceUrl = $eventSourceUrl;

        return $this;
    }

    public function actionSource(string $actionSource): self
    {
        $this->actionSource = $actionSource;

        return $this;
    }

    public function userData(?UserData $userData): self
    {
        $this->userData = $userData;

        return $this;
    }

    public function eventId(?string $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function build(): ConversionEvent
    {
        if (empty($this->eventName)) {
            throw InvalidEventDataException::missingEventName();
        }

        if ($this->currency !== null && strlen($this->currency) !== 3) {
            throw InvalidEventDataException::invalidCurrency($this->currency);
        }

        $eventId = $this->eventId ?? Uuid::uuid4()->toString();

        return new ConversionEvent(
            eventName: $this->eventName,
            value: $this->value,
            currency: $this->currency,
            contentIds: $this->contentIds,
            contentType: $this->contentType,
            contentCategory: $this->contentCategory,
            contentName: $this->contentName,
            quantity: $this->quantity,
            searchString: $this->searchString,
            status: $this->status,
            eventSourceUrl: $this->eventSourceUrl ?? request()->url(),
            actionSource: $this->actionSource,
            userData: $this->userData,
            eventId: $eventId,
        );
    }

    public function send(): array
    {
        $event = $this->build();

        return app(FacebookConversionService::class)->sendEvent($event);
    }
}
