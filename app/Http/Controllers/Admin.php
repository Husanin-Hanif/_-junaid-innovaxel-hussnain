<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\movie;
use App\Models\Showtime;
use App\Models\User;

class Admin extends Controller
{
     public function createmovie( Request $request){

        $request->validate([
            'title'=> 'required|string|max:200',
            'description' =>'nullable|string',
            'genre'=>'required|string|max:100',
        ]);
        $movie=movie::create($request->all());


       if($movie){
        return \response()->json(['message'=>'Movie Create Successfully','movie'=> $movie]);
       }
       return response()->json(['message'=>'Movie Not Create Successfully']);

    }


    public function getmovie($id){
     $edit=movie::find($id);
     if(!$edit){
        return \response()->json(['message'=>'Not get movie']);
     }

     return \response()->json(['message'=>'Get Movie','eidt'=> $edit]);
    }
    public function updatemovie(Request $request, $id){

        try{
            $editmovie=movie::find($id);

        if(!$editmovie){
            return \response()->json(['message'=>'movie not update']);

        }
        //   $request->validate([
        //     'title'=> 'required|string|max:200',
        //     'description' =>'nullable|string',
        //     'genre'=>'required|string|max:100',
        // ]);
        $editmovie->update($request->all());
        return \response()->json(['message'=>'movie successfully update','editmovie' => $editmovie]);

        }
        catch(Exception $e){
            \Log::error('Error during edit  ' . $e->getMessage());
            return \response()->json(['message'=>'error is ',$e->getmessage()]);
        }


    }
    public function deletemovie($id){
        $deletemovie=movie::find($id);
        if($deletemovie){
            $deletemovie->delete();
            return \response()->json(['message'=>'Movie Delete Successfully']);
        }
        return \response()->json(['message'=>'Movie not Delete ']);


    }

    public function index(){
        $showtime=Showtime::with('movie')->get();
        return \response()->json($showtime);
    }

    public function show($id){
        $showtime=Showtime::with('movie')->find($id);
        return \response()->json(['message'=>'get show detail successfully','showtime'=>$showtime]);
    }



    public function showcreate(Request $request)
{
    try {

        $validated = $request->validate([
            'movie_id' => 'required|exists:movie,id',
            'date' => 'required|date',
            'firsttime' => 'required|date_format:h:i A',
            'secondtime' => 'required|date_format:h:i A',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
        ]);


        $validated['firsttime'] = \Carbon\Carbon::createFromFormat('h:i A', $validated['firsttime'])->format('H:i:s');
        $validated['secondtime'] = \Carbon\Carbon::createFromFormat('h:i A', $validated['secondtime'])->format('H:i:s');


        $showtime = Showtime::create($validated);

        if (!$showtime) {
            return response()->json(['message' => 'Showtime not created'], 500);
        }

        return response()->json(['message' => 'Showtime created successfully', 'showtime' => $showtime]);

    } catch (\Exception $e) {

        \Log::error('Error during showtime creation: ' . $e->getMessage());


        return response()->json([
            'message' => 'An error occurred during showtime creation.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function updateshowtime(Request $request, $id)
{
    try {
        $showtime = Showtime::find($id);
        if (!$showtime) {
            return response()->json(['message' => 'Showtime not found'], 404);
        }
        $validated = $request->validate([
            'movie_id' => 'required|exists:movie,id',
            'date' => 'required|date',
            'firsttime' => 'required|date_format:h:i A',
            'secondtime' => 'required|date_format:h:i A',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
        ]);
        $validated['firsttime'] = \Carbon\Carbon::createFromFormat('h:i A', $validated['firsttime'])->format('H:i:s');
        $validated['secondtime'] = \Carbon\Carbon::createFromFormat('h:i A', $validated['secondtime'])->format('H:i:s');

        $showtime->update($validated);

        return response()->json(['message' => 'Showtime updated successfully', 'showtime' => $showtime]);
    }  catch (\Exception $e) {
        \Log::error('Error during Showtime update:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'message' => 'An error occurred during showtime update.',
            'error' => $e->getMessage(),
        ]);
    }
}


    public function deleteshowtime($id){
        $showtime=Showtime::find($id);
        if($showtime){
            $showtime->delete();
            return \response()->json(['message'=>'Show time delete Successfully']);
        }
        return response()->json(['message'=>'Show time not delete']);

    }
    public function promoteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);
        return response()->json([
            'message' => 'User role updated successfully', 'user' => $user,
            'new_role' => $newRole,
        ]);
    }
    public function getreport() {

        $reservations = Showtime::with('reservation')->get();
        $revenue = $reservations->sum(function ($showtime) {
               $total =$showtime->reservation ? $showtime->reservation->count() : 0;
            return $total * $showtime->price;
        });
        return response()->json([
            'Reservation' => $reservations,
            'Revenue' => $revenue,
        ]);
    }



}
