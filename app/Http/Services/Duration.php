<?php
namespace App\Http\Services;

use Carbon\Carbon;

class Duration
{
    protected $start;
    protected $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function inTime(): string
    {
        return gmdate("H:i:s", $this->inSecond());
    }

    public function inSecond(): int
    {
        return $this->start->diffInSeconds($this->end);
    }

    public function inHour(): int
    {
        return $this->start->diffInHours($this->end);
    }

    public function inMinute(): int
    {
        return $this->start->diffInMinutes($this->end);
    }
}
