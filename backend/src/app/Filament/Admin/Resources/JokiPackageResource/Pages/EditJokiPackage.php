<?php

namespace App\Filament\Admin\Resources\JokiPackageResource\Pages;

use App\Filament\Admin\Resources\JokiPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJokiPackage extends EditRecord
{
    protected static string $resource = JokiPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
