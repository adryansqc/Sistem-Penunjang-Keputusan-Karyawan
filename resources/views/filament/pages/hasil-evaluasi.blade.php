<x-filament-panels::page>
    {{-- Link to your custom CSS file --}}
    <link rel="stylesheet" href="{{ asset('css/hasil-evaluasi.css') }}">

    <div class="hasil-evaluasi-container"> {{-- Replaced space-y-6 --}}
        <div class="hasil-evaluasi-card"> {{-- Replaced p-4 bg-white rounded-xl shadow dark:bg-gray-800 --}}
            <div class="hasil-evaluasi-header-section"> {{-- Replaced mb-4 --}}
                <h2 class="hasil-evaluasi-header-title"> {{-- Replaced text-lg font-medium text-gray-900 dark:text-gray-100 --}}
                    Hasil Evaluasi Karyawan (AHP & TOPSIS)
                </h2>
                <p class="hasil-evaluasi-header-description"> {{-- Replaced mt-1 text-sm text-gray-600 dark:text-gray-400 --}}
                    Hitung dan lihat hasil akhir evaluasi karyawan berdasarkan metode AHP dan TOPSIS.
                </p>
            </div>

            <div class="hasil-evaluasi-button-wrapper"> {{-- Removed inline style as only one button now --}}
                <button type="button" wire:click="calculateAndSaveAllResults" class="hasil-evaluasi-button">
                    Hitung dan Simpan Semua Hasil Evaluasi
                </button>
            </div>

            <div class="hasil-evaluasi-table-container"> {{-- Replaced overflow-x-auto --}}
                <table class="hasil-evaluasi-table"> {{-- Replaced min-w-full divide-y divide-gray-200 dark:divide-gray-700 --}}
                    <thead>
                        <tr class="hasil-evaluasi-table-head"> {{-- Replaced bg-gray-50 dark:bg-gray-700 --}}
                            <th>Nama Karyawan</th>
                            <th>Peringkat AHP</th>
                            <th>Skor Total AHP</th>
                            <th>Keterangan AHP</th>
                            <th>Peringkat TOPSIS</th> {{-- New column for TOPSIS Rank --}}
                            <th>Skor TOPSIS</th>
                            <th>Keterangan TOPSIS</th>
                        </tr>
                    </thead>
                    <tbody class="hasil-evaluasi-table-body"> {{-- Replaced bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 --}}
                        @forelse($combinedResults as $result)
                            <tr>
                                <td class="data">
                                    {{ $result['karyawan']['nama'] ?? 'N/A' }}
                                </td>
                                <td class="rank">
                                    {{ $result['ahp']['peringkat'] ?? 'N/A' }}
                                </td>
                                <td class="data">
                                    {{ number_format($result['ahp']['skor_total'] ?? 0, 4) }}
                                </td>
                                <td class="data">
                                    {{ $result['ahp']['keterangan'] ?? 'Belum dihitung' }}
                                </td>
                                <td class="rank"> {{-- New column for TOPSIS Rank --}}
                                    {{ $result['topsis']['peringkat'] ?? 'N/A' }}
                                </td>
                                <td class="data"> {{-- New column for TOPSIS Score --}}
                                    {{ number_format($result['topsis']['skor_topsis'] ?? 0, 4) }}
                                </td>
                                <td class="data"> {{-- New column for TOPSIS Keterangan --}}
                                    {{ $result['topsis']['keterangan'] ?? 'Belum dihitung' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="hasil-evaluasi-table-empty"> {{-- Updated colspan to 7 --}}
                                    Belum ada hasil evaluasi. Klik tombol di atas untuk memulai perhitungan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>