<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\DTOs;

use Illuminate\Support\Facades\Request;

class UserData
{
    private ?string $email = null;

    private ?string $phone = null;

    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $city = null;

    private ?string $state = null;

    private ?string $zip = null;

    private ?string $country = null;

    private ?string $externalId = null;

    private ?string $clientIpAddress = null;

    private ?string $clientUserAgent = null;

    private ?string $fbp = null;

    private ?string $fbc = null;

    public function email(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function phone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function firstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function lastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function city(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function state(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function zip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function country(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function externalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function clientIpAddress(?string $clientIpAddress): self
    {
        $this->clientIpAddress = $clientIpAddress;

        return $this;
    }

    public function clientUserAgent(?string $clientUserAgent): self
    {
        $this->clientUserAgent = $clientUserAgent;

        return $this;
    }

    public function fbp(?string $fbp): self
    {
        $this->fbp = $fbp;

        return $this;
    }

    public function fbc(?string $fbc): self
    {
        $this->fbc = $fbc;

        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->email !== null) {
            $data['em'] = [hash('sha256', strtolower(trim($this->email)))];
        }

        if ($this->phone !== null) {
            $data['ph'] = [hash('sha256', preg_replace('/[^0-9]/', '', $this->phone))];
        }

        if ($this->firstName !== null) {
            $data['fn'] = [hash('sha256', strtolower(trim($this->firstName)))];
        }

        if ($this->lastName !== null) {
            $data['ln'] = [hash('sha256', strtolower(trim($this->lastName)))];
        }

        if ($this->city !== null) {
            $data['ct'] = [hash('sha256', strtolower(trim($this->city)))];
        }

        if ($this->state !== null) {
            $data['st'] = [hash('sha256', strtolower(trim($this->state)))];
        }

        if ($this->zip !== null) {
            $data['zp'] = [hash('sha256', strtolower(trim($this->zip)))];
        }

        if ($this->country !== null) {
            $data['country'] = [hash('sha256', strtolower(trim($this->country)))];
        }

        if ($this->externalId !== null) {
            $data['external_id'] = [hash('sha256', $this->externalId)];
        }

        $data['client_ip_address'] = $this->clientIpAddress ?? Request::ip();
        $data['client_user_agent'] = $this->clientUserAgent ?? Request::userAgent();

        if ($this->fbp !== null) {
            $data['fbp'] = $this->fbp;
        }

        if ($this->fbc !== null) {
            $data['fbc'] = $this->fbc;
        }

        return $data;
    }
}
