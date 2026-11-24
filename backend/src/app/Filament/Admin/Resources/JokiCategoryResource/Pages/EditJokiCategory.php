<?php

namespace App\Filament\Admin\Resources\JokiCategoryResource\Pages;

use App\Filament\Admin\Resources\JokiCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJokiCategory extends EditRecord
{
    protected static string $resource = JokiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
