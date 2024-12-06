import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Login } from '../../interface/loginpage-interface';

@Component({
  selector: 'app-pymntpage',
  imports: [],
  templateUrl: './pymntpage.component.html',
  styleUrl: './pymntpage.component.css'
})
export class PymntpageComponent {

  public loginData = {} as Login;
  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.loginData.username = params['username'];
      this.loginData.password = params['password'];
      console.log('Username:', this.loginData.username);
      console.log('Password:', this.loginData.password);
    });
  }
}
