# Guía: API Segura con JWT

## Índice

1. [Introducción](#introducción)
2. [PARTE 1: Configuración de JWT en Symfony](#parte-1-configuración-de-jwt-en-symfony)
3. [PARTE 2: Configuración de CORS](#parte-2-configuración-de-cors)
4. [PARTE 3: Frontend Angular](#parte-3-frontend-angular)
5. [PARTE 4: Pruebas con Bruno](#parte-4-pruebas-con-bruno)

---

## Introducción

### ¿Qué es JWT?

**JWT (JSON Web Token)** es un estándar abierto (RFC 7519) que define una forma compacta y autónoma de transmitir
información de forma segura entre partes como un objeto JSON.

Un token JWT tiene tres partes separadas por puntos:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
```

1. **Header**: Algoritmo y tipo de token
2. **Payload**: Datos del usuario (claims)
3. **Signature**: Firma para verificar autenticidad

### ¿Qué es CORS?

**CORS (Cross-Origin Resource Sharing)** es un mecanismo de seguridad que permite o restringe solicitudes HTTP desde un
dominio diferente al del servidor.

Por ejemplo:

- Frontend en `http://localhost:4200` (Angular)
- Backend en `http://localhost:8000` (Symfony)

Sin CORS configurado, el navegador bloqueará las peticiones del frontend al backend.

### Flujo de autenticación JWT

```
┌─────────┐                              ┌─────────┐
│ Angular │                              │ Symfony │
└────┬────┘                              └────┬────┘
     │                                        │
     │  1. POST /api/login_check              │
     │    { email, password }                 │
     │ ─────────────────────────────────────► │
     │                                        │
     │  2. Respuesta con token JWT            │
     │    { token: "eyJ..." }                 │
     │ ◄───────────────────────────────────── │
     │                                        │
     │  3. Guardar token en localStorage      │
     │                                        │
     │  4. GET /api/products                  │
     │    Header: Authorization: Bearer eyJ...│
     │ ─────────────────────────────────────► │
     │                                        │
     │  5. Verificar token y responder        │
     │    { data: [...] }                     │
     │ ◄───────────────────────────────────── │
     │                                        │
```

---

## PARTE 1: Configuración de JWT en Symfony

### Paso 1.1: Crear proyecto Symfony (si no existe)

```bash
symfony new api_secure --webapp
cd api_secure
```

### Paso 1.2: Instalar LexikJWTAuthenticationBundle

```bash
composer require lexik/jwt-authentication-bundle
```

### Paso 1.3: Generar las claves RSA

```bash
php bin/console lexik:jwt:generate-keypair
```

Este comando crea automáticamente:

- `config/jwt/private.pem` - Clave privada (para firmar tokens)
- `config/jwt/public.pem` - Clave pública (para verificar tokens)
- Actualiza el `.env` con las variables necesarias

### Paso 1.4: Verificar variables de entorno

Comprueba que tu archivo `.env` contiene:

```env
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=tu_passphrase_generada_automaticamente
###< lexik/jwt-authentication-bundle ###
```

### Paso 1.5: Configurar lexik_jwt_authentication.yaml

Verifica el archivo `config/packages/lexik_jwt_authentication.yaml`:

```yaml
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600  # Token válido por 1 hora (opcional)
```

### Paso 1.6: Crear entidad User (si no existe)

```bash
php bin/console make:user
```

Responde a las preguntas:

- Nombre de la clase: `User`
- Guardar en base de datos: `yes`
- Propiedad única para identificar: `email`
- Hashear passwords: `yes`

### Paso 1.7: Configurar security.yaml

Edita `config/packages/security.yaml`:

```yaml
security:
    # Configuración del hasher de contraseñas
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    # Proveedor de usuarios (de dónde cargar los usuarios)
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    # Firewalls (reglas de seguridad por rutas)
    firewalls:
        # Desactivar seguridad para el profiler de desarrollo
        dev:
            pattern: ^/(_(profiler|wdt))/
            security: false

        # Firewall para el endpoint de login
        login:
            pattern: ^/api/login
            stateless: true
            json_login:
                # Ruta donde se envían las credenciales
                check_path: /api/login_check
                # Campos del JSON que contienen email y password
                username_path: email
                password_path: password
                # Handlers de Lexik para generar el token
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure

        # Firewall para todas las rutas /api/* (excepto login)
        api:
            pattern: ^/api
            stateless: true
            jwt: ~  # Activar autenticación JWT

    # Control de acceso (qué roles pueden acceder a qué rutas)
    access_control:
        - { path: ^/api/login, roles: PUBLIC_ACCESS }
        - { path: ^/api/register, roles: PUBLIC_ACCESS }
        - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

#### Explicación de cada sección:

| Sección            | Descripción                                                    |
|--------------------|----------------------------------------------------------------|
| `password_hashers` | Algoritmo para hashear contraseñas (bcrypt, argon2i, etc.)     |
| `providers`        | De dónde cargar usuarios (entidad User, campo email)           |
| `firewalls.login`  | Intercepta `/api/login_check` y genera el token JWT            |
| `firewalls.api`    | Protege todas las rutas `/api/*` con JWT                       |
| `access_control`   | Define qué rutas son públicas y cuáles requieren autenticación |

### Paso 1.8: Crear AuthController

Crea el archivo `src/Controller/AuthController.php`:

```php
<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    /**
     * Endpoint para registrar nuevos usuarios
     * POST /api/register
     * Body: { "email": "user@example.com", "password": "123456" }
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Decodificar el JSON del body
        $data = json_decode($request->getContent(), true);

        // Validaciones básicas
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(
                ['error' => 'Email y password son obligatorios'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Verificar si el usuario ya existe
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'El email ya está registrado'],
                Response::HTTP_CONFLICT
            );
        }

        // Crear usuario con contraseña hasheada
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Usuario registrado correctamente',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Endpoint de login - Este método NO se ejecuta
     * El json_login de security.yaml intercepta la petición
     * y Lexik devuelve el token automáticamente
     */
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck(): JsonResponse
    {
        throw new \LogicException('This method should not be reached!');
    }
}
```

---

## PARTE 2: Configuración de CORS

### Paso 2.1: Instalar NelmioCorsBundle

```bash
composer require nelmio/cors-bundle
```

### Paso 2.2: Configurar nelmio_cors.yaml

Edita `config/packages/nelmio_cors.yaml`:

```yaml
nelmio_cors:
    defaults:
        allow_credentials: false
        allow_origin: [ ]
        allow_headers: [ ]
        allow_methods: [ ]
        expose_headers: [ ]
        max_age: 0
        hosts: [ ]
        origin_regex: false

    paths:
        # Configuración para todas las rutas /api/*
        '^/api/':
            # Orígenes permitidos (tu frontend Angular)
            allow_origin: [ 'http://localhost:4200', 'http://localhost:4000' ]

            # Headers permitidos en las peticiones
            allow_headers: [ 'Content-Type', 'Authorization' ]

            # Métodos HTTP permitidos
            allow_methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS' ]

            # Tiempo en segundos que el navegador cachea la respuesta preflight
            max_age: 3600
```

#### Explicación de las opciones:

| Opción          | Descripción                                       |
|-----------------|---------------------------------------------------|
| `allow_origin`  | Dominios que pueden hacer peticiones (tu Angular) |
| `allow_headers` | Headers que el cliente puede enviar               |
| `allow_methods` | Métodos HTTP permitidos                           |
| `max_age`       | Cacheo de la respuesta OPTIONS (preflight)        |

### Paso 2.3: Entender las peticiones Preflight

Cuando el navegador detecta una petición "no simple" (con headers personalizados como `Authorization`), primero envía
una petición **OPTIONS** (preflight) para verificar si el servidor permite la petición real.

```
Browser                                    Server
   │                                          │
   │  OPTIONS /api/data                       │
   │  Origin: http://localhost:4200           │
   │ ────────────────────────────────────────►│
   │                                          │
   │  200 OK                                  │
   │  Access-Control-Allow-Origin: *          │
   │  Access-Control-Allow-Methods: GET,POST  │
   │◄──────────────────────────────────────── │
   │                                          │
   │  GET /api/data                           │
   │  Authorization: Bearer eyJ...            │
   │ ────────────────────────────────────────►│
   │                                          │
   │  200 OK                                  │
   │  { data: [...] }                         │
   │◄──────────────────────────────────────── │
```

---

## PARTE 3: Frontend Angular

### Paso 3.1: Configurar app.config.ts para HTTP

Edita `src/app/app.config.ts` para habilitar las peticiones HTTP:

```typescript
import { ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withFetch } from '@angular/common/http';
import { routes } from './app.routes';

export const appConfig: ApplicationConfig = {
    providers: [
        provideRouter(routes),
        provideHttpClient(withFetch())
    ]
};
```

### Paso 3.2: Crear servicio de autenticación

Crea el servicio con: `ng generate service services/auth`

Edita `src/app/services/auth.service.ts`:

```typescript
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
    register(email: string, password: string): Observable<any> {
        return this.http.post(`${this.apiUrl}/register`, { email, password });
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
    getToken(): string|null {
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
```

### Paso 3.3: Crear componente de login

Crea el componente con: `ng generate component components/login`

**`src/app/components/login/login.ts`:**

```typescript
import { Component, inject } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

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
```

**`src/app/components/login/login.html`:**

```html

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    @if (errorMessage !== '') {
    <div class="error">{{ errorMessage }}</div>
    }

    <form [formGroup]="reactiveForm" (ngSubmit)="onSubmit()">
        <div>
            <label>Email:</label>
            <input type="email" formControlName="email" placeholder="email@ejemplo.com">
        </div>

        <div>
            <label>Password:</label>
            <input type="password" formControlName="password" placeholder="********">
        </div>

        <button type="submit">Entrar</button>
    </form>
</div>
```

### Paso 3.4: Configurar rutas

Edita `src/app/app.routes.ts`:

```typescript
import { Routes } from '@angular/router';
import { Login } from './components/login/login';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: Login }
];
```

---

## PARTE 4: Pruebas con Bruno

### Estructura de la colección

```
bruno-collection/
├── bruno.json
├── environments/
│   └── local.bru
└── Auth/
    ├── Register.bru
    └── Login.bru
```

### Archivos de configuración

**bruno.json:**

```json
{
    "version": "1",
    "name": "API Secure",
    "type": "collection"
}
```

**environments/local.bru:**

```
vars {
  baseUrl: http://localhost:8000
  token:
}
```

### Endpoints de autenticación

**Auth/Register.bru:**

```
meta {
  name: Register
  type: http
  seq: 1
}

post {
  url: {{baseUrl}}/api/register
  body: json
  auth: none
}

headers {
  Content-Type: application/json
}

body:json {
  {
    "email": "test@test.com",
    "password": "123456"
  }
}
```

**Auth/Login.bru:**

```
meta {
  name: Login
  type: http
  seq: 2
}

post {
  url: {{baseUrl}}/api/login_check
  body: json
  auth: none
}

headers {
  Content-Type: application/json
}

body:json {
  {
    "email": "test@test.com",
    "password": "123456"
  }
}

script:post-response {
  if (res.body.token) {
    bru.setEnvVar("token", res.body.token);
  }
}
```

---

## Resumen de Comandos

| Paso            | Comando                                            |
|-----------------|----------------------------------------------------|
| Instalar JWT    | `composer require lexik/jwt-authentication-bundle` |
| Generar claves  | `php bin/console lexik:jwt:generate-keypair`       |
| Instalar CORS   | `composer require nelmio/cors-bundle`              |
| Crear entidad   | `php bin/console make:entity NombreEntidad`        |
| Actualizar BD   | `php bin/console doctrine:schema:update --force`   |
| Iniciar Symfony | `symfony server:start` o `symfony server:start --no-tls` |
| Iniciar Angular | `ng serve`                                         |

> **Nota:** Si tienes problemas con certificados SSL al iniciar Symfony, usa `--no-tls` para desactivar HTTPS.

---

## Errores Comunes

### 1. CORS bloqueado

**Error:** `Access to XMLHttpRequest has been blocked by CORS policy`
**Solución:** Verificar que `nelmio_cors.yaml` tiene el origen de Angular configurado.

### 2. Token expirado

**Error:** `401 Unauthorized - Expired JWT Token`
**Solución:** Hacer login de nuevo para obtener un nuevo token.

---

## Recursos

- [Documentación LexikJWTAuthenticationBundle](https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html)
- [Documentación NelmioCorsBundle](https://symfony.com/bundles/NelmioCorsBundle/current/index.html)
- [Angular HTTP Client](https://angular.io/guide/http)