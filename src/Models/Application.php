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
    private string $applied_at;
    private string $cv_path;
    private string $cover_letter;

    public function __construct(
        int $id,
        Offer $offer, 
        Candidate $candidate, 
        string $status, 
        string $applied_at,
        string $cv_path,
        string $cover_letter
    ) {
        $this->id = $id;
        $this->offer = $offer;
        $this->candidate = $candidate;
        $this->status = $status;
        $this->applied_at = $applied_at;
        $this->cv_path = $cv_path;
        $this->cover_letter = $cover_letter;
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

    public function getAppliedAt(): string
    {
        return $this->applied_at;
    }

    public function getCvPath(): string
    {
        return $this->cv_path;
    }

    public function getCoverLetter(): string
    {
        return $this->cover_letter;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setAppliedAt(string $applied_at): void
    {
        $this->applied_at = $applied_at;
    }

    public function setCvPath(string $cv_path): void
    {
        $this->cv_path = $cv_path;
    }

    public function setCoverLetter(string $cover_letter): void
    {
        $this->cover_letter = $cover_letter;
    }

    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }

    public function setCandidate(Candidate $candidate): void
    {
        $this->candidate = $candidate;
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Accept the application
     */
    public function accept(): void
    {
        $this->status = 'accepted';
    }

    /**
     * Reject the application
     */
    public function reject(): void
    {
        $this->status = 'rejected';
    }

    /**
     * Reset to pending status
     */
    public function reset(): void
    {
        $this->status = 'pending';
    }
}