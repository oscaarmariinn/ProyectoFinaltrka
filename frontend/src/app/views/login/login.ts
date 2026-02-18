import { Component, inject } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth-service';

@Component({
    selector: 'app-login',
    imports: [ReactiveFormsModule],
    templateUrl: './login.html',
    styleUrl: './login.css'
})
export class Login {
    private authService = inject(AuthService);
    private router = inject(Router);

    errorMessage = '';

    reactiveForm = new FormGroup({
        email: new FormControl('', { nonNullable: true }),
        password: new FormControl('', { nonNullable: true })
    });

    onSubmit(): void {
        let data = this.reactiveForm.getRawValue();

        this.authService.login(data.email, data.password).subscribe({
            next: (response) => {
                // Guardar el token
                this.authService.saveToken(response.token);
                // Redirigir al formulario
                this.router.navigate(['/form']);
            },
            error: (err) => {
                this.errorMessage = 'Email o contraseña incorrectos';
            }
        });
    }
}