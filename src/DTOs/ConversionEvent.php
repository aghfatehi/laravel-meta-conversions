<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\DTOs;

class ConversionEvent
{
    public function __construct(
        private readonly string $eventName,
        private readonly ?float $value = null,
        private readonly ?string $currency = null,
        private readonly ?array $contentIds = null,
        private readonly ?string $contentType = null,
        private readonly ?string $contentCategory = null,
        private readonly ?string $contentName = null,
        private readonly ?int $quantity = null,
        private readonly ?string $searchString = null,
        private readonly ?string $status = null,
        private readonly ?string $eventSourceUrl = null,
        private readonly string $actionSource = 'website',
        private readonly ?UserData $userData = null,
        private readonly ?string $eventId = null,
        private readonly ?int $eventTime = null,
    ) {}

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getContentIds(): ?array
    {
        return $this->contentIds;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function getContentCategory(): ?string
    {
        return $this->contentCategory;
    }

    public function getContentName(): ?string
    {
        return $this->contentName;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getSearchString(): ?string
    {
        return $this->searchString;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getEventSourceUrl(): ?string
    {
        return $this->eventSourceUrl;
    }

    public function getActionSource(): string
    {
        return $this->actionSource;
    }

    public function getUserData(): ?UserData
    {
        return $this->userData;
    }

    public function getEventId(): ?string
    {
        return $this->eventId;
    }

    public function getEventTime(): ?int
    {
        return $this->eventTime;
    }

    public function toArray(): array
    {
        $data = [
            'event_name' => $this->eventName,
            'event_time' => $this->eventTime ?? time(),
            'event_id' => $this->eventId,
            'action_source' => $this->actionSource,
            'event_source_url' => $this->eventSourceUrl,
        ];

        if ($this->userData !== null) {
            $data['user_data'] = $this->userData->toArray();
        }

        $customData = [];

        if ($this->value !== null) {
            $customData['value'] = $this->value;
        }

        if ($this->currency !== null) {
            $customData['currency'] = $this->currency;
        }

        if ($this->contentIds !== null) {
            $customData['content_ids'] = $this->contentIds;
        }

        if ($this->contentType !== null) {
            $customData['content_type'] = $this->contentType;
        }

        if ($this->contentCategory !== null) {
            $customData['content_category'] = $this->contentCategory;
        }

        if ($this->contentName !== null) {
            $customData['content_name'] = $this->contentName;
        }

        if ($this->quantity !== null) {
            $customData['num_items'] = $this->quantity;
        }

        if ($this->searchString !== null) {
            $customData['search_string'] = $this->searchString;
        }

        if ($this->status !== null) {
            $customData['status'] = $this->status;
        }

        if ($customData !== []) {
            $data['custom_data'] = $customData;
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== []);
    }
}
