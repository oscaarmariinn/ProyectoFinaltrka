import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-user-passsord',
  imports: [RouterLink, RouterLinkActive, ReactiveFormsModule],
  templateUrl: './user-passsord.html',
  styleUrl: './user-passsord.css',
})
export class UserPasssord {

  private http = inject(HttpClient);

  loading        = signal(false);
  successMessage = '';
  errorMessage   = '';

  passwordForm = new FormGroup({
    currentPassword: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    newPassword:     new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.minLength(6)] }),
    confirmPassword: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
  });

  onSubmit(): void {
    this.successMessage = '';
    this.errorMessage   = '';

    const { currentPassword, newPassword, confirmPassword } = this.passwordForm.getRawValue();

    if (this.passwordForm.invalid) {
      this.errorMessage = 'Rellena todos los campos correctamente';
      return;
    }

    if (newPassword !== confirmPassword) {
      this.errorMessage = 'Las contraseñas nuevas no coinciden';
      return;
    }

    this.loading.set(true);

    this.http.patch('http://localhost:8000/api/user/password', { currentPassword, newPassword })
      .subscribe({
        next: () => {
          this.successMessage = 'Contraseña actualizada correctamente';
          this.passwordForm.reset();
          this.loading.set(false);
        },
        error: (err) => {
          this.errorMessage = err.error?.message ?? 'Error al actualizar la contraseña';
          this.loading.set(false);
        }
      });
  }
}
