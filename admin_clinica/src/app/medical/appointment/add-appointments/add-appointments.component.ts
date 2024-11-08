import { Component } from '@angular/core';
import { AppointmentService } from '../service/appointment.service';


@Component({
  selector: 'app-add-appointments',
  templateUrl: './add-appointments.component.html',
  styleUrls: ['./add-appointments.component.scss']
})
export class AddAppointmentsComponent {
  hours:any =[];
  specialities:any =[];
  date_appointment:any;
  hour:any;
  specialitie_id:any;

  name:string = '';
  surname:string = '';
  mobile:string = '';
  n_document:number = 0;
  name_companion:string = '';
  surname_companion:string = '';

  amount:number = 0;
  amount_add:number = 0;
  method_payment:string = '';

  DOCTORS:any = [];
  DOCTOR_SELECTED:any;
  constructor(
  public appointmentService: AppointmentService,
){

}

ngOnInit(): void{
  this.appointmentService.listConfig().subscribe((resp:any)=> {
    this.hours = resp.hours;
    this.specialities = resp.specialities;
  })

}

  save(){

  }

  filtro(){
    let data = {
      date_appointment : this.date_appointment,
      hour: this.hour,
      specialitie_id : this.specialitie_id,
    }
    this.appointmentService.listFilter(data).subscribe((resp:any) =>{
      console.log(resp);
      this.DOCTORS = resp.doctors;
    
  

    })
  }

  countDisponibilidad(DOCTOR:any){
    let SEGMENTS = [];
    SEGMENTS = DOCTOR.segments.filter((item:any) => !item.is_appointment);
    return SEGMENTS.length;
  }

  showSegment(DOCTOR:any){
    this.DOCTOR_SELECTED = DOCTOR;
  }
  selectSegment(SEGMENT:any){

  }
}
 //borrar