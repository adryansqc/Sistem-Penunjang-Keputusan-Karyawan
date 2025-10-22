<?php

namespace App\Filament\Resources\Kriterias\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KriteriaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kriteria') // Section for basic criteria information
                    ->schema([
                        TextEntry::make('kode'),
                        TextEntry::make('nama'),
                    ]),

                Section::make('Deskripsi Kriteria') // Section for description
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
