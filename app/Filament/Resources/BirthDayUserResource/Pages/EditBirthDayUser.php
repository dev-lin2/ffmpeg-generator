<?php

namespace App\Filament\Resources\BirthDayUserResource\Pages;

use App\Filament\Resources\BirthDayUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBirthDayUser extends EditRecord
{
    protected static string $resource = BirthDayUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
