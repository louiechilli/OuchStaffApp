@extends('layouts.app')

@section('title', 'Book Session')

@push('styles')
<style>
    /* Welcome Modal Styles */
    .welcome-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .welcome-modal.active {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    .welcome-modal-content {
        background: white;
        border-radius: 24px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease;
        display: flex;
        flex-direction: column;
    }
    
    @media (min-width: 768px) and (orientation: landscape) {
        .welcome-modal-content {
            max-width: 700px;
            max-height: 85vh;
        }
    }
    
    .welcome-step {
        display: none;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    
    .welcome-step.active {
        display: flex;
        animation: fadeSlide 0.3s ease;
    }
    
    .welcome-step-header {
        flex-shrink: 0;
        padding: 40px 32px 24px;
    }
    
    .welcome-step-content {
        flex: 1;
        overflow-y: auto;
        padding: 0 32px;
    }
    
    .welcome-step-footer {
        flex-shrink: 0;
        padding: 24px 32px 32px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
    
    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .welcome-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
    }
    
    .welcome-icon i {
        font-size: 36px;
        color: #0d9488;
    }
    
    .welcome-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 12px;
        line-height: 1.2;
    }
    
    .welcome-subtitle {
        font-size: 18px;
        color: #64748b;
        text-align: center;
        margin-bottom: 32px;
        line-height: 1.5;
    }
    
    .welcome-option {
        padding: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        touch-action: manipulation;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .welcome-option:hover {
        border-color: #0d9488;
        background: #f0fdfa;
    }
    
    .welcome-option.selected {
        border-color: #0d9488;
        background: #f0fdfa;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }
    
    .welcome-option-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .welcome-option.selected .welcome-option-icon {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
    }
    
    .welcome-option-icon i {
        font-size: 24px;
        color: #0d9488;
    }
    
    .welcome-option.selected .welcome-option-icon i {
        color: white;
    }
    
    .welcome-option-content {
        flex: 1;
    }
    
    .welcome-option-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    
    .welcome-option-subtitle {
        font-size: 14px;
        color: #64748b;
    }
    
    .welcome-progress {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-bottom: 24px;
    }
    
    .progress-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .progress-dot.active {
        width: 24px;
        border-radius: 4px;
        background: #0d9488;
    }
    
    .welcome-actions {
        display: flex;
        gap: 12px;
    }
    
    .btn-welcome {
        flex: 1;
        padding: 16px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        touch-action: manipulation;
        min-height: 56px;
    }
    
    .btn-welcome-primary {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    
    .btn-welcome-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
    }
    
    .btn-welcome-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-welcome-secondary {
        background: white;
        color: #64748b;
        border: 2px solid #e2e8f0;
    }
    
    .btn-welcome-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    /* Original styles */
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
        background-color: #0d9488 !important;
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

    /* New Client Form Styles */
    .new-client-form {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<!-- Welcome Modal -->
<div id="welcomeModal" class="welcome-modal active">
    <div class="welcome-modal-content">
        <!-- Step 1: Welcome -->
        <div class="welcome-step active" data-step="1">
            <div class="welcome-step-header">
                <div class="welcome-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h2 class="welcome-title">Let's Book Your Session</h2>
                <p class="welcome-subtitle">We'll help you set this up in 4 quick steps</p>
                
                <div class="welcome-progress">
                    <div class="progress-dot active"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                </div>
            </div>
            
            <div class="welcome-step-footer">
                <div class="welcome-actions">
                    <button type="button" onclick="closeWelcomeModal()" class="btn-welcome btn-welcome-secondary">
                        Skip Setup
                    </button>
                    <button type="button" onclick="nextWelcomeStep()" class="btn-welcome btn-welcome-primary">
                        Get Started
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Step 2: Artist Selection -->
        <div class="welcome-step" data-step="2">
            <div class="welcome-step-header">
                <div class="welcome-icon">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <h2 class="welcome-title">Choose Your Artist</h2>
                <p class="welcome-subtitle">Who would you like to book with?</p>
                
                <div class="welcome-progress">
                    <div class="progress-dot"></div>
                    <div class="progress-dot active"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                </div>
            </div>
            
            <div class="welcome-step-content">
                <div id="artistOptions">
                    @foreach($artists as $artist)
                    <label class="welcome-option {{ $loop->first ? 'selected' : '' }}" data-value="{{ $artist->id }}">
                        <div class="welcome-option-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="welcome-option-content">
                            <div class="welcome-option-title">{{ $artist->first_name }} {{ $artist->last_name }}</div>
                            @if($artist->email)
                            <div class="welcome-option-subtitle">{{ $artist->email }}</div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            
            <div class="welcome-step-footer">
                <div class="welcome-actions">
                    <button type="button" onclick="prevWelcomeStep()" class="btn-welcome btn-welcome-secondary">
                        Back
                    </button>
                    <button type="button" onclick="nextWelcomeStep()" class="btn-welcome btn-welcome-primary">
                        Continue
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Step 3: Duration -->
        <div class="welcome-step" data-step="3">
            <div class="welcome-step-header">
                <div class="welcome-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h2 class="welcome-title">Choose Duration</h2>
                <p class="welcome-subtitle">How long should this session be?</p>
                
                <div class="welcome-progress">
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot active"></div>
                    <div class="progress-dot"></div>
                </div>
            </div>
            
            <div class="welcome-step-content">
                <div id="durationOptions">
                    <label class="welcome-option" data-value="30">
                        <div class="welcome-option-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="welcome-option-content">
                            <div class="welcome-option-title">30 minutes</div>
                            <div class="welcome-option-subtitle">Quick consultation</div>
                        </div>
                    </label>
                    
                    <label class="welcome-option selected" data-value="60">
                        <div class="welcome-option-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="welcome-option-content">
                            <div class="welcome-option-title">60 minutes</div>
                            <div class="welcome-option-subtitle">Standard session</div>
                        </div>
                    </label>
                    
                    <label class="welcome-option" data-value="90">
                        <div class="welcome-option-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="welcome-option-content">
                            <div class="welcome-option-title">90 minutes</div>
                            <div class="welcome-option-subtitle">Extended session</div>
                        </div>
                    </label>

                    <label class="welcome-option" data-value="120">
                        <div class="welcome-option-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="welcome-option-content">
                            <div class="welcome-option-title">120 minutes</div>
                            <div class="welcome-option-subtitle">Full session</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="welcome-step-footer">
                <div class="welcome-actions">
                    <button type="button" onclick="prevWelcomeStep()" class="btn-welcome btn-welcome-secondary">
                        Back
                    </button>
                    <button type="button" onclick="nextWelcomeStep()" class="btn-welcome btn-welcome-primary">
                        Continue
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Step 4: Client Selection -->
        <div class="welcome-step" data-step="4">
            <div class="welcome-step-header">
                <div class="welcome-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h2 class="welcome-title">Select Client</h2>
                <p class="welcome-subtitle">Who is this booking for?</p>
                
                <div class="welcome-progress">
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot"></div>
                    <div class="progress-dot active"></div>
                </div>
            </div>
            
            <div class="welcome-step-content">
                <!-- Client List View -->
                <div id="clientListView">
                    <div style="margin-bottom: 16px;">
                        <input type="text" id="welcomeClientSearch" placeholder="Search clients..." 
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                    </div>
                    
                    <div id="welcomeClientList" style="margin-bottom: 16px;">
                        <!-- Populated by JavaScript -->
                    </div>

                    <button type="button" onclick="showNewClientForm()" class="w-full border-2 border-dashed border-slate-300 hover:border-slate-400 text-slate-700 font-medium px-6 py-3 rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add New Client</span>
                    </button>
                </div>

                <!-- New Client Form View -->
                <div id="newClientFormView" class="hidden">
                    <div class="space-y-4">
                        <div>
                            <input type="text" id="welcomeNewClientFirstName" placeholder="First Name" 
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                            <p id="welcomeErrorFirstName" class="hidden mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <input type="text" id="welcomeNewClientLastName" placeholder="Last Name" 
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                            <p id="welcomeErrorLastName" class="hidden mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <input type="email" id="welcomeNewClientEmail" placeholder="Email Address" 
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                            <p id="welcomeErrorEmail" class="hidden mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <input type="tel" id="welcomeNewClientPhone" placeholder="Phone Number" 
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-base">
                            <p id="welcomeErrorPhone" class="hidden mt-1 text-sm text-red-600"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="welcome-step-footer">
                <div class="welcome-actions">
                    <button type="button" id="clientBackBtn" onclick="handleClientBack()" class="btn-welcome btn-welcome-secondary">
                        Back
                    </button>
                    <button type="button" id="clientActionBtn" onclick="finishWelcome()" class="btn-welcome btn-welcome-primary" disabled>
                        Start Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="split-layout bg-gray-50">
    <!-- Left Panel -->
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
                    <span class="text-sm font-medium text-gray-700">Duration</span>
                    <button type="button" onclick="openChangeModal('duration')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="sessionTypeDisplay">60 minutes</p>
            </div>
            
            <!-- Artist Selection -->
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Artist</span>
                    <button type="button" onclick="openChangeModal('artist')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="artistDisplay">{{ $artists->first()->first_name ?? 'Artist' }} {{ $artists->first()->last_name ?? '' }}</p>
            </div>
            
            <!-- Client Selection -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Client</span>
                    <button type="button" onclick="openChangeModal('client')" class="text-sm text-teal-600 hover:text-teal-700 font-medium">Change</button>
                </div>
                <p class="text-gray-900" id="clientDisplay">Select a client</p>
            </div>

            <!-- Notes Section -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <label for="bookingNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="bookingNotes" rows="3" placeholder="Add any special instructions..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"></textarea>
            </div>
        </div>
    </div>
    
    <!-- Right Panel - Calendar -->
    <div class="right-panel bg-white">
        <div class="calendar-content">
            <div class="flex items-center justify-between max-w-6xl mx-auto p-4">
                <button type="button" id="prevWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h2 class="text-xl font-semibold text-gray-900" id="currentMonth">December 2024</h2>
                <button type="button" id="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-8 gap-px bg-gray-200 border-y border-gray-200">
                    <div class="bg-white p-3"></div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Mon</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day0">5</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Tue</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day1">6</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Wed</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day2">7</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Thu</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day3">8</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Fri</div>
                        <div class="text-2xl font-semibold text-gray-900" id="day4">9</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Sat</div>
                        <div class="text-2xl font-semibold text-gray-400" id="day5">10</div>
                    </div>
                    <div class="bg-white p-3 text-center">
                        <div class="text-sm text-gray-600">Sun</div>
                        <div class="text-2xl font-semibold text-gray-400" id="day6">11</div>
                    </div>
                </div>
                
                <div id="weeklyCalendar" class="relative grid grid-cols-8 gap-px bg-gray-200 border-b border-gray-200 mb-8">
                </div>
            </div>
        </div>
        
        <div class="calendar-footer">
            <div class="max-w-6xl mx-auto flex items-center justify-end">
                <div class="flex items-center gap-4">
                    <span class="text-2xl font-bold text-gray-900">£{{ number_format($service->base_price / 100, 2) }}</span>
                    <button type="button" id="confirmBtn" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-8 py-3 rounded-lg disabled:opacity-50" disabled>
                        Confirm time
                    </button>
                </div>
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

let selectedSlot = null;
let currentWeekStart = new Date();
let clientsList = [...clients];
let currentWelcomeStep = 1;
let selectedWelcomeArtist = {{ $artists->first()->id ?? 'null' }};
let selectedWelcomeDuration = 60;
let selectedWelcomeClient = null;
let availabilityData = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeWeekView();
    renderWeeklyCalendar();
});

function initializeWeekView() {
    const today = new Date();
    currentWeekStart = new Date(today);
    const day = currentWeekStart.getDay();
    const diff = currentWeekStart.getDate() - day + (day === 0 ? -6 : 1);
    currentWeekStart.setDate(diff);
}

async function fetchAvailabilityForWeek() {
    const artistId = document.getElementById('artistId').value;
    const duration = parseInt(document.getElementById('duration').value);
    
    if (!artistId) return [];
    
    const month = currentWeekStart.getMonth() + 1;
    const year = currentWeekStart.getFullYear();
    
    try {
        const response = await fetch('{{ route("api.availability.artist") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                artist_id: artistId,
                duration: duration,
                month: month,
                year: year
            })
        });
        
        if (!response.ok) throw new Error('Failed to fetch availability');
        
        const data = await response.json();
        return data.available_days || [];
    } catch (error) {
        console.error('Error fetching availability:', error);
        return [];
    }
}

