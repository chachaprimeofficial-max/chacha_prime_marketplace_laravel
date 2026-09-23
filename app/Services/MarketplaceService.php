<?php

namespace App\Services;

class MarketplaceService
{
    public function platformName(): string
    {
        return config('app.name', 'Chacha Prime');
    }
}
