@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Central Library Portal</h1>
            <p class="text-sm text-slate-500">UVAS Swat Veterinary & Animal Sciences Reference Library Catalog.</p>
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('services.library') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Book Title, Author, ISBN..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-md w-full sm:w-auto">Search Catalog</button>
        </form>
    </div>

    <!-- Books Directory -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Library Book Catalog</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ $books->total() }} Books</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">ISBN</th>
                        <th class="px-6 py-3.5">Book Title</th>
                        <th class="px-6 py-3.5">Author</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5 text-center">Copies (Available/Total)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($books as $b)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-700">{{ $b->isbn }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $b->title }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $b->author }}</td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-600">{{ $b->category }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs">
                                {{ $b->available_copies }} / {{ $b->total_copies }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No books found matching search query.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