async function fetchTimeSlotsForDay(date) {
    const artistId = document.getElementById('artistId').value;
    const duration = parseInt(document.getElementById('duration').value);
    
    if (!artistId) return [];
    
    try {
        const response = await fetch('{{ route("api.availability.timeslots") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                artist_id: artistId,
                date: date,
                duration: duration
            })
        });
        
        if (!response.ok) throw new Error('Failed to fetch time slots');
        
        const data = await response.json();
        return data.time_slots || [];
    } catch (error) {
        console.error('Error fetching time slots:', error);
        return [];
    }
}

async function renderWeeklyCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    document.getElementById('currentMonth').textContent = 
        `${monthNames[currentWeekStart.getMonth()]} ${currentWeekStart.getFullYear()}`;
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(currentWeekStart);
        date.setDate(currentWeekStart.getDate() + i);
        document.getElementById(`day${i}`).textContent = date.getDate();
    }
    
    const calendar = document.getElementById('weeklyCalendar');
    calendar.innerHTML = '<div class="col-span-8 text-center py-8 text-gray-500">Loading availability...</div>';
    
    availabilityData = await fetchAvailabilityForWeek();
    
    const duration = parseInt(document.getElementById('duration').value);
    
    const timeSlots = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', 
                       '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
    
    const slotsByDay = {};
    
    const fetchPromises = [];
    for (let day = 0; day < 7; day++) {
        const date = new Date(currentWeekStart);
        date.setDate(currentWeekStart.getDate() + day);
        const dateStr = date.toISOString().split('T')[0];
        
        if (availabilityData && availabilityData.includes(dateStr)) {
            fetchPromises.push(
                fetchTimeSlotsForDay(dateStr).then(slots => {
                    slotsByDay[day] = slots;
                })
            );
        } else {
            slotsByDay[day] = [];
        }
    }
    
    await Promise.all(fetchPromises);
    
    calendar.innerHTML = '';
    
    timeSlots.forEach((time, timeIndex) => {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'bg-white p-3 text-sm text-gray-600 border-t border-gray-100';
        const hour = parseInt(time.split(':')[0]);
        const displayTime = hour < 12 ? `${hour} AM` : hour === 12 ? '12 PM' : `${hour - 12} PM`;
        timeLabel.textContent = displayTime;
        calendar.appendChild(timeLabel);
        
        for (let day = 0; day < 7; day++) {
            const slotDiv = document.createElement('div');
            slotDiv.className = 'bg-white border-t border-gray-100 relative';
            
            const daySlots = slotsByDay[day] || [];
            const matchingSlot = daySlots.find(slot => slot.start === time);
            
            if (matchingSlot) {
                const slot = document.createElement('button');
                slot.type = 'button';
                
                const durationMinutes = parseInt(matchingSlot.duration || duration);
                const baseSlotHeight = 48;
                const heightMultiplier = durationMinutes / 60;
                let totalHeight = Math.round(baseSlotHeight * heightMultiplier - 4);
                
                slot.className = 'time-slot absolute top-0 left-0 right-0 bg-[#E4F6E9] hover:bg-[#d4f0e2] text-teal-900 text-sm font-medium py-2 px-3 rounded-lg z-10 transition-all';
                slot.style.height = `${totalHeight}px`;
                slot.style.minHeight = `${totalHeight}px`;
                slot.style.maxHeight = `${totalHeight}px`;
                
                const endTime = matchingSlot.end || calculateEndTime(time, durationMinutes);
                slot.textContent = `${matchingSlot.start}-${endTime}`;
                slot.dataset.date = day;
                slot.dataset.time = matchingSlot.start;
                slot.dataset.duration = durationMinutes;
                
                const slotDate = new Date(currentWeekStart);
                slotDate.setDate(currentWeekStart.getDate() + day);
                slot.dataset.fullDate = slotDate.toISOString().split('T')[0];
                
                slot.addEventListener('click', function() {
                    selectTimeSlot(this);
                });
                
                slotDiv.appendChild(slot);
            }
            
            calendar.appendChild(slotDiv);
        }
    });
    
    const hasSlots = Object.values(slotsByDay).some(slots => slots.length > 0);
    if (!hasSlots) {
        calendar.innerHTML = '<div class="col-span-8 text-center py-12"><div class="text-gray-500 text-lg font-medium mb-2">No availability this week</div><div class="text-gray-400 text-sm">Try selecting a different week or duration</div></div>';
    }
}

