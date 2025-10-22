<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class KaryawanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Karyawan')
                    ->schema([
                        TextEntry::make('nama'),
                        TextEntry::make('posisi')
                            ->placeholder('-'),
                        TextEntry::make('masa_kontrak_bulan')
                            ->placeholder('-'),
                    ]),

                Section::make('Detail Pekerjaan')
                    ->schema([
                        TextEntry::make('tanggal_masuk')
                            ->date()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
