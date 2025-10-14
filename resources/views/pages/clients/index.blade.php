@extends('layouts.app')

@section('title', 'Client Management')

@push('styles')
<style>
    .client-card {
        transition: all 0.3s ease;
    }
    .client-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
    .fade-in {
        animation: fadeIn 0.4s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .client-avatar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .search-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .infinite-loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10">

        @include('components.pageHeader', [
            'title' => 'Client Management',
            'subtitle' => 'Everything you need to manage your clients: contacts, history & bookings.',
            'action' => 'Back to Dashboard', 
            'actionUrl' => route('dashboard')
        ])

        {{-- Stats Row --}}
        <div class="mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-6">

                {{-- Stats Cards --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Total Clients</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-800" id="totalClients">{{ $stats['clients_total'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-100 p-3">
                            <i class="fa-regular fa-users text-lg text-slate-600"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Active clients</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">New This Week</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['clients_new_week'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-green-100 p-3">
                            <i class="fa-regular fa-user-plus text-lg text-green-600"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Recent signups</p>
                </div>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="mb-8">
            <h3 class="text-base font-semibold text-slate-800 mb-4">Quick actions</h3>
            <div class="flex flex-wrap gap-3">
                
                <div class="lg:col-span-2">
                    <div class="search-container rounded-2xl border border-slate-200 p-1 shadow-sm">
                        <div class="relative">
                            <input
                                type="text"
                                id="clientSearch"
                                placeholder="Search clients by name, email, or phone..."
                                class="w-full pl-12 pr-12 py-4 bg-transparent border-0 focus:outline-none focus:ring-0 text-base placeholder-slate-400"
                                autocomplete="off"
                            >
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-regular fa-magnifying-glass text-slate-400"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <div id="searchSpinner" class="hidden">
                                    <i class="fa-solid fa-spinner-third animate-spin text-slate-400"></i>
                                </div>
                                <button id="clearSearch" class="hidden text-slate-400 hover:text-slate-600 transition-colors">
                                    <i class="fa-regular fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <i class="fa-regular fa-plus text-green-600"></i>
                    New client
                </a>
                
                <button onclick="refreshClients()" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <i class="fa-regular fa-arrows-rotate text-slate-600"></i>
                    Refresh
                </button>

            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Clients List --}}
            <section class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base md:text-lg font-semibold text-slate-800">All clients</h3>
                    <div class="flex items-center gap-3">
                        <select id="sortBy" class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="name">Sort by name</option>
                            <option value="created_at">Newest first</option>
                            <option value="last_booking">Recent booking</option>
                        </select>
                    </div>
                </div>

                {{-- Loading State --}}
                <div id="loadingState" class="hidden space-y-4">
                    @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-4 p-4 border border-slate-100 rounded-xl">
                        <div class="w-12 h-12 bg-slate-200 rounded-full loading-shimmer"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-slate-200 rounded loading-shimmer mb-2 w-1/3"></div>
                            <div class="h-3 bg-slate-200 rounded loading-shimmer w-1/2"></div>
                        </div>
                        <div class="w-20 h-8 bg-slate-200 rounded loading-shimmer"></div>
                    </div>
                    @endfor
                </div>

                {{-- Empty State --}}
                <div id="emptyState" class="hidden text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-users text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-800 mb-2">No clients found</h3>
                    <p class="text-slate-500 mb-6">Try adjusting your search or add your first client</p>
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="fa-regular fa-plus"></i>
                        Add your first client
                    </a>
                </div>

                {{-- Clients List --}}
                <div id="clientsList" class="space-y-3 max-h-[600px] overflow-y-auto">
                    {{-- Will be populated via JavaScript --}}
                </div>

                {{-- Infinite Loading Indicator --}}
                <div id="infiniteLoadingIndicator" class="hidden text-center py-4 mt-4">
                    <div class="flex items-center justify-center gap-2 text-slate-500">
                        <i class="fa-solid fa-spinner-third animate-spin"></i>
                        <span>Loading more clients...</span>
                    </div>
                </div>

                {{-- End of List Indicator --}}
                <div id="endOfListIndicator" class="hidden text-center py-4 mt-4">
                    <div class="text-slate-500 text-sm">
                        <i class="fa-regular fa-check-circle text-green-500 mr-2"></i>
                        All clients loaded
                    </div>
                </div>
            </section>

            {{-- Right Rail --}}
            <div class="space-y-6">
                
                {{-- Recent Activity --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-slate-800">Recent activity</h3>
                        <a href="{{ route('clients.index') }}" class="text-sm text-slate-500 hover:text-slate-700">View all</a>
                    </div>
                    <div class="space-y-3" id="recentActivity">
                        <div class="text-sm text-slate-500 py-6">Loading recent activity...</div>
                    </div>
                </section>

                {{-- Quick Stats --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-800 mb-4">Client insights</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Most booked service</span>
                            <span class="text-sm font-medium text-slate-800">{{ $insights['popular_service'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Average booking value</span>
                            <span class="text-sm font-medium text-slate-800">£{{ $insights['avg_booking_value'] ?? '0' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Return client rate</span>
                            <span class="text-sm font-medium text-slate-800">{{ $insights['return_rate'] ?? '0' }}%</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

{{-- Client Detail Modal --}}
<div id="clientModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeClientModal()"></div>
        
        <div class="inline-block align-bottom bg-white rounded-2xl px-6 pt-6 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-md sm:w-full">
            <div id="modalContent">
                {{-- Modal content populated via JavaScript --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
let searchTimeout = null;
let currentPage = 1;
let isLoading = false;
let hasMorePages = true;
let allClients = [];
let currentQuery = '';
let currentSort = 'name';
let isSearchMode = false;
const perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('clientSearch');
    const clearBtn = document.getElementById('clearSearch');
    const searchSpinner = document.getElementById('searchSpinner');
    const clientsList = document.getElementById('clientsList');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const sortSelect = document.getElementById('sortBy');
    const infiniteLoadingIndicator = document.getElementById('infiniteLoadingIndicator');
    const endOfListIndicator = document.getElementById('endOfListIndicator');

    // Fetch first page of clients on load
    fetchClients(true);
    loadRecentActivity();

    // Setup infinite scroll
    clientsList.addEventListener('scroll', handleInfiniteScroll);

    // Search functionality (with debounce)
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        currentQuery = query;
        isSearchMode = !!query;

        if (query) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        searchTimeout = setTimeout(() => {
            resetPagination();
            fetchClients(true);
        }, 300);
    });

    // Clear search
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        currentQuery = '';
        isSearchMode = false;
        clearBtn.classList.add('hidden');
        resetPagination();
        fetchClients(true);
    });

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        resetPagination();
        fetchClients(true);
    });

    function handleInfiniteScroll() {
        if (isLoading || !hasMorePages) return;

        const { scrollTop, scrollHeight, clientHeight } = clientsList;
        const scrollPercentage = (scrollTop + clientHeight) / scrollHeight;

        // Load more when 80% scrolled
        if (scrollPercentage > 0.8) {
            fetchClients(false);
        }
    }

    function resetPagination() {
        currentPage = 1;
        hasMorePages = true;
        allClients = [];
        hideEndOfListIndicator();
    }

    async function fetchClients(reset = false) {
        if (isLoading) return;

        try {
            isLoading = true;
            
            if (reset) {
                resetPagination();
                showLoadingState();
            } else {
                showInfiniteLoadingIndicator();
            }

            const params = {
                page: currentPage,
                per_page: perPage,
                sort: currentSort
            };

            if (currentQuery) {
                params.q = currentQuery;
            }

            const response = await axios.get(`{{ route('clients.search') }}`, { params });
            const data = response.data;
            
            // Handle different response formats
            let clients = [];
            let total = 0;
            let hasMore = false;

            if (data.data) {
                // Laravel pagination format
                clients = data.data;
                total = data.total || clients.length;
                hasMore = data.next_page_url !== null;
            } else if (Array.isArray(data)) {
                // Simple array format
                clients = data;
                total = clients.length;
                hasMore = clients.length === perPage; // Assume more if we got a full page
            } else {
                // Object with clients array
                clients = data.clients || [];
                total = data.total || clients.length;
                hasMore = data.has_more || false;
            }

            if (reset) {
                allClients = clients;
                renderClients(allClients);
            } else {
                allClients = [...allClients, ...clients];
                appendClients(clients);
            }

            // Update pagination state
            hasMorePages = hasMore && clients.length === perPage;
            if (hasMorePages) {
                currentPage++;
            } else {
                showEndOfListIndicator();
            }

            // Update total count (only on reset/first load)
            if (reset) {
                document.getElementById('totalClients').textContent = isSearchMode ? allClients.length : total;
            }

        } catch (error) {
            console.error('Fetching clients error:', error);
            if (reset) {
                showEmptyState();
            }
        } finally {
            isLoading = false;
            hideLoadingState();
            hideInfiniteLoadingIndicator();
        }
    }

    function renderClients(clients) {
        if (!clients || clients.length === 0) {
            showEmptyState();
            return;
        }

        hideEmptyState();
        clientsList.innerHTML = clients.map(client => createClientRow(client)).join('');
        
        // Add staggered animations
        setTimeout(() => {
            clientsList.querySelectorAll('.client-row').forEach((row, index) => {
                setTimeout(() => {
                    row.classList.add('fade-in');
                }, index * 50);
            });
        }, 50);
    }

    function appendClients(clients) {
        if (!clients || clients.length === 0) return;

        const newRows = clients.map(client => createClientRow(client)).join('');
        clientsList.insertAdjacentHTML('beforeend', newRows);

        // Add animations to new rows only
        const newRowElements = clientsList.querySelectorAll('.client-row:not(.fade-in)');
        setTimeout(() => {
            newRowElements.forEach((row, index) => {
                setTimeout(() => {
                    row.classList.add('fade-in');
                }, index * 50);
            });
        }, 50);
    }

    function createClientRow(client) {
        const displayName = [client.name, client.first_name, client.last_name]
            .filter(Boolean)
            .join(' ')
            .trim() || 'Unnamed Client';
            
        const initials = displayName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        const lastBooking = client.last_booking_at ? new Date(client.last_booking_at).toLocaleDateString() : 'Never';
        
        return `
            <div class="client-row flex items-center gap-4 p-4 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer transition-all" 
                 onclick="openClientModal(${client.id})"
                 data-client-id="${client.id}">
                
                <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-slate-800 truncate">${displayName}</h4>
                    <div class="flex items-center gap-4 text-sm text-slate-500">
                        ${client.email ? `<span class="truncate">${client.email}</span>` : ''}
                        ${client.phone ? `<span class="truncate">${client.phone}</span>` : ''}
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-xs text-slate-500">Last booking</div>
                    <div class="text-sm font-medium text-slate-700">${lastBooking}</div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button onclick="event.stopPropagation(); editClient(${client.id})" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit client">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    <button onclick="event.stopPropagation(); createBooking(${client.id})" class="p-2 text-slate-400 hover:text-green-600 transition-colors" title="Create booking">
                        <i class="fa-regular fa-calendar-plus"></i>
                    </button>
                </div>
            </div>
        `;
    }

    async function loadRecentActivity() {
        try {
            const response = await axios.get(`{{ route('clients.activity') }}`);
            const activities = response.data.activities || [];
            
            const activityContainer = document.getElementById('recentActivity');
            
            if (activities.length === 0) {
                activityContainer.innerHTML = '<div class="text-sm text-slate-500 py-4">No recent activity</div>';
                return;
            }
            
            activityContainer.innerHTML = activities.map(activity => `
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div>
                        <p class="text-slate-800">${activity.description}</p>
                        <p class="text-slate-500 text-xs mt-1">${new Date(activity.created_at).toLocaleDateString()}</p>
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Activity loading error:', error);
        }
    }

    // UI State Management Functions
    function showLoadingState() {
        loadingState.classList.remove('hidden');
        clientsList.innerHTML = '';
        emptyState.classList.add('hidden');
    }

    function hideLoadingState() {
        loadingState.classList.add('hidden');
    }

    function showEmptyState() {
        emptyState.classList.remove('hidden');
        clientsList.innerHTML = '';
        loadingState.classList.add('hidden');
    }

    function hideEmptyState() {
        emptyState.classList.add('hidden');
    }

    function showInfiniteLoadingIndicator() {
        infiniteLoadingIndicator.classList.remove('hidden');
    }

    function hideInfiniteLoadingIndicator() {
        infiniteLoadingIndicator.classList.add('hidden');
    }

    function showEndOfListIndicator() {
        if (allClients.length > perPage) { // Only show if we have multiple pages
            endOfListIndicator.classList.remove('hidden');
        }
    }

    function hideEndOfListIndicator() {
        endOfListIndicator.classList.add('hidden');
    }

    // Global functions for onclick handlers
    window.openClientModal = function(clientId) {
        const client = allClients.find(c => c.id == clientId);
        if (!client) return;

        const displayName = [client.name, client.first_name, client.last_name]
            .filter(Boolean)
            .join(' ')
            .trim() || 'Unnamed Client';

        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = `
            <div class="text-center">
                <div class="client-avatar w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center text-white font-bold text-xl">
                    ${displayName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)}
                </div>
                <h3 class="text-xl font-semibold text-slate-800 mb-2">${displayName}</h3>
                <div class="space-y-1 text-slate-600 mb-6 text-sm">
                    ${client.email ? `<p>${client.email}</p>` : ''}
                    ${client.phone ? `<p>${client.phone}</p>` : ''}
                </div>
                <div class="flex gap-3 justify-center">
                    <a href="{{ url('clients') }}/${client.id}/edit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Edit Client
                    </a>
                    <a href="{{ url('bookings/create') }}?client=${client.id}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors text-sm">
                        <i class="fa-regular fa-calendar-plus"></i>
                        New Booking
                    </a>
                </div>
            </div>
        `;
        document.getElementById('clientModal').classList.remove('hidden');
    };

    window.closeClientModal = function() {
        document.getElementById('clientModal').classList.add('hidden');
    };

    window.editClient = function(clientId) {
        window.location.href = `{{ url('clients') }}/${clientId}/edit`;
    };

    window.createBooking = function(clientId) {
        window.location.href = `{{ url('bookings/create') }}?client=${clientId}`;
    };

    window.refreshClients = async function() {
        resetPagination();
        await fetchClients(true);
        loadRecentActivity();
    };
});
</script>
@endpush