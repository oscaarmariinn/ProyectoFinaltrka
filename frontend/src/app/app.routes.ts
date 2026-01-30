import { Routes } from '@angular/router';
import { Index } from './views/index';
import { Contact } from './views/contact/contact';
import { User } from './views/user/user';
import { Login } from './views/login/login';
import { Register } from './views/register/register';

export const routes: Routes = [
    { path: '', redirectTo: 'index', pathMatch: 'full' },
    { path: 'index', component: Index },
    { path: 'contact', component: Contact },
    { path: 'login', component: Login },
    { path: 'register', component: Register}
];
