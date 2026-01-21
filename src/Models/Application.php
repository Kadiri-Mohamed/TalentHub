<?php

namespace App\Models;
use App\Models\Offer;
use App\Models\Candidate;
class Application
{
    private Offer $offer;
    private Candidate $candidate;
    private string $status;
    private string $date;

    public function __construct(Offer $offer, Candidate $candidate, string $status, string $date)
    {
        $this->offer = $offer;
        $this->candidate = $candidate;
        $this->status = $status;
        $this->date = $date;
    }

    /**
     * @return Offer
     */

}