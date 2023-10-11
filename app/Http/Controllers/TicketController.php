<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\User;
use App\Notifications\TicketUpdateNotification;
use GuzzleHttp\Psr7\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $tickets =$user->isAdmin ? Ticket::latest()->get() : $user->ticket;
        return view('ticket.index',compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ticket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);
        
        $this->image($request,$ticket);
        return redirect()->route('ticket.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return view('ticket.view',compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        return view('ticket.edit',compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // dd($request->except('attachment'));
        $ticket->update($request->except('attachment'));
        if ($request->has('status')) {
            // $user = User::find($ticket->user_id);
            $ticket->user->notify(new TicketUpdateNotification($ticket));

            // return (new TicketUpdateNotification($ticket))->toMail($user);
        }
        if ($request->file('attachment')) {
            $currentAttachment = $ticket->attachment;
            if ($currentAttachment) {
                $currentAttachmentPath = storage_path("app/public/attachment/{$currentAttachment}");
                if (file_exists($currentAttachmentPath)) {
                    unlink($currentAttachmentPath);
                }
            }
        }
        $this->image($request,$ticket);
        
        return redirect()->route('ticket.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('ticket.index');
    }

    protected function image($request,$ticket)
    {
        if ($request->hasFile('attachment')) {
            $ext = $request->file('attachment')->getClientOriginalExtension();
            $filename = Str::random(25).'.'.$ext;
            $path = "public/attachment";
            $ticket->update(['attachment' => $filename]);
            $request->file('attachment')->storeAs($path, $filename);
        }
    }
}
