<?php

namespace App\Models\Appointment;

use App\Models\User;
use App\Models\Patient\Patient;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor\DoctorScheduleJoinHour;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "doctor_id",
        "patient_id",
        "date_appointment",
        "specialitie_id",
        "doctor_schedule_join_hour_id",
        "user_id",
    ];

    public function setCreatedAtAttribute($value)
    {
    	date_default_timezone_set('America/Mexico_City');
        $this->attributes["created_at"]= Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
    	date_default_timezone_set("America/Mexico_City");
        $this->attributes["updated_at"]= Carbon::now();
    }

    public function doctor() {
        return $this->belongsTo(User::class,"doctor_id");
    }
    public function patient() {
        return $this->belongsTo(Patient::class);
    }
    public function doctor_schedule_join_hour() {
        return $this->belongsTo(DoctorScheduleJoinHour::class);
    }
}
