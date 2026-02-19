import { Component, inject } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth-service';

@Component({
  selector: 'app-register',
  templateUrl: './register.html',
  styleUrls: ['./register.css']
})
export class Register {
  private authService = inject(AuthService);
  private router = inject(Router);

  errorMessage = '';

  // Formulario actualizado con todos los campos necesarios
  reactiveForm = new FormGroup({
    email: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.email]
    }),
    password: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.minLength(6)]
    }),
    name: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required]
    }),
    surname: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required]
    })
  });

  onSubmit(): void {
    if (this.reactiveForm.invalid) {
      this.errorMessage = 'Por favor, completa todos los campos correctamente';
      return;
    }

    let data = this.reactiveForm.getRawValue();

    this.authService.register({
      email: data.email,
      password: data.password,
      name: data.name,
      surname: data.surname
    }).subscribe({
      next: (response) => {
        console.log('Registro exitoso:', response);
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 2000);
      },
      error: (error) => {
        console.error('Error en registro:', error);
        this.errorMessage = error.error?.message || 'Error al registrar usuario';
      }
    });
  }
}