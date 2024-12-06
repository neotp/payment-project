import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, FormsModule, Validators, ReactiveFormsModule } from '@angular/forms';
import { Login } from '../../interface/loginpage-interface';
// import { ApiService } from '../../service/api.service';


@Component({
  imports: [ FormsModule ,ReactiveFormsModule],
  selector: 'app-loginpage',
  templateUrl: './loginpage.component.html',
  styleUrls: ['./loginpage.component.css'],
})
export class LoginpageComponent implements OnInit {
  public loginForm!: FormGroup;



  public loginData = {} as Login;

  constructor(
    private router: Router
    , private formBuilder: FormBuilder
    // , private api: ApiService
  ) {
    this.loginForm = this.formBuilder.group({
      usr: [{ value: null, disabled: false }, [Validators.required]]
      , pss: [{ value: null, disabled: false }, [Validators.required]]
    });
  }

  public ngOnInit(): void {
    this.loginForm.reset();
  }

  public onSubmit() {
    console.log(this.loginForm.value);
  }

  private populateForm(): void {
    this.loginData.username = this.loginForm.controls['usr'].value;
    this.loginData.password = this.loginForm.controls['pss'].value;
  }

  public login(): void {
    this.populateForm();
    if (!this.loginData.username || !this.loginData.password) {
      console.log('alert message');
    } else {
      console.log('username :',this.loginData.username );
      console.log('password :',this.loginData.password);
      // this.api.login(this.loginData).forEach((result: any) => {
      //   if(result){
      //     this.router.navigate(['/pymntpage'], {queryParams: this.loginData})
      //   } else {
      //     console.log('alert message');
      //   }
      // });
    }
  } 

  public register(): void {
    this.router.navigate(['/regispage']);
  }
}
