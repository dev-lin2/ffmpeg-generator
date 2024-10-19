<?php

namespace App\Filament\Resources\TemplateVideoResource\Pages;

use App\Filament\Resources\TemplateVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateVideos extends ListRecords
{
    protected static string $resource = TemplateVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
