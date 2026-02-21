import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { AuthService, AuthUser } from '../../services/auth-service';

@Component({
  selector: 'app-user',
  imports: [RouterLink, RouterLinkActive, ReactiveFormsModule],
  templateUrl: './user.html',
  styleUrl: './user.css',
})
export class User {

  protected authService = inject(AuthService);
  private http          = inject(HttpClient);
  private router        = inject(Router);

  editing = signal(false);
  successMessage = '';
  errorMessage   = '';

  editForm = new FormGroup({
    name:    new FormControl('', { nonNullable: true }),
    surname: new FormControl('', { nonNullable: true }),
  });

  startEdit(): void {
    const user = this.authService.currentUser();
    this.editForm.setValue({
      name:    user?.name    ?? '',
      surname: user?.surname ?? '',
    });
    this.editing.set(true);
    this.successMessage = '';
    this.errorMessage   = '';
  }

  cancelEdit(): void {
    this.editing.set(false);
  }

  saveChanges(): void {
    const { name, surname } = this.editForm.getRawValue();

    this.http.patch<AuthUser>('http://localhost:8000/api/user/profile', { name, surname })
      .subscribe({
        next: (updatedUser) => {
          this.authService.updateCurrentUser(updatedUser);
          this.editing.set(false);
          this.successMessage = 'Perfil actualizado correctamente';
        },
        error: () => {
          this.errorMessage = 'Error al guardar los cambios';
        }
      });
  }

  closeSesion(): void {
    this.authService.logout();
  }
}