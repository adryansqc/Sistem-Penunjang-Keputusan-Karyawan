<?php

namespace App\Filament\Resources\Kriterias\Pages;

use App\Filament\Resources\Kriterias\KriteriaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKriteria extends EditRecord
{
    protected static string $resource = KriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
