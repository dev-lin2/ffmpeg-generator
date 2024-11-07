<?php

namespace App\Filament\Resources\WishTextResource\Pages;

use App\Filament\Resources\WishTextResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWishTexts extends ListRecords
{
    protected static string $resource = WishTextResource::class;

    public function mount(): void
    {
        $this->mountAndRedirect();
        parent::mount();
    }

    // This functoin is used to redirect to the edit page if there is only one record (WishText will be created only once)
    protected function mountAndRedirect(): void
    {
        $wishTexts = WishTextResource::getModel()::all();

        if ($wishTexts->count() === 1) {
            $this->redirect(WishTextResource::getUrl('edit', ['record' => $wishTexts->first()->id]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
