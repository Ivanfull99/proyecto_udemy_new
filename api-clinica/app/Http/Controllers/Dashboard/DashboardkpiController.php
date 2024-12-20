<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Appointment\Appointment;
use App\Http\Resources\Appointment\AppointmentCollection;

class DashboardKpiController extends Controller
{
    
    public function config(){
        $users = User::orderBy("id","desc")
                ->whereHas("roles",function($q){
                    $q->where("name","like","%DOCTOR%");
                })
                //->where("state",1)
                ->get();

        return response()->json([
            "doctors" => $users->map(function($user){
                return [
                    "id" => $user->id,
                    "full_name" => $user->name.' '. $user->surname,
                    
                ];
            }),
        ]);
    }

    public function dashboard_admin(Request $request){
        date_default_timezone_set("America/Mexico_City");
        //MES ACTUAL CITAS
        $now = now();
        $num_appointments_current = DB::table("appointments")->where("deleted_at",NULL)
                            ->whereYear("date_appointment",$now->format("Y"))
                            ->whereMonth("date_appointment",$now->format("m"))
                            ->count();
        //MES ANTERIOR CITAS
        $before = now()->subMonth();
        $num_appointments_before = DB::table("appointments")->where("deleted_at",NULL)
                            ->whereYear("date_appointment",$before->format("Y"))
                            ->whereMonth("date_appointment",$before->format("m"))
                            ->count();

        // VS % PORCENTAJE DE CITAS
        $porcentajeD = 0;
        if($num_appointments_before > 0){
            $porcentajeD = (($num_appointments_current - $num_appointments_before ) / $num_appointments_before)*100;
        }
        ////////////////////////////////////////////////////
        //MES ACTUAL PACIENTES
        $now = now();
        $num_patients_current = DB::table("patients")->where("deleted_at",NULL)
                            ->whereYear("created_at",$now->format("Y"))
                            ->whereMonth("created_at",$now->format("m"))
                            ->count();
        //MES ANTERIOR PACIENTES
        $before = now()->subMonth();
        $num_patients_before = DB::table("patients")->where("deleted_at",NULL)
                            ->whereYear("created_at",$before->format("Y"))
                            ->whereMonth("created_at",$before->format("m"))
                            ->count();

        // VS % PORCENTAJE DE PACIENTES
        $porcentajeDP = 0;
        if($num_patients_before > 0){
            $porcentajeDP = (($num_patients_current - $num_patients_before ) / $num_patients_before)*100;
        }
        /////////////////////////////////////////////////////////////
        //MES ACTUAL DE CITAS ATENDIDAS
        $now = now();
        $num_appointments_attention_current = DB::table("appointments")->where("deleted_at",NULL)
                            ->whereYear("date_attention",$now->format("Y"))
                            ->whereMonth("date_attention",$now->format("m"))
                            ->count();
        //MES ANTERIOR CITAS ATENTIDAS
        $before = now()->subMonth();
        $num_appointments_attention_before = DB::table("appointments")->where("deleted_at",NULL)
                            ->whereYear("date_attention",$before->format("Y"))
                            ->whereMonth("date_attention",$before->format("m"))
                            ->count();

        // VS % PORCENTAJE DE CITAS ATENDIDAS
        $porcentajeDA = 0;
        if($num_appointments_attention_before > 0){
            $porcentajeDA = (($num_appointments_attention_current - $num_appointments_attention_before ) / $num_appointments_attention_before)*100;
        }
        ////////////////////////////////////////////////////////
          //MES ACTUAL CITAS  TOTAL
          $now = now();
          $num_appointments_total_current = DB::table("appointments")->where("deleted_at",NULL)
                              ->whereYear("date_appointment",$now->format("Y"))
                              ->whereMonth("date_appointment",$now->format("m"))
                              ->sum("appointments.amount");
          //MES ANTERIOR CITAS TOTAL
          $before = now()->subMonth();
          $num_appointments_total_before = DB::table("appointments")->where("deleted_at",NULL)
                              ->whereYear("date_appointment",$before->format("Y"))
                              ->whereMonth("date_appointment",$before->format("m"))
                              ->sum("appointments.amount");
  
          // VS % PORCENTAJE DE CITAS TOTAL
          $porcentajeDT = 0;
          if($num_appointments_total_before > 0){
              $porcentajeDT = (($num_appointments_total_current - $num_appointments_total_before ) / $num_appointments_total_before)*100;
          }

        $appointments = Appointment::whereYear("date_appointment",$now->format("Y"))
                                    ->whereMonth("date_appointment",$now->format("m"))
                                    ->where("status",1)
                                    ->orderBy("id","desc")
                                    ->take(5)
                                    ->get();
        return response()->json([
            "appointments" => AppointmentCollection::make($appointments),
            "num_appointments_current" => $num_appointments_current,
            "num_appointments_before" => $num_appointments_before,
            "porcentaje_d" =>  round($porcentajeD,2),
            //
            "num_patients_current" => $num_patients_current,
            "num_patients_before" => $num_patients_before,
            "porcentaje_dp" =>  round($porcentajeDP,2),
            //
            "num_appointments_attention_current" => $num_appointments_attention_current,
            "num_appointments_attention_before" => $num_appointments_attention_before,
            "porcentaje_da" =>  round($porcentajeDA,2),
             //
             "num_appointments_total_current" => $num_appointments_total_current,
             "num_appointments_total_before" => $num_appointments_total_before,
             "porcentaje_dt" =>  round($porcentajeDT,2),
        ]);
    }

