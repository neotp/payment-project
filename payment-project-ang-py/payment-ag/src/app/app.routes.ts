import { Routes } from '@angular/router';
import { LoginpageComponent } from './component/loginpage/loginpage.component';

export const routes: Routes = [
    {
        path:'',
        redirectTo:'loginpage',
        pathMatch:'full'
    },
    {
        path:'loginpage',
        component:LoginpageComponent
    }
];
