import { Component } from '@angular/core';
import { StaffService } from '../service/staff.service';
import { RESOURCE_CACHE_PROVIDER } from '@angular/platform-browser-dynamic';

@Component({
  selector: 'app-add-staff-n',
  templateUrl: './add-staff-n.component.html',
  styleUrls: ['./add-staff-n.component.scss']
})
export class AddStaffNComponent {

  public selectedValue !: string  ;
  public name:string = '';
  public surname:string = '';
  public mobile:string = '';
  public email:string = '';
  public password:string = '';
  public password_confirmation:string = '';

  public birth_date:string = '';
  public gender:number = 1;
  public education:string = '';
  public designation:string = '';
  public address:string = '';

  public roles:any = [];

  public FILE_AVATAR:any;
  public IMAGEN_PREVIZUALIZA:any = 'assets/img/user-06.jpg'; 

  constructor(
    public staffService: StaffService,
  ) {

  }
  ngOnInit(): void{
    this.staffService.listConfig().subscribe((resp:any) =>{
      console.log(resp);
      this.roles = resp.roles;
        })
  }

  save(){
    console.log(this.selectedValue);

    let formData = new FormData();
    formData.append("name",this.name);
    formData.append("surname",this.surname);
    formData.append("email",this.email);
    formData.append("mobile",this.mobile);
    formData.append("birth_date",this.birth_date);
    formData.append("gender",this.gender+"");
    formData.append("education",this.education);
    formData.append("designation",this.designation);
    formData.append("address",this.address);
    formData.append("password",this.password);
    formData.append("role_id",this.selectedValue);
    formData.append("imagen",this.FILE_AVATAR);


    this.staffService.registerUser(formData).subscribe((resp:any) =>{
      console.log(resp);
    })

  }

  loadFile($event:any){
    if($event.target.files[0].type.indexOf("image") < 0){
      alert("SOLAMENTE PUEDEN SER ARCHIVOS DE TIPO IMAGEN");
      return;
    }
    this.FILE_AVATAR = $event.target.files[0];
    let reader = new FileReader();
    reader.readAsDataURL(this.FILE_AVATAR);
    reader.onloadend = () => this.IMAGEN_PREVIZUALIZA = reader.result;
  }
}
