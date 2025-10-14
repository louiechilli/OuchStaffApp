@extends('layouts.app')

@section('title', 'Book Session')

@push('styles')
<style>
    .time-slot {
        transition: all 0.2s ease;
        border-radius: 8px !important; 
        cursor: pointer;
        font-size: 10px !important;
        margin: 2px !important;
        width: calc(100% - 4px) !important;
    }
    .time-slot:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }
    .time-slot.selected {
        background-color: #39794A !important;
        position: relative;
    }
    .time-slot.selected::after {
        content: '✓';
        position: absolute;
        top: 4px;
        right: 6px;
        color: white;
        font-weight: bold;
        font-size: 10px;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    @media (min-width: 768px) {
        .split-layout {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 0;
            height: 100vh;
            overflow: hidden;
        }
        .left-panel {
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
        }
        .right-panel {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        .calendar-content {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }
        .calendar-footer {
            flex-shrink: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="split-layout bg-gray-50">
    <!-- Left Panel - Session Details -->
    <div class="left-panel bg-white p-6 overflow-y-auto">
        <div class="mb-8">
            <button type="button" onclick="window.history.back()" class="text-gray-600 hover:text-gray-900 mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Book session</h1>
            
            <!-- Session Type -->
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Session type</span>
                    <button type="button" onclick="openModal('sessionTypeModal')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="sessionTypeDisplay">60 minutes consultation</p>
            </div>
            
            <!-- Artist Selection -->
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Artist</span>
                    <button type="button" onclick="openModal('artistModal')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="artistDisplay">{{ $artists->first()->first_name ?? 'Artist' }} {{ $artists->first()->last_name ?? '' }}</p>
            </div>
            
            <!-- Client Selection -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Client</span>
                    <button type="button" onclick="openModal('clientModal')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="clientDisplay">Select a client</p>
            </div>

            <!-- Notes Section -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <label for="bookingNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="bookingNotes" rows="3" placeholder="Add any special instructions or notes..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"></textarea>
            </div>
            
        </div>
    </div>
    
    <!-- Right Panel - Calendar -->
    <div class="right-panel bg-white">
        <div class="calendar-content">
            <!-- Calendar Header -->
            <div class="flex items-center justify-between max-w-6xl mx-auto p-4 align-middle">
                <button type="button" id="prevWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h2 class="text-xl font-semibold text-gray-900" id="currentMonth">December 2022</h2>
                <button type="button" id="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Weekly Calendar Grid -->
            <div class="max-w-6xl mx-auto">
                <!-- Day Headers -->
                <div class="grid grid-cols-8 gap-px bg-gray-200 border-y border-gray-200 overflow-hidden">
                    <div class="bg-white p-3"></div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Sun</div>
                        <div class="text-2xl font-semibold text-gray-400" id="day0">4</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Mon</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day1">5</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Tue</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day2">6</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Wed</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day3">7</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Thu</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day4">8</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Fri</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day5">9</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Sat</div>
                        <div class="text-2xl font-semibold text-gray-400" id="day6">10</div>
                    </div>
                </div>
                
                <!-- Time Slots Grid -->
                <div id="weeklyCalendar" class="relative grid grid-cols-8 gap-px bg-gray-200 border-b border-gray-200 overflow-hidden mb-8">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Footer - Sticky at Bottom -->
        <div class="calendar-footer">
            <div class="max-w-6xl mx-auto flex items-center justify-end">
                <div class="flex items-center gap-4">
                    <span class="text-2xl font-bold text-gray-900">€29</span>
                    <button type="button" id="confirmBtn" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-8 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Confirm time
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Type Modal -->
<div id="sessionTypeModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Select Session Type</h3>
                <button type="button" onclick="closeModal('sessionTypeModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <span class="font-medium text-gray-900">30 minutes consultation</span>
                <input type="radio" name="modal_duration" value="30" class="h-4 w-4 text-teal-600">
            </label>
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <span class="font-medium text-gray-900">45 minutes consultation</span>
                <input type="radio" name="modal_duration" value="45" class="h-4 w-4 text-teal-600">
            </label>
            <label class="flex items-center justify-between p-4 border border-teal-600 bg-teal-50 rounded-lg cursor-pointer">
                <span class="font-medium text-gray-900">60 minutes consultation</span>
                <input type="radio" name="modal_duration" value="60" checked class="h-4 w-4 text-teal-600">
            </label>
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <span class="font-medium text-gray-900">90 minutes consultation</span>
                <input type="radio" name="modal_duration" value="90" class="h-4 w-4 text-teal-600">
            </label>
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <span class="font-medium text-gray-900">120 minutes consultation</span>
                <input type="radio" name="modal_duration" value="120" class="h-4 w-4 text-teal-600">
            </label>
        </div>
        <div class="p-6 border-t border-gray-200">
            <button type="button" onclick="updateSessionType()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-3 rounded-lg">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- Artist Modal -->
<div id="artistModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Select Artist</h3>
                <button type="button" onclick="closeModal('artistModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-3">
            @foreach($artists as $artist)
            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer artist-option">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ $artist->first_name }}&size=48&background=e0e7ff&color=4f46e5" 
                        alt="{{ $artist->first_name }}" 
                        class="w-12 h-12 rounded-full">
                    <div>
                        <div class="font-medium text-gray-900">{{ $artist->first_name }} {{ $artist->last_name }}</div>
                        @if($artist->email)
                        <div class="text-sm text-gray-600">{{ $artist->email }}</div>
                        @endif
                    </div>
                </div>
                <input type="radio" name="modal_artist" value="{{ $artist->id }}" class="h-4 w-4 text-teal-600" {{ $loop->first ? 'checked' : '' }}>
            </label>
            @endforeach
        </div>
        <div class="p-6 border-t border-gray-200">
            <button type="button" onclick="updateArtist()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-3 rounded-lg">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- Client Modal -->
<div id="clientModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Select Client</h3>
                <button type="button" onclick="closeModal('clientModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <input type="text" id="modalClientSearch" placeholder="Search by name, email, or phone..." 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            <div id="modalClientList" class="space-y-2">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
        <div class="p-6 border-t border-gray-200">
            <button type="button" onclick="toggleNewClientForm()" class="w-full border-2 border-dashed border-gray-300 hover:border-gray-400 text-gray-700 font-medium px-6 py-3 rounded-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span id="newClientToggleText">Add New Client</span>
            </button>
            
            <!-- New Client Form -->
            <div id="newClientFormModal" class="hidden mt-4 space-y-3">
                <div>
                    <input type="text" id="newClientFirstName" placeholder="First Name" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p id="errorFirstName" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                <div>
                    <input type="text" id="newClientLastName" placeholder="Last Name" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p id="errorLastName" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                <div>
                    <input type="email" id="newClientEmail" placeholder="Email Address" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p id="errorEmail" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                <div>
                    <input type="tel" id="newClientPhone" placeholder="Phone Number" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p id="errorPhone" class="hidden mt-1 text-sm text-red-600"></p>
                </div>
                <button type="button" onclick="saveNewClient()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-3 rounded-lg">
                    Save Client
                </button>
            </div>
        </div>
    </div>
</div>

<form id="bookingForm" action="{{ route('bookings.store') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="service_id" value="{{ $service->id }}">
    <input type="hidden" name="artist_id" id="artistId" value="{{ $artists->first()->id ?? '' }}">
    <input type="hidden" name="client_id" id="clientId">
    <input type="hidden" name="duration" id="duration" value="60">
    <input type="hidden" name="selected_date" id="selectedDate">
    <input type="hidden" name="selected_time" id="selectedTime">
</form>
@endsection

@push('scripts')
<script>
const clients = @json($clients);
const artists = @json($artists);
const csrf = '{{ csrf_token() }}';
const timeSlots = [
    '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', 
    '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
];

let selectedSlot = null;
let currentWeekStart = new Date();
let clientsList = [...clients];

document.addEventListener('DOMContentLoaded', function() {
    initializeWeekView();
    renderWeeklyCalendar();
    populateClientList();
});

function initializeWeekView() {
    const today = new Date();
    currentWeekStart = new Date(today);
    currentWeekStart.setDate(today.getDate() - today.getDay());
}

function renderWeeklyCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    document.getElementById('currentMonth').textContent = 
        `${monthNames[currentWeekStart.getMonth()]} ${currentWeekStart.getFullYear()}`;
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(currentWeekStart);
        date.setDate(currentWeekStart.getDate() + i);
        document.getElementById(`day${i}`).textContent = date.getDate();
    }
    
    const duration = parseInt(document.getElementById('duration').value);
    const calendar = document.getElementById('weeklyCalendar');
    calendar.innerHTML = '';
    
    // Calculate how many slots this duration spans (each slot is 60 minutes)
    const slotsSpanned = Math.ceil(duration / 60);
    
    // Track occupied slots to prevent overlapping
    const occupiedSlots = {};
    
    timeSlots.forEach((time, timeIndex) => {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'bg-white p-3 text-sm text-gray-600 border-t border-gray-100';
        timeLabel.textContent = time.replace(':00', ' AM').replace('13:00', '1 PM').replace('14:00', '2 PM')
            .replace('15:00', '3 PM').replace('16:00', '4 PM').replace('17:00', '5 PM').replace('18:00', '6 PM');
        calendar.appendChild(timeLabel);
        
        for (let day = 0; day < 7; day++) {
            const slotKey = `${day}-${timeIndex}`;
            const slotDiv = document.createElement('div');
            slotDiv.className = 'bg-white border-t border-gray-100 relative';
            
            // Skip if this slot is occupied by a previous event
            if (occupiedSlots[slotKey]) {
                calendar.appendChild(slotDiv);
                continue;
            }
            
            const availabilityThreshold = duration <= 30 ? 0.3 : duration <= 60 ? 0.4 : duration <= 90 ? 0.5 : 0.6;
            
            // Only create slots if we have enough remaining time slots and it's a weekday
            const hasEnoughSpace = timeIndex + slotsSpanned <= timeSlots.length;
            
            if (hasEnoughSpace && Math.random() > availabilityThreshold && day > 0 && day < 6) {
                const slot = document.createElement('button');
                slot.type = 'button';
                
                // Calculate height based on exact duration
                // Each hour slot has a base height + padding + border
                const baseSlotHeight = 48; // Height of one time slot row
                const gapHeight = 1; // Gap between rows (gap-px)
                const marginPixels = 4; // Total vertical margin (2px top + 2px bottom)

                // Calculate the exact height based on duration as a fraction of hours
                const durationInHours = duration / 60;
                const fullSlots = Math.floor(durationInHours);
                const partialSlot = durationInHours - fullSlots;
                
                // Total height calculation
                let totalHeight;
                if (partialSlot > 0) {
                    // Has partial slot: full slots with gaps + partial slot
                    totalHeight = (fullSlots * (baseSlotHeight + gapHeight)) + (partialSlot * baseSlotHeight);
                } else {
                    // Only full slots: adjust based on number of slots
                    if (fullSlots === 1) {
                        totalHeight = baseSlotHeight - 4; // 60 mins
                    } else if (fullSlots === 2) {
                        totalHeight = (2 * baseSlotHeight) + 1 - 6; // 120 mins
                    } else {
                        totalHeight = (fullSlots * baseSlotHeight) + ((fullSlots - 1) * gapHeight);
                    }
                }

                totalHeight = Math.round(totalHeight - marginPixels);

                slot.className = 'time-slot absolute top-0 left-0 right-0 bg-[#E4F6E9] hover:bg-[#e4f6e9] text-teal-900 text-sm font-medium py-2 px-3 rounded-lg z-10';
                slot.style.height = `${totalHeight}px`;
                slot.style.minHeight = `${totalHeight}px`;
                slot.style.maxHeight = `${totalHeight}px`;
                
                const startHour = parseInt(time.split(':')[0]);
                const startMinute = parseInt(time.split(':')[1]);
                const totalMinutes = startHour * 60 + startMinute + duration;
                const endHour = Math.floor(totalMinutes / 60);
                const endMinute = totalMinutes % 60;
                const endTime = `${endHour}:${endMinute.toString().padStart(2, '0')}`;
                
                slot.textContent = `${time}-${endTime}`;
                slot.dataset.date = day;
                slot.dataset.time = time;
                slot.dataset.duration = duration;
                
                slot.addEventListener('click', function() {
                    selectTimeSlot(this);
                });
                
                slotDiv.appendChild(slot);
                
                // Mark occupied slots
                for (let s = 0; s < slotsSpanned; s++) {
                    occupiedSlots[`${day}-${timeIndex + s}`] = true;
                }
            }
            
            calendar.appendChild(slotDiv);
        }
    });
}

function selectTimeSlot(element) {
    // Store the current height BEFORE making any changes
    const currentHeight = element.offsetHeight;
    
    // Temporarily disable transitions to prevent animation artifacts
    element.style.transition = 'none';
    
    // Remove selection from all slots
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected', 'bg-teal-600', 'text-white');
        slot.classList.add('bg-[#E4F6E9]', 'text-teal-900');
    });
    
    // Add selection to clicked slot
    element.classList.add('selected', 'bg-teal-600', 'text-white');
    element.classList.remove('bg-[#E4F6E9]', 'text-teal-900');
    
    // Force height to stay exactly the same using !important
    element.style.setProperty('height', `${currentHeight}px`, 'important');
    element.style.setProperty('min-height', `${currentHeight}px`, 'important');
    element.style.setProperty('max-height', `${currentHeight}px`, 'important');
    
    // Re-enable transitions after a brief delay
    setTimeout(() => {
        element.style.transition = '';
    }, 50);
    
    // Store selection data
    selectedSlot = {
        date: element.dataset.date,
        time: element.dataset.time
    };
    
    document.getElementById('selectedTime').value = element.dataset.time;
    
    const date = new Date(currentWeekStart);
    date.setDate(currentWeekStart.getDate() + parseInt(element.dataset.date));
    document.getElementById('selectedDate').value = date.toISOString().split('T')[0];
    
    checkFormValidity();
}

