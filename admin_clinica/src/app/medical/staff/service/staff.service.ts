import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from 'src/app/config/config';
import { AuthService } from 'src/app/shared/auth/auth.service';

@Injectable({
  providedIn: 'root'
})
export class StaffService {

  constructor(
    public http: HttpClient,
    public authService: AuthService,
  ){ }

  listUsers(){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/staffs";
    return this.http.get(URL,{headers: headers});
  }
  listConfig(){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/staffs/config";
    return this.http.get(URL,{headers: headers});

  }

  registerUser(data:any){
    let headers = new HttpHeaders({'Authorization': 'bearer' +this.authService.token});
    let URL = URL_SERVICIOS+"/staffs";
    return this.http.post(URL,data,{headers: headers});
  };
}
