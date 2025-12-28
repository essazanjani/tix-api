<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticket\ChangeStatusRequest;
use App\Http\Requests\Admin\Ticket\ReplyTicketRequest;
use App\Http\Resources\Admin\Ticket\TicketCollection;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Ticket;
use App\Services\Admin\TicketService;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $service)
    {}


    public function index()
    {
        $result = $this->service->index();
        return $this->successResponse(new TicketCollection($result));
    }


    public function show(Ticket $ticket)
    {
        $result = $this->service->show($ticket);
        return $this->successResponse(new TicketResource($result));
    }


    public function reply(ReplyTicketRequest $request, Ticket $ticket)
    {
        $this->service->reply($request->toDTO(Auth::id()), $ticket);
        return $this->createdResponse();
    }


    public function changeStatus(ChangeStatusRequest $request, Ticket $ticket)
    {
        $this->service->changeStatus($request->toDTO(), $ticket);
        return $this->successResponse();
    }
}