function checkFormValidity() {
    const confirmBtn = document.getElementById('confirmBtn');
    const hasClient = document.getElementById('clientId').value;
    const hasTime = selectedSlot !== null;
    
    confirmBtn.disabled = !(hasClient && hasTime);
}

document.getElementById('prevWeek').addEventListener('click', function() {
    currentWeekStart.setDate(currentWeekStart.getDate() - 7);
    renderWeeklyCalendar();
});

document.getElementById('nextWeek').addEventListener('click', function() {
    currentWeekStart.setDate(currentWeekStart.getDate() + 7);
    renderWeeklyCalendar();
});

document.getElementById('confirmBtn').addEventListener('click', function() {
    document.getElementById('bookingForm').submit();
});

function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function updateSessionType() {
    const selected = document.querySelector('input[name="modal_duration"]:checked');
    if (selected) {
        document.getElementById('duration').value = selected.value;
        document.getElementById('sessionTypeDisplay').textContent = `${selected.value} minutes consultation`;
        closeModal('sessionTypeModal');
        
        selectedSlot = null;
        document.getElementById('selectedTime').value = '';
        document.getElementById('selectedDate').value = '';
        checkFormValidity();
        renderWeeklyCalendar();
    }
}

function updateArtist() {
    const selected = document.querySelector('input[name="modal_artist"]:checked');
    if (selected) {
        const artistId = selected.value;
        document.getElementById('artistId').value = artistId;
        
        const artist = artists.find(a => a.id == artistId);
        if (artist) {
            document.getElementById('artistDisplay').textContent = `${artist.first_name} ${artist.last_name}`;
        }
        
        closeModal('artistModal');
        
        selectedSlot = null;
        document.getElementById('selectedTime').value = '';
        document.getElementById('selectedDate').value = '';
        checkFormValidity();
        renderWeeklyCalendar();
    }
}

