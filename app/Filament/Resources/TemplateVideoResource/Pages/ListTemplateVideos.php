<?php

namespace App\Filament\Resources\TemplateVideoResource\Pages;

use App\Filament\Resources\TemplateVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateVideos extends ListRecords
{
    protected static string $resource = TemplateVideoResource::class;
    protected static ?string $label = "新規動画テンプレート作成";
    protected static ?string $breadcrumb = '動画テンプレート一覧';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('新規動画テンプレート作成'),
        ];
    }
}
