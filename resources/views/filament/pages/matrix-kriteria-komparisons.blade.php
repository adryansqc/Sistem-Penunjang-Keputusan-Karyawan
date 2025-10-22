<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/matrix-table.css') }}">

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-y-1 p-4 sm:px-6 sm:py-5">
            <h2 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Matriks Perbandingan Kriteria
            </h2>
            {{-- <button type="button"
                    wire:click="updateMatrixData"
                    wire:loading.attr="disabled"
                    wire:target="updateMatrixData"
                    class="fi-btn fi-btn-size-md fi-btn-variant-secondary fi-btn-color-gray"
                    style="margin-top: 10px;">
                Perbarui Data Matriks
            </button> --}}
        </div>

        <div class="fi-section-content-ctn border-t border-gray-200 p-4 dark:border-white/10 sm:px-6 sm:py-5">
            <div class="fi-section-content">
                <div class="comparison-guide-container">
                    <p class="comparison-guide-title">Panduan Skala Perbandingan:</p>
                    <div class="comparison-guide-list">
                        <span><span class="comparison-guide-item-value">1</span> - Sama penting</span>
                        <span><span class="comparison-guide-item-value">3</span> - Sedikit lebih penting</span>
                        <span><span class="comparison-guide-item-value">5</span> - Lebih penting</span>
                        <span><span class="comparison-guide-item-value">7</span> - Sangat lebih penting</span>
                        <span><span class="comparison-guide-item-value">9</span> - Mutlak lebih penting</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="matrix-table">
                        <thead class="matrix-table-header-row">
                            <tr class="matrix-table-row">
                                <th class="matrix-table-header-cell">
                                    <span class="matrix-table-header-text">Kriteria</span>
                                </th>
                                @foreach ($this->kriteriaList as $kriteria)
                                    <th class="matrix-table-header-cell">
                                        <span class="matrix-table-header-text">{{ $kriteria['nama'] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="matrix-table-body">
                            @foreach ($this->kriteriaList as $rowKriteria)
                                <tr class="matrix-table-row">
                                    <td class="matrix-table-cell">
                                        <div class="matrix-table-text-column">
                                            {{ $rowKriteria['nama'] }}
                                        </div>
                                    </td>
                                    @foreach ($this->kriteriaList as $colKriteria)
                                        <td class="matrix-table-cell">
                                            <div class="matrix-table-text-column">
                                                @if ($rowKriteria['id'] === $colKriteria['id'])
                                                    {{ number_format($this->comparisonMatrix[$rowKriteria['id']][$colKriteria['id']], 2) }}
                                                @else
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0.01"
                                                        class="editable-comparison-input" {{-- Changed to custom CSS class --}}
                                                        value="{{ number_format($this->comparisonMatrix[$rowKriteria['id']][$colKriteria['id']], 2) }}"
                                                        wire:change="updateComparisonValue({{ $rowKriteria['id'] }}, {{ $colKriteria['id'] }}, $event.target.value)"
                                                        wire:loading.attr="disabled"
                                                        wire:target="updateComparisonValue"
                                                    />
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="" style="margin-top: 20px;">
                    <button type="button"
                            wire:click="calculateWeights"
                            wire:loading.attr="disabled"
                            wire:target="calculateWeights"
                            class="calculate-weights-button">
                        Hitung Bobot Kriteria
                    </button>
                </div>
                <div class="weights-section">
                    <div class="weights-section-header">
                        <h2 class="weights-section-heading">
                            Hasil Bobot Kriteria
                        </h2>
                    </div>
                    <div class="weights-section-content-container">
                        <div class="weights-section-content">
                            <div class="overflow-x-auto">
                                <table class="matrix-table">
                                    <thead class="matrix-table-header-row">
                                        <tr class="matrix-table-row">
                                            <th class="matrix-table-header-cell">
                                                <span class="matrix-table-header-text">Kriteria</span>
                                            </th>
                                            <th class="matrix-table-header-cell">
                                                <span class="matrix-table-header-text">Bobot</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="matrix-table-body">
                                        @foreach ($this->kriteriaList as $kriteria)
                                            <tr class="matrix-table-row">
                                                <td class="matrix-table-cell">
                                                    <div class="matrix-table-text-column">
                                                        {{ $kriteria['nama'] }}
                                                    </div>
                                                </td>
                                                <td class="matrix-table-cell">
                                                    <div class="matrix-table-text-column">
                                                        {{ number_format($this->weights[$kriteria['id']] ?? 0, 4) }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>