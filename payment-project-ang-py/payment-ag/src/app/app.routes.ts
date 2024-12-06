import { Routes } from '@angular/router';
import { LoginpageComponent } from './component/loginpage/loginpage.component';
import { RegispageComponent } from './component/regispage/regispage.component';
import { PymntpageComponent } from './component/pymntpage/pymntpage.component';

export const routes: Routes = [
    {
        path:'',
        redirectTo:'loginpage',
        pathMatch:'full'
    },
    {
        path:'loginpage',
        component:LoginpageComponent
    },
    {
        path:'regispage',
        component:RegispageComponent
    },
    {
        path:'pymntpage',
        component:PymntpageComponent
    }
];
