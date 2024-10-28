<?php

namespace App\Filament\Resources\WishTextResource\Pages;

use App\Filament\Resources\WishTextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWishText extends EditRecord
{
    protected static string $resource = WishTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
