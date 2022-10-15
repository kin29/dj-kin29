<?php

declare(strict_types=1);

namespace App\Service\Spotify\DTO;

class Artist
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
