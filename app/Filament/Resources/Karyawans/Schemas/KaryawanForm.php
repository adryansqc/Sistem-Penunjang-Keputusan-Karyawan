<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('posisi')
                    ->required()
                    ->default(null),
                TextInput::make('masa_kontak_bulan')
                    ->numeric()
                    ->required()
                    ->placeholder('Contoh : 12')
                    ->minValue(1)
                    ->maxValue(12)
                    ->helperText('Masukkan angka bulan')
                    ->default(null),
                DatePicker::make('tanggal_masuk')
                    ->required(),

            ]);
    }
}
