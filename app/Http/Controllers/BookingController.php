<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'booking_start_date' => 'required|date',
            'booking_end_date' => 'required|date|after_or_equal:booking_start_date',
            'booking_type' => 'required|in:full_day,half_day,custom',
            'booking_slot' => 'nullable|required_if:booking_type,half_day|in:first_half,second_half',
            'booking_from' => 'nullable|required_if:booking_type,custom|date_format:H:i',
            'booking_to' => 'nullable|required_if:booking_type,custom|date_format:H:i|after:booking_from',
        ]);

        $fromTime = $request->booking_from ? Carbon::parse($request->booking_from) : null;
        $toTime = $request->booking_to ? Carbon::parse($request->booking_to) : null;

        $overlap = Booking::where(function ($query) use ($request) {
                // Date range overlaps
                $query->whereDate('booking_start_date', '<=', $request->booking_end_date)
                      ->whereDate('booking_end_date', '>=', $request->booking_start_date);
            })
            ->where(function ($query) use ($request, $fromTime, $toTime) {
                if ($request->booking_type === 'full_day') {
                    $query->whereNotNull('id'); // any booking is a conflict
                }

                elseif ($request->booking_type === 'half_day') {
                    $query->where(function ($q) use ($request) {
                        $q->where('booking_type', 'full_day')
                          ->orWhere(function ($sq) use ($request) {
                              $sq->where('booking_type', 'half_day')
                                 ->where('booking_slot', $request->booking_slot);
                          })
                          ->orWhere(function ($sq) use ($request) {
                              if ($request->booking_slot === 'first_half') {
                                  $sq->where('booking_type', 'custom')
                                     ->whereTime('booking_from', '<', '12:00:00');
                              } else {
                                  $sq->where('booking_type', 'custom')
                                     ->whereTime('booking_to', '>', '12:00:00');
                              }
                          });
                    });
                }

                elseif ($request->booking_type === 'custom') {
                    $query->where(function ($q) use ($fromTime, $toTime) {
                        $q->where('booking_type', 'full_day')
                          ->orWhere(function ($sq) use ($fromTime, $toTime) {
                              $sq->where('booking_type', 'half_day')
                                 ->where(function ($ssq) use ($fromTime, $toTime) {
                                     $ssq->where(function ($s1) use ($fromTime) {
                                         $s1->where('booking_slot', 'first_half')
                                            ->whereTime('booking_from', '<', '12:00:00');
                                     })
                                     ->orWhere(function ($s2) use ($toTime) {
                                         $s2->where('booking_slot', 'second_half')
                                            ->whereTime('booking_to', '>', '12:00:00');
                                     });
                                 });
                          })
                          ->orWhere(function ($sq) use ($fromTime, $toTime) {
                              $sq->where('booking_type', 'custom')
                                 ->where(function ($overlapQ) use ($fromTime, $toTime) {
                                     $overlapQ->where(function ($q1) use ($fromTime, $toTime) {
                                         $q1->whereTime('booking_from', '<', $toTime)
                                            ->whereTime('booking_to', '>', $fromTime);
                                     });
                                 });
                          });
                    });
                }
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'overlap' => 'The selected booking overlaps with an existing booking.',
            ])->withInput();
        }

        Booking::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'booking_start_date' => $request->booking_start_date,
            'booking_end_date' => $request->booking_end_date,
            'booking_type' => $request->booking_type,
            'booking_slot' => $request->booking_slot,
            'booking_from' => $request->booking_from,
            'booking_to' => $request->booking_to,
        ]);

        return redirect()->route('bookings.create')->with('success', 'Booking created successfully!');
    }
}