function populateClientList() {
    const list = document.getElementById('modalClientList');
    list.innerHTML = clientsList.map(client => {
        const name = [client.first_name, client.last_name].filter(Boolean).join(' ') || 'Unnamed Client';
        return `
            <button type="button" onclick="selectClient(${client.id}, '${name.replace(/'/g, "\\'")}', '${client.email || ''}', '${client.phone || ''}')" 
                    class="w-full text-left p-4 border border-gray-200 rounded-lg hover:bg-gray-50 client-option">
                <div class="font-medium text-gray-900">${name}</div>
                ${client.email ? `<div class="text-sm text-gray-600">${client.email}</div>` : ''}
                ${client.phone ? `<div class="text-sm text-gray-600">${client.phone}</div>` : ''}
            </button>
        `;
    }).join('');
}

function selectClient(id, name, email, phone) {
    document.getElementById('clientId').value = id;
    let displayText = name;
    if (email) displayText += `\n${email}`;
    document.getElementById('clientDisplay').textContent = name;
    closeModal('clientModal');
    checkFormValidity();
}

function toggleNewClientForm() {
    const form = document.getElementById('newClientFormModal');
    const toggleText = document.getElementById('newClientToggleText');
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        toggleText.textContent = 'Cancel';
        ['newClientFirstName', 'newClientLastName', 'newClientEmail', 'newClientPhone'].forEach(id => {
            document.getElementById(id).value = '';
            document.getElementById(id).classList.remove('border-red-500');
        });
        ['errorFirstName', 'errorLastName', 'errorEmail', 'errorPhone'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.textContent = '';
        });
    } else {
        form.classList.add('hidden');
        toggleText.textContent = 'Add New Client';
    }
}