function calculateEndTime(startTime, durationMinutes) {
    const [hours, minutes] = startTime.split(':').map(Number);
    const totalMinutes = hours * 60 + minutes + durationMinutes;
    const endHours = Math.floor(totalMinutes / 60);
    const endMinutes = totalMinutes % 60;
    return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
}

function selectTimeSlot(element) {
    const currentHeight = element.offsetHeight;
    element.style.transition = 'none';
    
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected', 'bg-teal-600', 'text-white');
        slot.classList.add('bg-[#E4F6E9]', 'text-teal-900');
    });
    
    element.classList.add('selected', 'bg-teal-600', 'text-white');
    element.classList.remove('bg-[#E4F6E9]', 'text-teal-900');
    
    element.style.setProperty('height', `${currentHeight}px`, 'important');
    element.style.setProperty('min-height', `${currentHeight}px`, 'important');
    element.style.setProperty('max-height', `${currentHeight}px`, 'important');
    
    setTimeout(() => {
        element.style.transition = '';
    }, 50);
    
    selectedSlot = {
        date: element.dataset.date,
        time: element.dataset.time,
        fullDate: element.dataset.fullDate
    };
    
    document.getElementById('selectedTime').value = element.dataset.time;
    document.getElementById('selectedDate').value = element.dataset.fullDate;
    
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
    selectedSlot = null;
    document.getElementById('selectedTime').value = '';
    document.getElementById('selectedDate').value = '';
    checkFormValidity();
    renderWeeklyCalendar();
});

