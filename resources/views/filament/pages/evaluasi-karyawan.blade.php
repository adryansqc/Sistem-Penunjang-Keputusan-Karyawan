<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/evaluasi-karyawan.css') }}">

    <div class="evaluasi-container"> {{-- Replaced space-y-6 --}}
        <div class="evaluasi-card"> {{-- Replaced p-4 bg-white rounded-xl shadow dark:bg-gray-800 --}}
            <div class="evaluasi-header-section"> {{-- Replaced mb-4 --}}
                <h2 class="evaluasi-header-title"> {{-- Replaced text-lg font-medium text-gray-900 dark:text-gray-100 --}}
                    Evaluasi Karyawan
                </h2>
                <p class="evaluasi-header-description"> {{-- Replaced mt-1 text-sm text-gray-600 dark:text-gray-400 --}}
                    Berikan penilaian untuk setiap kriteria (0.0 - 1.0)
                </p>
            </div>

            <div class="evaluasi-save-button-wrapper"> {{-- Replaced mb-4 --}}
                <button type="button" wire:click="simpanEvaluasi" class="evaluasi-save-button"> {{-- Replaced Tailwind classes --}}
                    Simpan Evaluasi
                </button>
            </div>

            <div class="evaluasi-table-container"> {{-- Replaced overflow-x-auto --}}
                <table class="evaluasi-table"> {{-- Replaced min-w-full divide-y divide-gray-200 dark:divide-gray-700 --}}
                    <thead>
                        <tr class="evaluasi-table-head"> {{-- Replaced bg-gray-50 dark:bg-gray-700 --}}
                            <th> {{-- px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 --}}
                                Nama Karyawan
                            </th>
                            <th> {{-- px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 --}}
                                Posisi
                            </th>
                            @foreach($kriteriaList as $kriteria)
                                <th> {{-- px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 --}}
                                    {{ $kriteria['nama'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="evaluasi-table-body"> {{-- Replaced bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 --}}
                        @foreach($karyawanList as $karyawan)
                            <tr>
                                <td> {{-- px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 --}}
                                    {{ $karyawan['nama'] }}
                                </td>
                                <td> {{-- px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 --}}
                                    {{ $karyawan['posisi'] }}
                                </td>
                                @foreach($kriteriaList as $kriteria)
                                    <td> {{-- px-6 py-4 whitespace-nowrap --}}
                                        <input
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            max="1"
                                            class="evaluasi-input" {{-- Replaced Tailwind classes --}}
                                            wire:model.lazy="evaluationValues.{{ $karyawan['id'] }}.{{ $kriteria['id'] }}"
                                            wire:change="updateNilai({{ $karyawan['id'] }}, {{ $kriteria['id'] }}, $event.target.value)"
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="weights-display-section"> {{-- Replaced mt-6 p-4 bg-gray-50 rounded-lg dark:bg-gray-700 --}}
                <h3 class="weights-display-title"> {{-- Replaced text-sm font-medium text-gray-900 dark:text-gray-100 --}}
                    Bobot Kriteria Saat Ini:
                </h3>
                <div class="weights-list"> {{-- Replaced mt-2 flex flex-wrap gap-2 --}}
                    @foreach($kriteriaList as $kriteria)
                        <div class="weight-item"> {{-- Replaced px-3 py-1 bg-white rounded-full shadow-sm dark:bg-gray-800 --}}
                            <span class="weight-item-name">{{ $kriteria['nama'] }}:</span> {{-- Replaced text-sm text-gray-600 dark:text-gray-400 --}}
                            <span class="weight-item-value">{{ number_format($kriteria['bobot'] * 100, 2) }}%</span> {{-- Replaced ml-1 text-sm font-medium text-gray-900 dark:text-gray-100 --}}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>