📘 Project Summary
A Laravel 12-based booking system with secure user authentication, dynamic booking form behavior, and optimized booking conflict detection for large-scale performance (1M+ bookings).

✅ Features Implemented
🔐 Authentication
User Registration with:

First Name, Last Name, Email, Password

Email verification required before login

Duplicate emails restricted

User Login after email verification

Form validation on all auth inputs

📄 Booking Form (Post-login)
Fields:

Customer Name

Customer Email

Booking Start & End Date (supports multi-day)

Booking Type: Full Day / Half Day / Custom

Conditional: Booking Slot (for Half Day)

Conditional: Time From / To (for Custom)

Dynamic field visibility based on booking type using JavaScript

Validation for required fields and formats

🚫 Overlap Restriction Logic
Accurate logic to prevent duplicate/conflicting bookings:

Scenario	Restriction Enforced
Full Day booked	❌ No other bookings on same day
Half Day booked (First/Second Half)	❌ No Full Day or overlapping Custom bookings
Custom Time (e.g., 10–11 AM)	❌ No Full Day, Half Day (First Half), or overlapping Custom

Supports single-day and multi-day bookings with full validation and collision detection.

⚡ Performance Consideration
Booking system tested to handle:

1,000,000+ records using Seeder

10,000+ bookings per day

Booking conflict check uses:

Optimized date range queries

Indexed columns: booking_start_date, booking_end_date, booking_type, booking_slot

Efficient overlap logic scoped to only relevant date ranges

🧪 Testing Done
Manual test scenarios covered:

Full Day vs. All Types

Half Day vs. Full/Custom

Custom time overlaps

Multi-day booking logic

Seeder created to insert 1 million fake bookings

Overlap checks remain fast and accurate even with 1M+ records

🗂 Tech Stack
Laravel 12

PHP 8.2+

MySQL

Tailwind CSS (optional UI)

Carbon for date/time handling