document.getElementById('nextWeek').addEventListener('click', function() {
    currentWeekStart.setDate(currentWeekStart.getDate() + 7);
    selectedSlot = null;
    document.getElementById('selectedTime').value = '';
    document.getElementById('selectedDate').value = '';
    checkFormValidity();
    renderWeeklyCalendar();
});

document.getElementById('confirmBtn').addEventListener('click', function() {
    document.getElementById('bookingForm').submit();
});

// Welcome Modal Functions
function nextWelcomeStep() {
    const currentStep = document.querySelector(`.welcome-step[data-step="${currentWelcomeStep}"]`);
    currentStep.classList.remove('active');
    
    currentWelcomeStep++;
    
    const nextStep = document.querySelector(`.welcome-step[data-step="${currentWelcomeStep}"]`);
    nextStep.classList.add('active');
    
    if (currentWelcomeStep === 4) {
        populateWelcomeClientList();
    }
}

function prevWelcomeStep() {
    const currentStep = document.querySelector(`.welcome-step[data-step="${currentWelcomeStep}"]`);
    currentStep.classList.remove('active');
    
    currentWelcomeStep--;
    
    const prevStep = document.querySelector(`.welcome-step[data-step="${currentWelcomeStep}"]`);
    prevStep.classList.add('active');
}

