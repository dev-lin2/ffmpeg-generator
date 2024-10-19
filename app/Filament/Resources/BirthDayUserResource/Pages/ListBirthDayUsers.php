<?php

namespace App\Filament\Resources\BirthDayUserResource\Pages;

use App\Filament\Resources\BirthDayUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBirthDayUsers extends ListRecords
{
    protected static string $resource = BirthDayUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
