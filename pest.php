<?php

declare(strict_types=1);

use Pest\Testing\TestCase;

require_once __DIR__.'/vendor/autoload.php';

TestCase::defaultTestSuite('unit')
    ->directories(['tests'])
    ->bootstrap(__DIR__.'/vendor/autoload.php');
