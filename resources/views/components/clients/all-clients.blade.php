@can('view-clients')
    {{-- All clients --}}
    <section class="md:grid md:grid-cols-1 lg:grid-cols-2 rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
        <header class="mb-4 md:col-span-full">
            <h2 class="text-xl font-semibold text-slate-700">Clients</h2>
            <p class="text-sm text-slate-500">Manage and view all clients in the system.</p>
        </header>
        <div class="mb-4 flex items-center space-x-2 md:col-span-full">
            <input type="text" name="search" id="search" placeholder="Search clients..." class="flex-grow rounded border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-500" />
            <button id="clear-search" type="button" class="rounded border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200 focus:outline-none focus:ring focus:ring-indigo-500">Clear</button>
        </div>
        <div class="mt-4 overflow-x-auto md:col-span-full">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-500">
                <tr>
                    <th class="py-2 pr-4">First Name</th>
                    <th class="py-2 pr-4">Last Name</th>
                    <th class="py-2 pr-4">Email</th>
                    <th class="py-2 pr-4">Phone</th>
                    <th class="py-2 pr-4">Created At</th>
                    <th class="py-2 pr-4">Actions</th>
                </tr>
                </thead>
                <tbody id="clients-tbody" class="divide-y divide-slate-100">
                @forelse($clients ?? [] as $client)
                    <tr class="align-top">
                        <td class="py-2 pr-4 whitespace-nowrap">{{ $client->first_name }}</td>
                        <td class="py-2 pr-4">{{ $client->last_name }}</td>
                        <td class="py-2 pr-4">{{ $client->email }}</td>
                        <td class="py-2 pr-4">{{ $client->phone }}</td>
                        <td class="py-2 pr-4">{{ $client->created_at->format('Y-m-d') }}</td>
                        <td class="py-2 pr-4">
                            <a href="{{ route('clients.show', $client) }}" class="inline-block rounded bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-700">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-slate-500">No clients found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 md:col-span-full">
            {{ $clients->links() }}
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function() {
            const searchInput = document.getElementById('search');
            const clearBtn = document.getElementById('clear-search');
            const tbody = document.getElementById('clients-tbody');
            let debounceTimeout = null;

            function renderClients(clients) {
                if (!clients.length) {
                    tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-slate-500">No clients found.</td></tr>';
                    return;
                }
                tbody.innerHTML = clients.map(client => {
                    const createdAt = new Date(client.created_at).toISOString().slice(0,10);
                    const viewUrl = '/clients/' + client.id;
                    return `
                        <tr class="align-top">
                            <td class="py-2 pr-4 whitespace-nowrap">${client.first_name}</td>
                            <td class="py-2 pr-4">${client.last_name}</td>
                            <td class="py-2 pr-4">${client.email}</td>
                            <td class="py-2 pr-4">${client.phone}</td>
                            <td class="py-2 pr-4">${createdAt}</td>
                            <td class="py-2 pr-4">
                                <a href="${viewUrl}" class="inline-block rounded bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-700">View</a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function fetchClients(query) {
                axios.get('/clients/search', { params: { q: query } })
                    .then(response => {
                        if (response.data && Array.isArray(response.data)) {
                            renderClients(response.data);
                        } else {
                            tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-slate-500">No clients found.</td></tr>';
                        }
                    })
                    .catch(() => {
                        tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-slate-500">Error loading clients.</td></tr>';
                    });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimeout);
                const query = this.value.trim();
                debounceTimeout = setTimeout(() => {
                    if (query.length > 0) {
                        fetchClients(query);
                    } else {
                        // Reload the page or reset to original clients
                        location.reload();
                    }
                }, 500);
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                location.reload();
            });
        })();
    </script>
@endcan
