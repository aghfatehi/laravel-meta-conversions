<?php

use Aghfatehi\LaravelMetaConversions\DTOs\UserData;

it('can set and get email', function () {
    $userData = new UserData;
    $result = $userData->email('user@example.com');

    expect($result)->toBeInstanceOf(UserData::class);

    $data = $userData->toArray();

    expect($data['em'])->toBe([hash('sha256', 'user@example.com')]);
});

it('hashes email with sha256', function () {
    $userData = new UserData;
    $userData->email('Test@Example.COM');

    $data = $userData->toArray();

    expect($data['em'][0])->toBe(hash('sha256', 'test@example.com'));
});

it('hashes phone after stripping non-numeric characters', function () {
    $userData = new UserData;
    $userData->phone('+966 55 123 4567');

    $data = $userData->toArray();

    expect($data['ph'][0])->toBe(hash('sha256', '966551234567'));
});

it('hashes first name with sha256 lowercase', function () {
    $userData = new UserData;
    $userData->firstName('AHMED');

    $data = $userData->toArray();

    expect($data['fn'][0])->toBe(hash('sha256', 'ahmed'));
});

it('hashes last name with sha256 lowercase', function () {
    $userData = new UserData;
    $userData->lastName('GHFATEHI');

    $data = $userData->toArray();

    expect($data['ln'][0])->toBe(hash('sha256', 'ghfatehi'));
});

it('includes client ip and user agent by default', function () {
    $userData = new UserData;

    $data = $userData->toArray();

    expect($data)->toHaveKey('client_ip_address');
    expect($data)->toHaveKey('client_user_agent');
});

it('can set fbp and fbc', function () {
    $userData = new UserData;
    $userData->fbp('fb.1.1234567890.1234567890');
    $userData->fbc('fb.1.1234567890.1234567890');

    $data = $userData->toArray();

    expect($data['fbp'])->toBe('fb.1.1234567890.1234567890');
    expect($data['fbc'])->toBe('fb.1.1234567890.1234567890');
});

it('can set city state zip country', function () {
    $userData = new UserData;
    $userData->city('Riyadh');
    $userData->state('Riyadh Province');
    $userData->zip('12345');
    $userData->country('SA');

    $data = $userData->toArray();

    expect($data['ct'][0])->toBe(hash('sha256', 'riyadh'));
    expect($data['st'][0])->toBe(hash('sha256', 'riyadh province'));
    expect($data['zp'][0])->toBe(hash('sha256', '12345'));
    expect($data['country'][0])->toBe(hash('sha256', 'sa'));
});

it('hashes external id', function () {
    $userData = new UserData;
    $userData->externalId('user_123');

    $data = $userData->toArray();

    expect($data['external_id'][0])->toBe(hash('sha256', 'user_123'));
});

it('returns empty array when no fields set', function () {
    $userData = new UserData;

    $data = $userData->toArray();

    expect($data)->toHaveKey('client_ip_address');
    expect($data)->toHaveKey('client_user_agent');
    expect($data)->not->toHaveKey('em');
    expect($data)->not->toHaveKey('ph');
});
