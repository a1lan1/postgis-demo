<?php

test('smoke', function () {
    $routes = [
        '/',
    ];

    visit($routes)
        ->assertNoSmoke()
        ->assertScreenshotMatches();
});
