<div x-data="clientSelect()" class="space-y-6">
    <div class="flex flex-row justify-between items-center">
        <button type="button" class="px-3 py-2 bg-green-500 text-white hover:bg-green-400 rounded-lg cursor-pointer" @click="openModal">+ Add new client</button>
    </div>
    <div class="relative">
        <input type="text"
               x-model="query"
               @input="searchClients"
               placeholder="Search for client by name, email, or phone"
               class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
        <ul x-show="query.length > 1" class="absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-48 overflow-y-auto">
            <template x-for="client in results" :key="client.id">
                <li @click="selectClient(client)"
                    class="px-3 py-2 hover:bg-slate-100 cursor-pointer"
                    x-text="client.first_name + ' ' + client.last_name + ' - ' + '(' + (client.email ?? 'no email') + ')' + ' - ' + '(' + (client.phone ?? 'no phone') + ')' "></li>
            </template>
            <li x-show="query.length > 1" class="px-3 py-2 text-primary-600 hover:bg-primary-50 cursor-pointer" @click="openModal">+ Add new client</li>
        </ul>
    </div>

    <!-- Hidden input for form -->
    <input type="hidden" name="client_id" :value="selected?.id">

    <!-- Selected client preview -->
    <template x-if="selected">
        <div class="p-3 bg-white rounded-lg border text-sm flex flex-col gap-4">
            <span class="font-medium" x-text="selected.first_name + ' ' + selected.last_name"></span>
            <span class="text-slate-500" x-text="selected.email"></span>
            <span class="text-slate-500" x-text="selected.phone"></span>
        </div>
    </template>

    <!-- Add New Client Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow">
            <h2 class="text-lg font-semibold mb-4">Add New Client</h2>
            <form @submit.prevent="createClient" class="">

                <div class="flex flex-col gap-4">
                    <input type="text" x-model="newClient.first_name" placeholder="First name" class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                    <input type="text" x-model="newClient.last_name" placeholder="Last name" class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                    <input type="email" x-model="newClient.email" placeholder="Email" class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                    <input type="text" x-model="newClient.phone" placeholder="Phone" class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-400 text-white rounded-lg">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function clientSelect() {
        return {
            query: '',
            results: [],
            selected: null,
            showModal: false,
            newClient: { first_name: '', last_name: '', email: '', phone: '' },

            searchClients() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                axios.get('{{ route('clients.search') }}', { params: { q: this.query } })
                    .then(res => {
                        this.results = res.data;
                    });
            },

            selectClient(client) {
                this.selected = client;
                this.query = client.first_name + ' ' + client.last_name;
                this.results = [];
                if (typeof this.step !== 'undefined' && this.step === 1) {
                    this.step++;
                }
            },

            openModal() {
                this.newClient = { first_name: '', last_name: '', email: '', phone: '' };
                this.showModal = true;
            },

            createClient() {
                axios.post('{{ route('clients.store') }}', this.newClient)
                    .then(res => {
                        this.selected = res.data;
                        this.query = this.selected.first_name + ' ' + this.selected.last_name;
                        this.showModal = false;
                        this.results = [];
                    })
                    .catch(err => {
                        alert('Failed to create client');
                    });
            }
        }
    }
</script>