async function saveNewClient() {
    ['errorFirstName', 'errorLastName', 'errorEmail', 'errorPhone'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.textContent = '';
    });
    ['newClientFirstName', 'newClientLastName', 'newClientEmail', 'newClientPhone'].forEach(id => {
        document.getElementById(id).classList.remove('border-red-500');
    });
    
    const firstName = document.getElementById('newClientFirstName').value.trim();
    const lastName = document.getElementById('newClientLastName').value.trim();
    const email = document.getElementById('newClientEmail').value.trim();
    const phone = document.getElementById('newClientPhone').value.trim();
    
    let hasError = false;
    
    if (!firstName && !lastName) {
        document.getElementById('newClientFirstName').classList.add('border-red-500');
        document.getElementById('newClientLastName').classList.add('border-red-500');
        document.getElementById('errorFirstName').textContent = 'First or last name is required';
        document.getElementById('errorFirstName').classList.remove('hidden');
        hasError = true;
    }
    
    if (!email && !phone) {
        document.getElementById('newClientEmail').classList.add('border-red-500');
        document.getElementById('newClientPhone').classList.add('border-red-500');
        document.getElementById('errorEmail').textContent = 'Email or phone is required';
        document.getElementById('errorEmail').classList.remove('hidden');
        hasError = true;
    }
    
    if (hasError) return;
    
    try {
        const response = await fetch('{{ route("clients.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                first_name: firstName,
                last_name: lastName,
                email: email || null,
                phone: phone || null
            })
        });
        
        if (!response.ok) {
            throw new Error('Failed to create client');
        }
        
        const data = await response.json();
        const newClient = data.client || data;
        
        clientsList.push(newClient);
        populateClientList();
        
        const name = [newClient.first_name, newClient.last_name].filter(Boolean).join(' ');
        selectClient(newClient.id, name, newClient.email || '', newClient.phone || '');
        
        toggleNewClientForm();
        
    } catch (error) {
        console.error(error);
        document.getElementById('newClientFirstName').classList.add('border-red-500');
        document.getElementById('errorFirstName').textContent = 'Failed to create client. Please try again.';
        document.getElementById('errorFirstName').classList.remove('hidden');
    }
}

document.getElementById('modalClientSearch').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.client-option').forEach(option => {
        const text = option.textContent.toLowerCase();
        option.style.display = text.includes(query) ? 'block' : 'none';
    });
});

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
@endpush