<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;


 
    protected $fillable = [
        'student_id',
        'name',
        'program',
        'year_level',
        'units',
        'gpa',
        'attendance',
        'status',
    ];

    /**
     * Cast attributes to proper types.
     */
    protected $casts = [
        'gpa' => 'decimal:2',
        'year_level' => 'integer',
        'units' => 'integer',
        'attendance' => 'integer',
    ];

    /**
     * Compute academic standing from GPA.
     *   >= 2.50  → Good Standing
     *   1.75-2.49 → At Risk
     *   < 1.75   → Probation
     */
    public static function computeStatus(float $gpa): string
    {
        if ($gpa >= 2.5)  return 'Good Standing';
        if ($gpa >= 1.75) return 'At Risk';
        return 'Probation';
    }

   
    public function isAtRisk(): bool
    {
        return $this->status === 'At Risk';
    }


    public function isOnProbation(): bool
    {
        return $this->status === 'Probation';
    }

   
    public function isGoodStanding(): bool
    {
        return $this->status === 'Good Standing';
    }

    
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Good Standing' => 'bg-success',
            'At Risk'       => 'bg-warning text-dark',
            'Probation'     => 'bg-danger',
            default         => 'bg-secondary',
        };
    }

    public function gpaColorClass(): string
    {
        if ($this->gpa < 1.75) return 'text-danger';
        if ($this->gpa < 2.50) return 'text-warning';
        return 'text-success';
    }

    
    protected static function booted(): void
    {
        static::saving(function (Student $student) {
            $student->status = self::computeStatus((float) $student->gpa);
        });
    }
}