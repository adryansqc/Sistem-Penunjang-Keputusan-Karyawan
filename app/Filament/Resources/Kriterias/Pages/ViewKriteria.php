<?php

namespace App\Filament\Resources\Kriterias\Pages;

use App\Filament\Resources\Kriterias\KriteriaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKriteria extends ViewRecord
{
    protected static string $resource = KriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
