import { Component } from '@angular/core';
import { LoginpageComponent } from "./component/loginpage/loginpage.component";

@Component({
  selector: 'app-root',
  imports: [LoginpageComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'payment-ag';
}
