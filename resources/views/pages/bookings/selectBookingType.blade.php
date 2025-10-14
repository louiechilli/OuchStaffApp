{{-- resources/views/pages/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10 flex flex-col gap-6">

            @include('components.pageHeader', [
                'title' => 'Choose a booking type',
                'subtitle' => 'What type of booking would you like to create?',
                'action' => 'Back to Dashboard', 
                'actionUrl' => route('dashboard')
            ])

            <hr class="border-slate-200">

            @foreach($serviceCategories as $category)
                @php
                    $items = $services->where('category_id', $category->id);
                @endphp

                @if($items->count())
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">{{ $category->name }}</h2>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($items as $svc)
                                <a href="{{ route('bookings.create', ['service_id' => $svc->id]) }}"
                                   class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base md:text-lg font-semibold text-slate-800">
                                                    {{ $svc->name }}
                                                </h3>
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-slate-400 mt-1"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
@endsection
