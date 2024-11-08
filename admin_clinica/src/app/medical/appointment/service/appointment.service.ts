import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from 'src/app/config/config';
import { AuthService } from 'src/app/shared/auth/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AppointmentService {

  constructor(
    public http: HttpClient,
    public authService: AuthService,
  ){ }

  listAppointments(){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment";
    return this.http.get(URL,{headers: headers});
  }
  listConfig(){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment/config";
    return this.http.get(URL,{headers: headers});

  }

  registerAppointment(data:any){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment";
    return this.http.post(URL,data,{headers: headers});
  }

  listFilter(data:any){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment/filter";
    return this.http.post(URL,data,{headers: headers});
  }

  showAppointment(Appointment_id:string){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment/"+Appointment_id;
    return this.http.get(URL,{headers: headers});
  }
  updateAppointment(Appointment_id:string,data:any){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment/"+Appointment_id;
    return this.http.post(URL,data,{headers: headers});
  }
  deleteAppointment(Appointment_id:string){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/appointment/"+Appointment_id;
    return this.http.delete(URL,{headers: headers});
  }
}
