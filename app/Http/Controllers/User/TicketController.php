<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Ticket\ReplyTicketRequest;
use App\Http\Requests\User\Ticket\StoreTicketRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Ticket;
use App\Services\User\TicketService;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $service)
    {}


    public function index()
    {
        $result = $this->service->index(Auth::id());
        return $this->successResponse($result);
    }


    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $result = $this->service->show($ticket);
        return $this->successResponse(new TicketResource($result));
    }


    public function store(StoreTicketRequest $request)
    {
        $this->service->store($request->toDTO(Auth::id()));
        return $this->createdResponse();
    }


    public function reply(ReplyTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('reply', $ticket);
        $this->service->reply($request->toDTO(), $ticket);
        return $this->createdResponse();
    }
}