function closeWelcomeModal() {
    document.getElementById('welcomeModal').classList.remove('active');
}

function finishWelcome() {
    document.getElementById('artistId').value = selectedWelcomeArtist;
    const artist = artists.find(a => a.id == selectedWelcomeArtist);
    if (artist) {
        document.getElementById('artistDisplay').textContent = `${artist.first_name} ${artist.last_name}`;
    }
    
    document.getElementById('duration').value = selectedWelcomeDuration;
    document.getElementById('sessionTypeDisplay').textContent = `${selectedWelcomeDuration} minutes`;
    
    if (selectedWelcomeClient) {
        document.getElementById('clientId').value = selectedWelcomeClient.id;
        const name = [selectedWelcomeClient.first_name, selectedWelcomeClient.last_name].filter(Boolean).join(' ');
        document.getElementById('clientDisplay').textContent = name;
    }
    
    closeWelcomeModal();
    renderWeeklyCalendar();
    checkFormValidity();
}

// Artist selection in welcome modal
document.querySelectorAll('#artistOptions .welcome-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('#artistOptions .welcome-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        this.classList.add('selected');
        selectedWelcomeArtist = parseInt(this.dataset.value);
    });
});

// Duration selection in welcome modal
document.querySelectorAll('#durationOptions .welcome-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('#durationOptions .welcome-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        this.classList.add('selected');
        selectedWelcomeDuration = parseInt(this.dataset.value);
    });
});

