<?php

namespace App\Filament\Admin\Resources\JokiPackageResource\Pages;

use App\Filament\Admin\Resources\JokiPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJokiPackages extends ListRecords
{
    protected static string $resource = JokiPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
