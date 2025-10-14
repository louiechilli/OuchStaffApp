@extends('layouts.app')

@section('title', 'Bookings')

@push('styles')
<style>
    .booking-row {
        transition: all 0.2s ease;
    }
    .booking-row:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
    }
    .loading-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10">
        
        @include('components.pageHeader', [
            'title' => 'Bookings',
            'subtitle' => 'Manage your bookings efficiently.',
            'action' => 'Back to Dashboard', 
            'actionUrl' => route('dashboard')
        ])


        <div class="mb-6">
            <label for="artistFilter" class="block text-sm font-medium text-slate-700 mb-1">Filter by Artist</label>
            <select id="artistFilter" name="artistFilter"
                    class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                <option value="">Loading artists...</option>
            </select>
        </div>


        {{-- Table --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Starts In</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artist</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTable" class="bg-white divide-y divide-gray-200">
                        {{-- Loading rows --}}
                        <tr id="loadingRow">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                <div>Loading bookings...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Load More Button --}}
        <div class="text-center mt-6">
            <button id="loadMoreBtn" class="hidden px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Load More Bookings
            </button>
            <div id="noMoreData" class="hidden text-gray-500 text-sm">
                No more bookings to load
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let isLoading = false;
let hasMorePages = true;
let currentArtistId = {{ auth()->id() }}; // Default to current user

document.addEventListener('DOMContentLoaded', function() {
    const artistFilter = document.getElementById('artistFilter');
    const bookingsTable = document.getElementById('bookingsTable');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadingRow = document.getElementById('loadingRow');
    const noMoreData = document.getElementById('noMoreData');

    // Load artists first, then bookings
    loadArtists();

    // Artist filter change
    artistFilter.addEventListener('change', function() {
        currentArtistId = this.value || {{ auth()->id() }}; // Default back to current user if empty
        currentPage = 1;
        hasMorePages = true;
        clearTable();
        loadBookings();
    });

    // Load more button
    loadMoreBtn.addEventListener('click', loadBookings);

    // Infinite scroll
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            if (!isLoading && hasMorePages) {
                loadBookings();
            }
        }
    });

    async function loadArtists() {
        try {
            const response = await fetch(`{{ route('artists.search') }}`);
            const artists = await response.json(); // Direct array, not {artists: [...]}
            
            console.log('Artists data:', artists);
            
            artistFilter.innerHTML = '';
            
            if (Array.isArray(artists) && artists.length > 0) {
                artists.forEach(artist => {
                    const option = document.createElement('option');
                    option.value = artist.id;
                    option.textContent = `${artist.first_name} ${artist.last_name}`;
                    // Pre-select current user
                    if (artist.id == {{ auth()->id() }}) {
                        option.selected = true;
                    }
                    artistFilter.appendChild(option);
                });
            } else {
                artistFilter.innerHTML = '<option value="{{ auth()->id() }}">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</option>';
            }
            
            // Load bookings after artists are loaded
            loadBookings();
            
        } catch (error) {
            console.error('Error loading artists:', error);
            artistFilter.innerHTML = '<option value="{{ auth()->id() }}">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }} (Error loading others)</option>';
            // Still load bookings for current user
            loadBookings();
        }
    }

    async function loadBookings() {
        if (isLoading || !hasMorePages) return;

        isLoading = true;
        loadMoreBtn.classList.add('hidden');

        if (currentPage === 1) {
            loadingRow.classList.remove('hidden');
        }

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 20,
                artist_id: currentArtistId // Always include artist_id (defaults to current user)
            });

            const response = await fetch(`{{ route('bookings.search') }}?${params}`);
            const json = await response.json();

            const bookings = json.bookings.data || json.bookings || [];

            console.log('Bookings data:', bookings);

            if (currentPage === 1) {
                loadingRow.classList.add('hidden');
                bookingsTable.innerHTML = '';
            }

            if (bookings && bookings.length > 0) {
                bookings.forEach(booking => {
                    bookingsTable.appendChild(createBookingRow(booking));
                });

                currentPage++;
                hasMorePages = json.bookings.next_page_url || json.has_more || false;

                if (hasMorePages) {
                    loadMoreBtn.classList.remove('hidden');
                } else {
                    noMoreData.classList.remove('hidden');
                }
            } else {
                if (currentPage === 1) {
                    bookingsTable.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="text-lg mb-2">No bookings found</div>
                                <div class="text-sm">Try selecting a different artist or create a new booking</div>
                            </td>
                        </tr>
                    `;
                } else {
                    noMoreData.classList.remove('hidden');
                }
                hasMorePages = false;
            }

        } catch (error) {
            console.error('Error loading bookings:', error);
            if (currentPage === 1) {
                loadingRow.classList.add('hidden');
                bookingsTable.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-red-500">
                            Error loading bookings. Please try again.
                        </td>
                    </tr>
                `;
            }
        } finally {
            isLoading = false;
        }
    }

    function createBookingRow(booking) {
        const row = document.createElement('tr');
        row.className = 'booking-row cursor-pointer';
        row.onclick = () => window.location.href = `{{ url('bookings') }}/${booking.id}`;

        // Calculate "Starts In" string
        let startsInStr = 'Not scheduled';
        if (booking.scheduled_start_at) {
            const now = new Date();
            const startDate = new Date(booking.scheduled_start_at);
            const diffMs = startDate - now;
            if (diffMs <= 0) {
                startsInStr = 'Started';
            } else {
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);
                if (diffDays > 1) {
                    startsInStr = `in ${diffDays} days`;
                } else if (diffDays === 1) {
                    startsInStr = 'in 1 day';
                } else if (diffHours > 0) {
                    startsInStr = `in ${diffHours} hours`;
                } else if (diffMins > 0) {
                    startsInStr = `in ${diffMins} minutes`;
                } else {
                    startsInStr = 'Started';
                }
            }
        }

        // Client info
        const client = booking.clients && booking.clients.length > 0 ? booking.clients[0] : null;
        const client_name = client ? `${client.first_name || ''} ${client.last_name || ''}`.trim() || 'Unknown Client' : 'Unknown Client';
        const client_email = client ? (client.email || '') : '';

        // Service info
        const service_name = booking.services && booking.services.length > 0 ? booking.services[0].name : 'Service';

        // Artist info
        const artist_name = booking.assigned_to ? `${booking.assigned_to.first_name || ''} ${booking.assigned_to.last_name || ''}`.trim() || booking.assigned_to.name || 'Artist' : 'Unassigned';

        // Payment status
        const totalPaid = booking.total_paid || 0;
        const totalAmount = booking.total_amount || 0;
        let paymentBadge = '';
        
        if (totalPaid >= totalAmount && totalAmount > 0) {
            paymentBadge = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>';
        } else if (totalPaid > 0) {
            paymentBadge = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>';
        } else {
            paymentBadge = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>';
        }

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${client_name}</div>
                <div class="text-sm text-gray-500">${client_email}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${service_name}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${startsInStr}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${artist_name}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${paymentBadge}
                <div class="text-xs text-gray-500 mt-1">£${totalPaid.toFixed(2)} / £${totalAmount.toFixed(2)}</div>
            </td>
        `;

        return row;
    }

    function clearTable() {
        bookingsTable.innerHTML = '';
        loadMoreBtn.classList.add('hidden');
        noMoreData.classList.add('hidden');
    }
});
</script>
@endpush