// Populate welcome client list
function populateWelcomeClientList() {
    const list = document.getElementById('welcomeClientList');
    list.innerHTML = clientsList.map(client => {
        const name = [client.first_name, client.last_name].filter(Boolean).join(' ') || 'Unnamed Client';
        return `
            <label class="welcome-option" data-client-id="${client.id}">
                <div class="welcome-option-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="welcome-option-content">
                    <div class="welcome-option-title">${name}</div>
                    ${client.email ? `<div class="welcome-option-subtitle">${client.email}</div>` : ''}
                </div>
            </label>
        `;
    }).join('');
    
    document.querySelectorAll('#welcomeClientList .welcome-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('#welcomeClientList .welcome-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            const clientId = parseInt(this.dataset.clientId);
            selectedWelcomeClient = clientsList.find(c => c.id === clientId);
            document.getElementById('clientActionBtn').disabled = false;
        });
    });
}

// Search functionality in welcome modal
document.getElementById('welcomeClientSearch')?.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('#welcomeClientList .welcome-option').forEach(option => {
        const text = option.textContent.toLowerCase();
        option.style.display = text.includes(query) ? 'flex' : 'none';
    });
});

// Show new client form - replaces the entire client list view
function showNewClientForm() {
    document.getElementById('clientListView').classList.add('hidden');
    document.getElementById('newClientFormView').classList.remove('hidden');
    
    // Update modal title and subtitle
    const modal = document.querySelector('.welcome-step[data-step="4"]');
    modal.querySelector('.welcome-title').textContent = 'Add New Client';
    modal.querySelector('.welcome-subtitle').textContent = 'Enter the client details';
    
    // Update action buttons
    document.getElementById('clientBackBtn').textContent = 'Cancel';
    document.getElementById('clientBackBtn').onclick = hideNewClientForm;
    
    document.getElementById('clientActionBtn').textContent = 'Save Client';
    document.getElementById('clientActionBtn').disabled = false;
    document.getElementById('clientActionBtn').onclick = saveWelcomeNewClient;
    
    // Clear form
    ['welcomeNewClientFirstName', 'welcomeNewClientLastName', 'welcomeNewClientEmail', 'welcomeNewClientPhone'].forEach(id => {
        document.getElementById(id).value = '';
        document.getElementById(id).classList.remove('border-red-500');
    });
    ['welcomeErrorFirstName', 'welcomeErrorLastName', 'welcomeErrorEmail', 'welcomeErrorPhone'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.textContent = '';
    });
}

// Hide new client form - go back to client list
function hideNewClientForm() {
    document.getElementById('newClientFormView').classList.add('hidden');
    document.getElementById('clientListView').classList.remove('hidden');
    
    // Restore modal title and subtitle
    const modal = document.querySelector('.welcome-step[data-step="4"]');
    modal.querySelector('.welcome-title').textContent = 'Select Client';
    modal.querySelector('.welcome-subtitle').textContent = 'Who is this booking for?';
    
    // Restore action buttons
    document.getElementById('clientBackBtn').textContent = 'Back';
    document.getElementById('clientBackBtn').onclick = handleClientBack;
    
    const isInitialSetup = document.getElementById('clientActionBtn').textContent.includes('Start Booking');
    document.getElementById('clientActionBtn').textContent = isInitialSetup ? 'Start Booking' : 'Apply';
    document.getElementById('clientActionBtn').disabled = !selectedWelcomeClient;
    document.getElementById('clientActionBtn').onclick = isInitialSetup ? finishWelcome : applyClientChange;
}

