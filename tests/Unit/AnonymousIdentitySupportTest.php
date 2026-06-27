<?php

use App\Models\User;
use App\Support\AnonymousIdentitySupport;

test('anonymous aliases use varied animal names without anonymous suffix or numbers', function () {
    $aliases = [];

    for ($id = 1; $id <= 1000; $id++) {
        $user = new User();
        $user->id = $id;

        $aliases[] = AnonymousIdentitySupport::buildUserAlias($user);
    }

    expect(array_unique($aliases))->toHaveCount(1000);

    foreach ($aliases as $alias) {
        expect($alias)
            ->not->toContain('Anonim')
            ->not->toMatch('/\d/');
    }
});

test('anonymous animal pool is large enough for long term variation', function () {
    expect(count(AnonymousIdentitySupport::namePool()))->toBeGreaterThanOrEqual(1000);
});

test('anonymous fallback picks an unused animal name without numeric suffix', function () {
    $usedAliases = array_slice(AnonymousIdentitySupport::namePool(), 0, 50);
    $alias = AnonymousIdentitySupport::fallbackAlias('room:member', $usedAliases);

    expect($usedAliases)->not->toContain($alias);
    expect($alias)
        ->not->toContain('Anonim')
        ->not->toMatch('/\d/');
});
