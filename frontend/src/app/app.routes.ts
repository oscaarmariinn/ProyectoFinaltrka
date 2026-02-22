import { Routes } from '@angular/router';
import { Index } from './views/index';
import { Contact } from './views/contact/contact';
import { User } from './views/user/user';
import { Login } from './views/login/login';
import { Register } from './views/register/register';
import {Events} from './views/events/events';
import { UserPasssord } from './views/user-passsord/user-passsord';
import { Group } from './views/group/group';
import { CreateGroup } from './views/create-group/create-group';
import { CreateEvent } from './views/create-event/create-event';
import { UpdateGroup } from './views/update-group/update-group';



export const routes: Routes = [
    { path: '', redirectTo: 'index', pathMatch: 'full' },
    { path: 'index', component: Index },
    { path: 'login', component: Login },
    { path: 'register', component: Register},
    { path: 'events', component: Events },
    {path: 'user', component: User},
    {path: 'newpassword', component: UserPasssord},
    {path: 'group', component: Group},
    {path: 'create-group', component: CreateGroup},
    {path: 'create-event', component: CreateEvent},
    { path: 'groups/edit/:id', component: UpdateGroup }
];
