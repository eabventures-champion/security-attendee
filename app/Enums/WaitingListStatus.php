<?php
namespace App\Enums;

enum WaitingListStatus: string
{
    case Waiting = 'waiting';
    case Notified = 'notified';
    case Registered = 'registered';
    case Expired = 'expired';
}