    public function dashboard_admin_year(Request $request){

        $year = $request->year;

        $query_patient_by_genders = DB::table("appointments")->where("appointments.deleted_at",NULL)
                        ->whereYear("appointments.date_appointment",$year)
                        ->join("patients","appointments.patient_id", "=" ,"patients.id")
                        ->select(
                            DB::raw("YEAR(appointments.date_appointment) as year"),
                            DB::raw("MONTH(appointments.date_appointment) as month"),
                            DB::raw("SUM(CASE WHEN patients.gender = 1 THEN 1 ELSE 0 END) as hombre"),
                            DB::raw("SUM(CASE WHEN patients.gender = 2 THEN 1 ELSE 0 END) as mujer")
                        )->groupBy("year",'month')
                        ->orderBy("year")
                        ->orderBy("month")
                        ->get();
        $query_patients_speciality = DB::table("appointments")->where("appointments.deleted_at",NULL)  
                                    ->whereYear("appointments.date_appointment",$year)
                                    ->join("specialities","appointments.specialitie_id", "=" ,"specialities.id")
                                    ->select("specialities.name as name",DB::raw("COUNT(appointments.specialitie_id) as count"))
                                    ->groupBy("specialities.name")
                                    ->get();
        $query_patients_speciality_percentage = collect([]);
        $total_patients_speciality = $query_patients_speciality->sum("count");
        foreach ($query_patients_speciality as $key =>  $query_speciality) {
          $count_by_speciality = $query_speciality->count;
          $percentage = round(($count_by_speciality/$total_patients_speciality)* 100,2);
          $query_patients_speciality_percentage->push([
            "name" => $query_speciality->name,
            "percentage" => $percentage
          ]);
        }

        $query_income_year = DB::table("appointments")->where("appointments.deleted_at",NULL)
                            ->whereYear("appointments.date_appointment",$year)
                            ->where("appointments.status_pay",1)
                            ->select(
                                DB::raw("YEAR(appointments.date_appointment) as year"),
                                DB::raw("MONTH(appointments.date_appointment) as month"),
                                DB::raw("SUM(appointments.amount) as income"),
                            )->groupBy("year",'month')
                            ->orderBy("year")
                            ->orderBy("month")
                            ->get();

        $months_name = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        
        return response()->json([
            "months_name" => $months_name,
            "query_income_year" => $query_income_year,
            "query_patients_speciality_percentage" =>  $query_patients_speciality_percentage,
            "query_patient_by_genders" => $query_patient_by_genders,
            "query_patients_speciality" => $query_patients_speciality,
          
        ]);

    }

