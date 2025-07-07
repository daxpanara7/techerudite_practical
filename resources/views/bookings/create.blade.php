<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">New Booking</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <div class="mb-4">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label>Customer Email</label>
                <input type="email" name="customer_email" class="w-full border rounded p-2" required>
            </div>

            <!-- NEW: Date Range -->
            <div class="mb-4">
                <label>Booking Start Date</label>
                <input type="date" name="booking_start_date" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label>Booking End Date</label>
                <input type="date" name="booking_end_date" class="w-full border rounded p-2" required>
            </div>


            <div class="mb-4">
                <label>Booking Type</label>
                <select name="booking_type" id="booking_type" class="w-full border rounded p-2" required>
                    <option value="">Select Type</option>
                    <option value="full_day">Full Day</option>
                    <option value="half_day">Half Day</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <div class="mb-4" id="booking_slot_group" style="display:none;">
                <label>Booking Slot</label>
                <select name="booking_slot" id="booking_slot" class="w-full border rounded p-2">
                    <option value="">Select Slot</option>
                    <option value="first_half">First Half</option>
                    <option value="second_half">Second Half</option>
                </select>
            </div>

            <div class="mb-4" id="booking_time_group" style="display:none;">
                <label>From Time</label>
                <input type="time" name="booking_from" class="w-full border rounded p-2"><br><br>
                <label>To Time</label>
                <input type="time" name="booking_to" class="w-full border rounded p-2">
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit Booking
            </button>
        </form>
    </div>

    <script>
        const bookingType = document.getElementById('booking_type');
        const slotGroup = document.getElementById('booking_slot_group');
        const timeGroup = document.getElementById('booking_time_group');

        bookingType.addEventListener('change', function () {
            const type = this.value;
            slotGroup.style.display = type === 'half_day' ? 'block' : 'none';
            timeGroup.style.display = type === 'custom' ? 'block' : 'none';
        });
    </script>
</x-app-layout>
