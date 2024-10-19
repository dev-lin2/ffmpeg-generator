<?php

namespace App\Filament\Resources\BirthDayUserResource\Pages;

use App\Filament\Resources\BirthDayUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBirthDayUser extends CreateRecord
{
    protected static string $resource = BirthDayUserResource::class;
}
