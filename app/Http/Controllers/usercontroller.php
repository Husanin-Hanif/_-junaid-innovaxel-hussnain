<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\movie;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class usercontroller extends Controller
{
    public function listOfMovieShowtime($date)
{
    $movie = DB::table('movie')
        ->join('showtime', 'movie.id', '=', 'showtime.movie_id')
        ->where('showtime.date', $date)
        ->select(
            'movie.id as movie_id',
            'movie.title as Movie_Title',
            'movie.description as Description',
            'showtime.id as ShowID',
            'showtime.firsttime as Time1',
            'showtime.secondtime as Time2',
            'showtime.capacity as Capacity',
            'showtime.price as Price'
        )
        ->get();

    return response()->json(['movies' => $movie]);
}

public function reserveSeats(Request $request){
    try{
        $request->validate([
            'showtime_id' =>'required|exists:showtime,id',
            'seats' =>'required|array|min:1',
            'seats.*' => 'string',
        ]);
        // $showtime=Showtime::find($request->showtime_id);

        $lockedSeats = Cache::get("lock:seats:{$request->showtime_id}", []);
        if (count(array_intersect($lockedSeats, $request->seats)) > 0) {
            return response()->json([
                'message' => 'Some seats are temporarily locked. Please try again.',
            ]);
        }
           Cache::put("lock:seats:{$request->showtime_id}", array_merge($lockedSeats, $request->seats), now()->addMinutes(1));
        $reservation=reservation::create([
            'user_id' => Auth::id(),
            'showtime_id' => $request->showtime_id,
            'seats' => $request->seats,
        ]);
        return \response()->json(['Seats create Succssfully','reservation'=>$reservation]);
    }
    catch (\Exception $e) {

        Log::error("Error reserving seats: " . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while reserving seats.',
            'error' => $e->getMessage(),
        ], 500);
    }

}



public function listreservation()
{
    $userid = Auth::id();

    $reservations = DB::table('reservation')
        ->join('showtime', 'reservation.showtime_id', '=', 'showtime.id')
        ->join('movie', 'showtime.movie_id', '=', 'movie.id')
        ->where('reservation.user_id', $userid)
        ->where('showtime.firsttime', '>=', now()->format('h:i:s'))
         ->where('showtime.secondtime','<=',now()->format('h:i:s'))
        ->orderBy('showtime.firsttime', 'asc')
        ->select(
            'reservation.id as reservation_id',
            'reservation.seats',
            'showtime.firsttime',
            'showtime.secondtime',
            'movie.title as movie_title',
            'movie.description as movie_description'
        )
        ->get();

    if ($reservations->isEmpty()) {
        return response()->json(['message' => 'No reservation Found']);
    }

    return response()->json([
        'message' => 'Reservation found',
        'reservations' => $reservations
    ]);
}
public function cancelReservation($id)
{
    $reservation = reservation::find($id);

    if (!$reservation) {
        return response()->json(['message' => 'Reservation not found'], 404);
    }

    $reservation->delete();
    return response()->json(['message' => 'Reservation canceled successfully']);
}


}
