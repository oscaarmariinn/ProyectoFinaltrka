import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Category, EventInterface } from '../interfaces/event-interface';
import { environment } from '../../environments/environment';
import { AuthService } from './auth-service';

export interface EventFilters {
  title?:            string;
  description?:      string;
  date_from?:        string;
  date_to?:          string;
  category?:         number | string;
  max_participants?: number | string;
}

@Injectable({
  providedIn: 'root',
})
export class EventService {

  private url         = `${environment.apiUrl}/api/events`;
  private http        = inject(HttpClient);
  private authService = inject(AuthService);
  private urlUpcoming = `${environment.apiUrl}/api/events/upcoming`;

  public getDataEvent(): Observable<EventInterface[]> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<EventInterface[]>(this.url, { headers });
  }

  public getUpcomingEvents(): Observable<EventInterface[]> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<EventInterface[]>(this.urlUpcoming, { headers });
  }

  public getDataEventASC(): Observable<EventInterface[]> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<EventInterface[]>(`${this.url}?order[eventDate]=ASC`, { headers });
  }

  public getCreatedEvents(): Observable<EventInterface[]> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<EventInterface[]>(`${this.url}/created`, { headers });
  }

  public createEvent(payload: any): Observable<any> {
    const headers = this.authService.getAuthHeaders();
    return this.http.post<any>(this.url, payload, { headers });
  }

  public updateEvent(userId: number, eventId: number, payload: any): Observable<any> {
    const headers = this.authService.getAuthHeaders();
    return this.http.patch<any>(`${this.url}/${userId}/${eventId}`, payload, { headers });
  }

  public getEventById(id: number): Observable<EventInterface> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<EventInterface>(`${this.url}/${id}`, { headers });
  }

  public getFilteredEvents(filters: EventFilters): Observable<EventInterface[]> {
    const headers = this.authService.getAuthHeaders();
    let params = new HttpParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        params = params.set(key, String(value));
      }
    });

    return this.http.get<EventInterface[]>(`${this.url}/filter`, { headers, params });
  }

  public getCategories(): Observable<Category[]> {
    const headers = this.authService.getAuthHeaders();
    return this.http.get<Category[]>(`${this.url}/categories`, { headers });
  }

  public joinEvent(eventId: number): Observable<{ message: string; participants: number }> {
    const headers = this.authService.getAuthHeaders();
    return this.http.post<{ message: string; participants: number }>(
      `${this.url}/${eventId}/join`,
      {},
      { headers }
    );
  }

  public leaveEvent(eventId: number): Observable<{ message: string; participants: number }> {
    const headers = this.authService.getAuthHeaders();
    return this.http.delete<{ message: string; participants: number }>(
      `${this.url}/${eventId}/leave`,
      { headers }
    );
  }

  public exportEventParticipants(eventId: number, format: 'csv' | 'json' = 'csv'): void {
    const headers = this.authService.getAuthHeaders();
    this.http.get(`${this.url}/${eventId}/export?format=${format}`, {
      headers,
      responseType: 'blob',
    }).subscribe(blob => {
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `asistentes_${eventId}.${format}`;
      link.click();
      URL.revokeObjectURL(url);
    });
  }

  public exportAllEvents(filters: EventFilters = {}, format: 'csv' | 'json' = 'csv'): void {
    const headers = this.authService.getAuthHeaders();
    let params = new HttpParams().set('format', format);

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        params = params.set(key, String(value));
      }
    });

    this.http.get(`${this.url}/export/all`, {
      headers,
      params,
      responseType: 'blob',
    }).subscribe(blob => {
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `eventos.${format}`;
      link.click();
      URL.revokeObjectURL(url);
    });
  }
}