// Handle back button in client step
function handleClientBack() {
    // Check if we're in new client form first
    if (!document.getElementById('newClientFormView').classList.contains('hidden')) {
        hideNewClientForm();
    } else if (document.querySelector('.welcome-step[data-step="4"] .welcome-actions button:first-child').textContent === 'Cancel') {
        // We're in change mode
        closeWelcomeModal();
    } else {
        // We're in initial setup
        prevWelcomeStep();
    }
}

// Save new client from welcome modal
async function saveWelcomeNewClient() {
    ['welcomeErrorFirstName', 'welcomeErrorLastName', 'welcomeErrorEmail', 'welcomeErrorPhone'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.textContent = '';
    });
    ['welcomeNewClientFirstName', 'welcomeNewClientLastName', 'welcomeNewClientEmail', 'welcomeNewClientPhone'].forEach(id => {
        document.getElementById(id).classList.remove('border-red-500');
    });
    
    const firstName = document.getElementById('welcomeNewClientFirstName').value.trim();
    const lastName = document.getElementById('welcomeNewClientLastName').value.trim();
    const email = document.getElementById('welcomeNewClientEmail').value.trim();
    const phone = document.getElementById('welcomeNewClientPhone').value.trim();
    
    let hasError = false;
    
    if (!firstName && !lastName) {
        document.getElementById('welcomeNewClientFirstName').classList.add('border-red-500');
        document.getElementById('welcomeNewClientLastName').classList.add('border-red-500');
        document.getElementById('welcomeErrorFirstName').textContent = 'First or last name is required';
        document.getElementById('welcomeErrorFirstName').classList.remove('hidden');
        hasError = true;
    }
    
    if (!email && !phone) {
        document.getElementById('welcomeNewClientEmail').classList.add('border-red-500');
        document.getElementById('welcomeNewClientPhone').classList.add('border-red-500');
        document.getElementById('welcomeErrorEmail').textContent = 'Email or phone is required';
        document.getElementById('welcomeErrorEmail').classList.remove('hidden');
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
        
        if (!response.ok) throw new Error('Failed to create client');
        
        const data = await response.json();
        const newClient = data.client || data;
        
        clientsList.push(newClient);
        selectedWelcomeClient = newClient;
        
        // Hide form and show success message temporarily
        hideNewClientForm();
        
        // Repopulate list and select the new client
        populateWelcomeClientList();
        
        const clientOption = document.querySelector(`#welcomeClientList .welcome-option[data-client-id="${newClient.id}"]`);
        if (clientOption) {
            clientOption.classList.add('selected');
            clientOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Enable the action button
        const actionBtn = document.getElementById('clientActionBtn');
        actionBtn.disabled = false;
        
    } catch (error) {
        console.error(error);
        document.getElementById('welcomeNewClientFirstName').classList.add('border-red-500');
        document.getElementById('welcomeErrorFirstName').textContent = 'Failed to create client. Please try again.';
        document.getElementById('welcomeErrorFirstName').classList.remove('hidden');
    }
}

