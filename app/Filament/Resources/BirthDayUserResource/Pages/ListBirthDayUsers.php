<?php

namespace App\Filament\Resources\BirthDayUserResource\Pages;

use App\Filament\Resources\BirthDayUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBirthDayUsers extends ListRecords
{
    protected static string $resource = BirthDayUserResource::class;

    protected static ?string $label = '作成';

    protected static ?string $title = '誕生日ユーザー一覧';

    protected static ?string $breadcrumb = '誕生日ユーザー一覧';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('作成'),
        ];
    }
}
