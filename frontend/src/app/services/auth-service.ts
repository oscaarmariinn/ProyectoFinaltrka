import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost:8000/api';

  // Registro de usuario
  register(userData: { email: string, password: string, name: string, surname: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData);
  }

  // Login - devuelve el token
  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login_check`, { email, password });
  }

  // Guardar token en localStorage
  saveToken(token: string): void {
    localStorage.setItem('jwt_token', token);
  }

  // Obtener token
  getToken(): string | null {
    return localStorage.getItem('jwt_token');
  }

  // Eliminar token (logout)
  logout(): void {
    localStorage.removeItem('jwt_token');
  }

  // Comprobar si está logueado
  isLoggedIn(): boolean {
    return this.getToken() !== null;
  }

  // Crear headers con el token para peticiones autenticadas
  getAuthHeaders(): HttpHeaders {
    const token = this.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }
}