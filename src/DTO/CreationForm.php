<?php

declare(strict_types=1);

namespace App\DTO;

class CreationForm
{
    private array $artistNames = [];
    private string $playlistName = '';
    private bool $isPrivate = true;

    /**
     * @return array
     */
    public function getArtistNames(): array
    {
        return $this->artistNames;
    }

    /**
     * @param array $artistNames
     */
    public function setArtistNames(array $artistNames): void
    {
        $this->artistNames = $artistNames;
    }

    /**
     * @return string
     */
    public function getPlaylistName(): string
    {
        return $this->playlistName;
    }

    /**
     * @param string $playlistName
     */
    public function setPlaylistName(string $playlistName): void
    {
        $this->playlistName = $playlistName;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    /**
     * @param bool $isPrivate
     */
    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }
}
