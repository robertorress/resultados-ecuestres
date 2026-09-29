<?php

final class ConcursoDisplayData
{
    public function __construct(
        public readonly string $id,
        public readonly string $fecha,
        public readonly string $nombre,
        public readonly string $tipo,
        public readonly string $search,
        public readonly bool $isUpcoming,
        public readonly string $statusImage,
        public readonly string $statusAlt,
        public readonly string $actionIcon,
        public readonly ?string $typeImage,
        public readonly ?string $typeAlt
    ) {
    }
}