    public function dashboard_doctor(Request $request){
        date_default_timezone_set("America/Mexico_City");

        $doctor_id = $request->doctor_id;
        //MES ACTUAL CITAS
        $now = now();
        $num_appointments_current = DB::table("appointments")->where("deleted_at",NULL)
                            ->where("doctor_id",$doctor_id)
                            ->whereYear("date_appointment",$now->format("Y"))
                            ->whereMonth("date_appointment",$now->format("m"))
                            ->count();
        //MES ANTERIOR CITAS
        $before = now()->subMonth();
        $num_appointments_before = DB::table("appointments")->where("deleted_at",NULL)
                            ->where("doctor_id",$doctor_id)
                            ->whereYear("date_appointment",$before->format("Y"))
                            ->whereMonth("date_appointment",$before->format("m"))
                            ->count();

        // VS % PORCENTAJE DE CITAS
        $porcentajeD = 0;
        if($num_appointments_before > 0){
            $porcentajeD = (($num_appointments_current - $num_appointments_before ) / $num_appointments_before)*100;
        }

        /////////////////////////////////////////////////////////////
        //MES ACTUAL DE CITAS ATENDIDAS
        $now = now();
        $num_appointments_attention_current = DB::table("appointments")->where("deleted_at",NULL)
                            ->where("doctor_id",$doctor_id)
                            ->whereYear("date_attention",$now->format("Y"))
                            ->whereMonth("date_attention",$now->format("m"))
                            ->count();
        //MES ANTERIOR CITAS ATENTIDAS
        $before = now()->subMonth();
        $num_appointments_attention_before = DB::table("appointments")->where("deleted_at",NULL)
                            ->where("doctor_id",$doctor_id)
                            ->whereYear("date_attention",$before->format("Y"))
                            ->whereMonth("date_attention",$before->format("m"))
                            ->count();

        // VS % PORCENTAJE DE CITAS ATENDIDAS
        $porcentajeDA = 0;
        if($num_appointments_attention_before > 0){
            $porcentajeDA = (($num_appointments_attention_current - $num_appointments_attention_before ) / $num_appointments_attention_before)*100;
        }
        ////////////////////////////////////////////////////////
          //MES ACTUAL CITAS  TOTAL
          $now = now();
          $num_appointments_total_pay_current = DB::table("appointments")->where("deleted_at",NULL)
                              ->where("doctor_id",$doctor_id)
                              ->whereYear("date_appointment",$now->format("Y"))
                              ->whereMonth("date_appointment",$now->format("m"))
                              ->where("status_pay",1)
                              ->sum("appointments.amount");
          //MES ANTERIOR CITAS TOTAL
          $before = now()->subMonth();
          $num_appointments_total_pay_before = DB::table("appointments")->where("deleted_at",NULL)
                              ->where("doctor_id",$doctor_id)
                              ->whereYear("date_appointment",$before->format("Y"))
                              ->whereMonth("date_appointment",$before->format("m"))
                              ->where("status_pay",1)
                              ->sum("appointments.amount");
  
          // VS % PORCENTAJE DE CITAS TOTAL
          $porcentajeDT = 0;
          if($num_appointments_total_pay_before > 0){
              $porcentajeDT = (($num_appointments_total_pay_current - $num_appointments_total_pay_before ) / $num_appointments_total_pay_before)*100;
          }

          ////////////////////////////////////////////////////////
          //MES ACTUAL CITAS  PENDIENTE
          $now = now();
          $num_appointments_total_pending_current = DB::table("appointments")->where("deleted_at",NULL)
                              ->where("doctor_id",$doctor_id)
                              ->whereYear("date_appointment",$now->format("Y"))
                              ->whereMonth("date_appointment",$now->format("m"))
                              ->where("status_pay",2)
                              ->sum("appointments.amount");
          //MES ANTERIOR CITAS PENDIENTE
          $before = now()->subMonth();
          $num_appointments_total_pending_before = DB::table("appointments")->where("deleted_at",NULL)
                              ->where("doctor_id",$doctor_id)
                              ->whereYear("date_appointment",$before->format("Y"))
                              ->whereMonth("date_appointment",$before->format("m"))
                              ->where("status_pay",2)
                              ->sum("appointments.amount");
  
          // VS % PORCENTAJE DE CITAS Pendiente
          $porcentajeDTP = 0;
          if($num_appointments_total_pending_before > 0){
              $porcentajeDTP = (($num_appointments_total_pending_current - $num_appointments_total_pending_before ) / $num_appointments_total_pending_before)*100;
          }

        $appointments = Appointment::whereYear("date_appointment",$now->format("Y"))
                                    ->where("doctor_id",$doctor_id)
                                    ->whereMonth("date_appointment",$now->format("m"))
                                    ->where("status",1)
                                    ->orderBy("id","desc")
                                    ->take(5)
                                    ->get();
        return response()->json([
            "appointments" => AppointmentCollection::make($appointments),
            "num_appointments_current" => $num_appointments_current,
            "num_appointments_before" => $num_appointments_before,
            "porcentaje_d" =>  round($porcentajeD,2),
            
            "num_appointments_attention_current" => $num_appointments_attention_current,
            "num_appointments_attention_before" => $num_appointments_attention_before,
            "porcentaje_da" =>  round($porcentajeDA,2),
             //
             "num_appointments_total_pay_current" => $num_appointments_total_pay_current,
             "num_appointments_total_pay_before" => $num_appointments_total_pay_before,
             "porcentaje_dt" =>  round($porcentajeDT,2),

             "num_appointments_total_pending_current" => $num_appointments_total_pending_current,
             "num_appointments_total_pending_before" => $num_appointments_total_pending_before,
             "porcentaje_dtp" =>  round($porcentajeDTP,2),
        ]);
    }

