import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { login } from '../../interface/loginpage-interface';

@Component({
  imports: [ FormsModule ],
  selector: 'app-loginpage',
  templateUrl: './loginpage.component.html',
  styleUrls: ['./loginpage.component.css'],
})
export class LoginpageComponent {
  public loginData = {} as login;

  constructor(private router: Router) {}

  onLogin(): void {
    console.log('Login data:', this.loginData);
    // Implement login API call here
  }

  navigateToRegister(): void {
    this.router.navigate(['/register']);
  }
}