// Open change modal - reuses the welcome modal steps
function openChangeModal(type) {
    const modal = document.getElementById('welcomeModal');
    modal.classList.add('active');
    
    // Hide all steps first
    document.querySelectorAll('.welcome-step').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show the appropriate step
    if (type === 'artist') {
        currentWelcomeStep = 2;
        document.querySelector('.welcome-step[data-step="2"]').classList.add('active');
        
        // Update the modal to act as a change modal
        const actions = document.querySelector('.welcome-step[data-step="2"] .welcome-actions');
        actions.innerHTML = `
            <button type="button" onclick="closeWelcomeModal()" class="btn-welcome btn-welcome-secondary">
                Cancel
            </button>
            <button type="button" onclick="applyArtistChange()" class="btn-welcome btn-welcome-primary">
                Apply
            </button>
        `;
        
        // Pre-select current artist
        const currentArtistId = parseInt(document.getElementById('artistId').value);
        document.querySelectorAll('#artistOptions .welcome-option').forEach(opt => {
            opt.classList.remove('selected');
            if (parseInt(opt.dataset.value) === currentArtistId) {
                opt.classList.add('selected');
                selectedWelcomeArtist = currentArtistId;
            }
        });
        
    } else if (type === 'duration') {
        currentWelcomeStep = 3;
        document.querySelector('.welcome-step[data-step="3"]').classList.add('active');
        
        const actions = document.querySelector('.welcome-step[data-step="3"] .welcome-actions');
        actions.innerHTML = `
            <button type="button" onclick="closeWelcomeModal()" class="btn-welcome btn-welcome-secondary">
                Cancel
            </button>
            <button type="button" onclick="applyDurationChange()" class="btn-welcome btn-welcome-primary">
                Apply
            </button>
        `;
        
        // Pre-select current duration
        const currentDuration = parseInt(document.getElementById('duration').value);
        document.querySelectorAll('#durationOptions .welcome-option').forEach(opt => {
            opt.classList.remove('selected');
            if (parseInt(opt.dataset.value) === currentDuration) {
                opt.classList.add('selected');
                selectedWelcomeDuration = currentDuration;
            }
        });
        
    } else if (type === 'client') {
        currentWelcomeStep = 4;
        document.querySelector('.welcome-step[data-step="4"]').classList.add('active');
        
        // Make sure we're showing the client list, not the new client form
        document.getElementById('newClientFormView').classList.add('hidden');
        document.getElementById('clientListView').classList.remove('hidden');
        
        // Reset titles
        const modal = document.querySelector('.welcome-step[data-step="4"]');
        modal.querySelector('.welcome-title').textContent = 'Select Client';
        modal.querySelector('.welcome-subtitle').textContent = 'Who is this booking for?';
        
        populateWelcomeClientList();
        
        // Update action buttons for change mode
        document.getElementById('clientBackBtn').textContent = 'Cancel';
        document.getElementById('clientBackBtn').onclick = closeWelcomeModal;
        
        document.getElementById('clientActionBtn').textContent = 'Apply';
        document.getElementById('clientActionBtn').onclick = applyClientChange;
        
        // Pre-select current client if one is selected
        const currentClientId = parseInt(document.getElementById('clientId').value);
        if (currentClientId) {
            document.querySelectorAll('#welcomeClientList .welcome-option').forEach(opt => {
                opt.classList.remove('selected');
                if (parseInt(opt.dataset.clientId) === currentClientId) {
                    opt.classList.add('selected');
                    selectedWelcomeClient = clientsList.find(c => c.id === currentClientId);
                    document.getElementById('clientActionBtn').disabled = false;
                }
            });
        } else {
            document.getElementById('clientActionBtn').disabled = true;
        }
    }
}

function applyArtistChange() {
    document.getElementById('artistId').value = selectedWelcomeArtist;
    const artist = artists.find(a => a.id == selectedWelcomeArtist);
    if (artist) {
        document.getElementById('artistDisplay').textContent = `${artist.first_name} ${artist.last_name}`;
    }
    
    closeWelcomeModal();
    
    selectedSlot = null;
    document.getElementById('selectedTime').value = '';
    document.getElementById('selectedDate').value = '';
    checkFormValidity();
    renderWeeklyCalendar();
}

function applyDurationChange() {
    document.getElementById('duration').value = selectedWelcomeDuration;
    document.getElementById('sessionTypeDisplay').textContent = `${selectedWelcomeDuration} minutes`;
    
    closeWelcomeModal();
    
    selectedSlot = null;
    document.getElementById('selectedTime').value = '';
    document.getElementById('selectedDate').value = '';
    checkFormValidity();
    renderWeeklyCalendar();
}

function applyClientChange() {
    if (selectedWelcomeClient) {
        document.getElementById('clientId').value = selectedWelcomeClient.id;
        const name = [selectedWelcomeClient.first_name, selectedWelcomeClient.last_name].filter(Boolean).join(' ');
        document.getElementById('clientDisplay').textContent = name;
    }
    
    // Reset views before closing
    if (!document.getElementById('newClientFormView').classList.contains('hidden')) {
        hideNewClientForm();
    }
    
    closeWelcomeModal();
    checkFormValidity();
}
</script>
@endpush