    public function dashboard_doctor_year(Request $request){

        $year = $request->year;
        
        $doctor_id = $request->doctor_id;

        $query_patient_by_genders = DB::table("appointments")->where("appointments.deleted_at",NULL)
                        ->whereYear("appointments.date_appointment",$year)
                        ->where("appointments.doctor_id",$doctor_id)
                        ->join("patients","appointments.patient_id", "=" ,"patients.id")
                        ->select(
                            DB::raw("YEAR(appointments.date_appointment) as year"),
                            DB::raw("SUM(CASE WHEN patients.gender = 1 THEN 1 ELSE 0 END) as hombre"),
                            DB::raw("SUM(CASE WHEN patients.gender = 2 THEN 1 ELSE 0 END) as mujer")
                        )->groupBy("year")
                        ->orderBy("year")
                        ->get();

        $query_income_year = DB::table("appointments")->where("appointments.deleted_at",NULL)
                            ->whereYear("appointments.date_appointment",$year)
                            ->where("appointments.doctor_id",$doctor_id)
                            //->where("appointments.status_pay",1)
                            ->select(
                                DB::raw("YEAR(appointments.date_appointment) as year"),
                                DB::raw("MONTH(appointments.date_appointment) as month"),
                                DB::raw("SUM(appointments.amount) as income"),
                            )->groupBy("year",'month')
                            ->orderBy("year")
                            ->orderBy("month")
                            ->get();
        
        $query_n_appointment_year = DB::table("appointments")->where("appointments.deleted_at",NULL)
                                    ->whereYear("appointments.date_appointment",$year)
                                    ->where("appointments.doctor_id",$doctor_id)
                                    ->select(
                                        DB::raw("YEAR(appointments.date_appointment) as year"),
                                        DB::raw("MONTH(appointments.date_appointment) as month"),
                                        DB::raw("COUNT(*) as count_appointments"),
                                    )->groupBy("year",'month')
                                    ->orderBy("year")
                                    ->orderBy("month")
                                    ->get();

        $query_n_appointment_year_before = DB::table("appointments")->where("appointments.deleted_at",NULL)
                                    ->whereYear("appointments.date_appointment",$year - 1)
                                    ->where("appointments.doctor_id",$doctor_id)
                                    ->select(
                                        DB::raw("YEAR(appointments.date_appointment) as year"),
                                        DB::raw("MONTH(appointments.date_appointment) as month"),
                                        DB::raw("COUNT(*) as count_appointments"),
                                    )->groupBy("year",'month')
                                    ->orderBy("year")
                                    ->orderBy("month")
                                    ->get();

       $join_n_appointments_year = collect([]);
       $months_name = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                               
       foreach ($query_n_appointment_year->merge($query_n_appointment_year_before)->groupBy("month") as $key => $month_year) {
            // dd( $month_year);
            $join_n_appointments_year->push([
                "month" => $key,
                "month_name" => $months_name[$key - 1],
                "details" => $month_year,
            ]);
       }

        return response()->json([
            "months_name" => $months_name,
            "join_n_appointments_year" => $join_n_appointments_year,
            "query_n_appointment_year_before" => $query_n_appointment_year_before,
            "query_n_appointment_year" => $query_n_appointment_year,
            "query_income_year" => $query_income_year,
            "query_patient_by_genders" => $query_patient_by_genders,
        ]);

    }
}
