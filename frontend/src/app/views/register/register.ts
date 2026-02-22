import { Component, inject } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../services/auth-service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './register.html',
  styleUrls: ['./register.css']
})
export class Register {
  private authService = inject(AuthService);
  private router = inject(Router);

  errorMessage = '';
  successMessage = '';

  reactiveForm = new FormGroup({
    name: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    surname: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    email: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.email] }),
    password: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.minLength(6)] }),
  });

  get name() { return this.reactiveForm.get('name')!; }
  get surname() { return this.reactiveForm.get('surname')!; }
  get email() { return this.reactiveForm.get('email')!; }
  get password() { return this.reactiveForm.get('password')!; }

  onSubmit(): void {
    if (this.reactiveForm.invalid) {
      this.reactiveForm.markAllAsTouched();
      this.errorMessage = 'Por favor, completa todos los campos correctamente.';
      return;
    }

    const data = this.reactiveForm.getRawValue();

    this.authService.register({
      email: data.email,
      password: data.password,
      name: data.name,
      surname: data.surname
    }).subscribe({
      next: () => {
        this.successMessage = '¡Registro exitoso! Redirigiendo...';
        setTimeout(() => this.router.navigate(['/login']), 2000);
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al registrar usuario.';
      }
    });
  }
}