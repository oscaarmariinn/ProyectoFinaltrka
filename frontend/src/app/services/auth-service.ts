import { Injectable, inject, signal, computed } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import {environment} from '../../environments/environment';

export interface AuthUser {
  id: number;
  email: string;
  roles: string[];
  name: string;
  surname: string;
}

export interface LoginResponse {
  token: string;
  user: AuthUser;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http      = inject(HttpClient);
  private router    = inject(Router);
  private apiUrl = `${environment.apiUrl}/api`;
  private readonly TOKEN_KEY = 'jwt_token';
  private readonly USER_KEY  = 'auth_user';

  private _currentUser = signal<AuthUser | null>(this.loadUserFromStorage());

  readonly currentUser     = this._currentUser.asReadonly();
  readonly isAuthenticated = computed(() => this._currentUser() !== null);

  register(userData: { email: string; password: string; name: string; surname: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData);
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http
      .post<LoginResponse>(`${this.apiUrl}/login_check`, { email, password })
      .pipe(
        tap((response) => {
          this.saveToken(response.token);
          this.saveUser(response.user);
          this._currentUser.set(response.user);
        })
      );
  }

  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    localStorage.removeItem(this.USER_KEY);
    this._currentUser.set(null);
    this.router.navigate(['/login']);
  }

  saveToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return this.isAuthenticated();
  }

  isTokenExpired(): boolean {
    const token = this.getToken();
    if (!token) return true;
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return payload.exp < Math.floor(Date.now() / 1000);
    } catch {
      return true;
    }
  }

  getAuthHeaders(): HttpHeaders {
    return new HttpHeaders({ Authorization: `Bearer ${this.getToken()}` });
  }

  updateCurrentUser(user: AuthUser): void {
  this.saveUser(user);
  this._currentUser.set(user);
}

  private saveUser(user: AuthUser): void {
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
  }

  private loadUserFromStorage(): AuthUser | null {
    try {
      const stored = localStorage.getItem(this.USER_KEY);
      const token  = localStorage.getItem(this.TOKEN_KEY);
      if (!stored || !token) return null;

      const payload = JSON.parse(atob(token.split('.')[1]));
      if (payload.exp < Math.floor(Date.now() / 1000)) {
        localStorage.removeItem(this.USER_KEY);
        localStorage.removeItem(this.TOKEN_KEY);
        return null;
      }

      return JSON.parse(stored) as AuthUser;
    } catch {
      return null;
    }
  }
}
