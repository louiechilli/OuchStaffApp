@extends('layouts.app')

@section('title', 'Screen Locked')

@push('styles')
<style>
    .pin-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #6366f1; /* indigo-500 */
        transition: all 0.2s;
    }
    .pin-dot.filled {
        background: #6366f1;
    }
    .shake {
        animation: shake 0.5s;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
        20%, 40%, 60%, 80% { transform: translateX(8px); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm w-full max-w-md p-8 lock-container text-center">
        <div class="text-5xl mb-4">🔒</div>
        <h1 class="text-2xl font-semibold text-slate-800 mb-1">Screen Locked</h1>
        <p class="text-slate-500 mb-8">Enter your 4-digit PIN to unlock</p>

        <div class="flex justify-center gap-4 mb-8">
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
            <div class="pin-dot"></div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6 max-w-xs mx-auto">
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('1')">1</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('2')">2</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('3')">3</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('4')">4</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('5')">5</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('6')">6</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('7')">7</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('8')">8</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('9')">9</button>
            <button class="pin-button bg-red-100 text-red-600 rounded-full py-6 text-sm font-medium hover:bg-red-200" onclick="clearPin()">Clear</button>
            <button class="pin-button bg-gray-100 rounded-full py-6 text-xl font-medium hover:bg-gray-200" onclick="addDigit('0')">0</button>
            <button class="pin-button bg-red-100 text-red-600 rounded-full py-6 text-xl font-medium hover:bg-red-200" onclick="deleteDigit()">⌫</button>
        </div>

        <div id="errorMessage" class="text-red-500 text-sm min-h-[20px]"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pin = '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function updateDisplay() {
        document.querySelectorAll('.pin-dot').forEach((dot, i) => {
            dot.classList.toggle('filled', i < pin.length);
        });
    }

    function addDigit(digit) {
        if (pin.length < 4) {
            pin += digit;
            updateDisplay();
            if (pin.length === 4) {
                verifyPin();
            }
        }
    }

    function deleteDigit() {
        pin = pin.slice(0, -1);
        updateDisplay();
    }

    function clearPin() {
        pin = '';
        updateDisplay();
        showError('');
    }

    async function verifyPin() {
        try {
            const res = await fetch("{{ route('unlock') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ pin })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = "{{ route('dashboard') }}";
            } else {
                showError(data.message || 'Invalid PIN');
                shake();
                clearPin();
            }
        } catch (e) {
            showError('An error occurred. Please try again.');
            clearPin();
        }
    }

    function showError(msg) {
        document.getElementById('errorMessage').textContent = msg;
    }

    function shake() {
        const box = document.querySelector('.lock-container');
        box.classList.add('shake');
        setTimeout(() => box.classList.remove('shake'), 500);
    }
</script>
@endpush