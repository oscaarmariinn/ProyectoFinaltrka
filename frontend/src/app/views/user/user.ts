import { Component, inject } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../services/auth-service';

@Component({
  selector: 'app-user',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './user.html',
  styleUrl: './user.css',
})
export class User {

  public data = inject(AuthService);
  private router = inject(Router);


  public closeSesion(): void {
    this.data.logout();
    alert('Has cerrado sesión correctamente')
    this.router.navigate(['/index']);
  }

}
