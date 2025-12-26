<?php

namespace App\Enums;

enum TicketStatusEnum: int
{
    case OPEN = 1;
    case PENDING = 5;
    case CLOSED = 10;
}
