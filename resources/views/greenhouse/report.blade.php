@extends('layouts.app')
@section('title', 'Laporan ' . $greenhouse->name)

@section('content')
@php
    $backRoute = auth()->user()->isAdmin()
        ? route('admin.greenhouses.show', $greenhouse)
        : route('greenhouse.show', $greenhouse);

    $gradeCards = [
        ['label' => 'Grade A', 'value' => $summary['grade_a'], 'class' => 'text-emerald-700 bg-emerald-50'],
        ['label' => 'Grade B', 'value' => $summary['grade_b'], 'class' => 'text-sky-700 bg-sky-50'],
        ['label' => 'Grade C', 'value' => $summary['grade_c'], 'class' => 'text-amber-700 bg-amber-50'],
        ['label' => 'Grade D', 'value' => $summary['grade_d'], 'class' => 'text-stone-700 bg-stone-100'],
    ];

    // Map weight by grade for easy access
    $weightMap = collect($weightByGrade)->keyBy('grade');
@endphp

<div class="max-w-6xl mx-auto px-4 pt-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-stone-200 shadow-sm text-sm font-semibold text-stone-600">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-stone-800 truncate">Laporan {{ $greenhouse->name }}</h1>
            <p class="text-xs text-stone-400 font-mono">{{ $greenhouse->code }}</p>
        </div>
    </div>

    <!-- Filter Tanggal -->
    <!-- <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-stone-500 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from ?? '' }}"
                    class="text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to ?? '' }}"
                    class="text-sm border border-stone-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400 outline-none">
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold transition-colors">
                Filter
            </button>
            @if($from || $to)
                <a href="{{ request()->url() }}"
                    class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-600 text-sm font-semibold transition-colors">
                    Reset
                </a>
            @endif
            @if($from || $to)
                <span class="text-xs text-stone-400 self-center ml-1">
                    Menampilkan data
                    @if($from && $to)dari {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    @elseif($from)sampai {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }}
                    @else{{ $to ? 'hingga ' . \Carbon\Carbon::parse($to)->translatedFormat('d M Y') : '' }}
                    @endif
                </span>
            @endif
        </form>
    </div> -->

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-stone-50 text-stone-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V11m0 0c-2.76 0-5-2.24-5-5V4h2c2.76 0 5 2.24 5 5v2zm0 0c2.76 0 5-2.24 5-5V4h-2c-2.76 0-5 2.24-5 5v2zM5 22h14"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-stone-800 leading-tight">{{ $summary['total_trees'] }}</div>
                <div class="text-xs text-stone-500 mt-1">Total Pohon</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-emerald-700 leading-tight">{{ $summary['alive_trees'] }}</div>
                <div class="text-xs text-stone-500 mt-1">Hidup ({{ $summary['alive_rate'] }}%)</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-red-600 leading-tight">{{ $summary['dead_trees'] }}</div>
                <div class="text-xs text-stone-500 mt-1">Mati ({{ $summary['dead_rate'] }}%)</div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-2xl font-bold text-sky-700 leading-tight">{{ $summary['total_fruits'] }}</div>
                <div class="text-xs text-stone-500 mt-1">Total Buah</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-stone-100 lg:col-span-2">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="font-bold text-stone-800">Kualitas Buah</h2>
                <span class="text-xs font-semibold text-stone-500">{{ $summary['total_fruits'] }} total buah dari pohon hidup</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="rounded-xl bg-emerald-50 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-bold text-emerald-700 leading-tight">{{ $summary['good_fruits'] }}</div>
                        <div class="text-xs text-emerald-700 mt-1">Bagus ({{ $summary['good_rate'] }}%)</div>
                    </div>
                </div>
                <div class="rounded-xl bg-red-50 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-bold text-red-600 leading-tight">{{ $summary['rotten_fruits'] }}</div>
                        <div class="text-xs text-red-600 mt-1">Busuk ({{ $summary['rotten_rate'] }}%)</div>
                    </div>
                </div>
                <div class="rounded-xl bg-stone-50 p-4 col-span-2 md:col-span-1 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white text-stone-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.366 2.445a1 1 0 00-.364 1.118l1.286 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.445a1 1 0 00-1.176 0l-3.366 2.445c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L4.063 9.384c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-bold text-stone-800 leading-tight truncate">{{ $topVariety?->variety_name ?? '-' }}</div>
                        <div class="text-xs text-stone-500 mt-1">Varietas paling produktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-stone-100">
            <h2 class="font-bold text-stone-800 mb-4">Total Grade</h2>
            <div class="grid grid-cols-2 gap-2">
                @foreach($gradeCards as $grade)
                    <div class="rounded-xl p-3 {{ $grade['class'] }} flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white/80 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.366 2.445a1 1 0 00-.364 1.118l1.286 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.445a1 1 0 00-1.176 0l-3.366 2.445c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L4.063 9.384c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-bold leading-tight">{{ $grade['value'] }}</div>
                            <div class="text-xs mt-0.5">{{ $grade['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-100">
                <h2 class="font-bold text-stone-800">Pohon per Varietas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 text-xs text-stone-500">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Varietas</th>
                            <th class="text-right px-3 py-3 font-semibold">Total</th>
                            <th class="text-right px-3 py-3 font-semibold">Hidup</th>
                            <th class="text-right px-5 py-3 font-semibold">Mati</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($treesByVariety as $row)
                            <tr>
                                <td class="px-5 py-3 font-semibold text-stone-700">{{ $row->variety_name }}</td>
                                <td class="px-3 py-3 text-right text-stone-600">{{ $row->total_trees }}</td>
                                <td class="px-3 py-3 text-right text-emerald-700 font-semibold">{{ $row->alive_trees }}</td>
                                <td class="px-5 py-3 text-right text-red-600 font-semibold">{{ $row->dead_trees }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-stone-400">Belum ada data pohon.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-100">
                <h2 class="font-bold text-stone-800">Buah dan Grade per Varietas</h2>
                <p class="text-xs text-stone-400 mt-1">Buah dari pohon mati tidak masuk hitungan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 text-xs text-stone-500">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Varietas</th>
                            <th class="text-right px-3 py-3 font-semibold">Buah</th>
                            <th class="text-right px-3 py-3 font-semibold">A</th>
                            <th class="text-right px-3 py-3 font-semibold">B</th>
                            <th class="text-right px-3 py-3 font-semibold">C</th>
                            <th class="text-right px-3 py-3 font-semibold">D</th>
                            <th class="text-right px-5 py-3 font-semibold">Busuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($fruitsByVariety as $row)
                            <tr>
                                <td class="px-5 py-3 font-semibold text-stone-700">{{ $row->variety_name }}</td>
                                <td class="px-3 py-3 text-right text-stone-700 font-semibold">{{ $row->total_fruits }}</td>
                                <td class="px-3 py-3 text-right text-emerald-700">{{ $row->grade_a }}</td>
                                <td class="px-3 py-3 text-right text-sky-700">{{ $row->grade_b }}</td>
                                <td class="px-3 py-3 text-right text-amber-700">{{ $row->grade_c }}</td>
                                <td class="px-3 py-3 text-right text-stone-600">{{ $row->grade_d }}</td>
                                <td class="px-5 py-3 text-right text-red-600 font-semibold">{{ $row->rotten_fruits }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-stone-400">Belum ada data buah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Berat per Grade -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-stone-100 mb-6">
        <h2 class="font-bold text-stone-800 mb-4">Berat Buah per Grade</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach(['A', 'B', 'C', 'D'] as $g)
                @php
                    $data = $weightMap[$g] ?? null;
                @endphp
                <div class="rounded-xl border border-stone-200 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $g === 'A' ? 'bg-emerald-100 text-emerald-700' : ($g === 'B' ? 'bg-sky-100 text-sky-700' : ($g === 'C' ? 'bg-amber-100 text-amber-700' : 'bg-stone-100 text-stone-600')) }}">
                        <span class="text-sm font-bold">{{ $g }}</span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-stone-800 leading-tight">{{ $data ? round($data->total_weight, 1) : 0 }} kg</div>
                        <div class="text-xs text-stone-500">{{ $data ? $data->count : 0 }} buah · ø {{ $data ? round($data->avg_weight, 2) : 0 }} kg</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Berat per Varietas dan Grade -->
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-stone-100">
            <h2 class="font-bold text-stone-800">Berat Buah per Varietas & Grade</h2>
        </div>
        <div class="divide-y divide-stone-100">
            @php
                $groupedWeight = collect($weightByVarietyGrade)->groupBy('variety_name');
            @endphp
            @forelse($groupedWeight as $varietyName => $rows)
                @php
                    $subTotalCount = $rows->sum('fruit_count');
                    $subTotalWeight = $rows->sum('total_weight');
                    $subTotalAvg = $subTotalCount > 0 ? $subTotalWeight / $subTotalCount : 0;
                @endphp
                <div>
                    <div class="px-5 py-2 bg-stone-50">
                        <h3 class="text-sm font-bold text-stone-700">{{ $varietyName }}</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-white text-xs text-stone-500">
                            <tr>
                                <th class="text-right px-5 py-2 font-semibold">Grade</th>
                                <th class="text-right px-3 py-2 font-semibold">Jumlah</th>
                                <th class="text-right px-3 py-2 font-semibold">Total Berat</th>
                                <th class="text-right px-5 py-2 font-semibold">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                            <tr class="border-t border-stone-50">
                                <td class="px-5 py-2 text-right">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-bold {{ $row->grade === 'A' ? 'bg-emerald-100 text-emerald-700' : ($row->grade === 'B' ? 'bg-sky-100 text-sky-700' : ($row->grade === 'C' ? 'bg-amber-100 text-amber-700' : 'bg-stone-100 text-stone-600')) }}">
                                        {{ $row->grade }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right text-stone-700">{{ $row->fruit_count }}</td>
                                <td class="px-3 py-2 text-right text-stone-700 font-semibold">{{ round($row->total_weight, 1) }} kg</td>
                                <td class="px-5 py-2 text-right text-stone-500">{{ round($row->avg_weight, 2) }} kg</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                                <td class="px-5 py-2 text-right">
                                    <span class="text-xs font-bold text-emerald-700">SUBTOTAL</span>
                                </td>
                                <td class="px-3 py-2 text-right font-bold text-emerald-700">{{ $subTotalCount }}</td>
                                <td class="px-3 py-2 text-right font-bold text-emerald-700">{{ round($subTotalWeight, 1) }} kg</td>
                                <td class="px-5 py-2 text-right text-emerald-600">{{ round($subTotalAvg, 2) }} kg</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-stone-400">Belum ada data berat.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-stone-100">
        <h2 class="font-bold text-stone-800 mb-4">Catatan Operasional</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-xl border border-stone-100 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-stone-700">Survival Rate</div>
                    <div class="text-2xl font-bold text-emerald-700 leading-tight">{{ $summary['alive_rate'] }}%</div>
                    <p class="text-xs text-stone-500 mt-1">Persentase pohon yang masih hidup.</p>
                </div>
            </div>
            <div class="rounded-xl border border-stone-100 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-stone-700">Rotten Rate</div>
                    <div class="text-2xl font-bold text-red-600 leading-tight">{{ $summary['rotten_rate'] }}%</div>
                    <p class="text-xs text-stone-500 mt-1">Proporsi buah busuk dari seluruh buah tercatat.</p>
                </div>
            </div>
            <div class="rounded-xl border border-stone-100 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.366 2.445a1 1 0 00-.364 1.118l1.286 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.445a1 1 0 00-1.176 0l-3.366 2.445c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L4.063 9.384c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-stone-700">Premium Yield</div>
                    <div class="text-2xl font-bold text-emerald-700 leading-tight">{{ $summary['total_fruits'] > 0 ? round(($summary['grade_a'] / $summary['total_fruits']) * 100, 1) : 0 }}%</div>
                    <p class="text-xs text-stone-500 mt-1">Persentase grade A dari total buah.</p>
                </div>
            </div>
            <div class="rounded-xl border border-stone-100 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-stone-700">Rata-rata Berat</div>
                    <div class="text-2xl font-bold text-violet-700 leading-tight">{{ $summary['avg_weight'] }} kg</div>
                    <p class="text-xs text-stone-500 mt-1">Rata-rata berat per buah.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
