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