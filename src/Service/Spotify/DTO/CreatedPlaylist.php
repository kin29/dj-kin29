<?php
declare(strict_types=1);

namespace App\Service\Spotify\DTO;

class CreatedPlaylist
{
    public function __construct(
        public string $name,
        public string $url,
        public string $imageUrl,
    ) {
    }
}
