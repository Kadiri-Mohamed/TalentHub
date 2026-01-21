<?php

namespace App\Models;
use App\Models\Offer;
use App\Models\Candidate;
class Application
{
    private int $id; 
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function getCandidate(): Candidate
    {
        return $this->candidate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDate(): string
    {
        return $this->date;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }
    public function setCandidate(Candidate $candidate): void
    {
        $this->candidate = $candidate;
    }


}