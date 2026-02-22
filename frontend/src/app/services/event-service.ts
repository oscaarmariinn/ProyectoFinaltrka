import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Category, EventInterface } from '../interfaces/event-interface';
import {environment} from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class EventService {

  private url = `${environment.apiUrl}/api/events`;
  private http = inject(HttpClient);

  public getDataEvent(): Observable<EventInterface[]> {
    return this.http.get<EventInterface[]>(this.url);
  }

  private urlASC = "http://127.0.0.1:8000/api/events?order[eventDate]=ASC";
  private httpp = inject(HttpClient);
  private urlUpcoming = "http://127.0.0.1:8000/api/events/upcoming";

  public getUpcomingEvents(): Observable<EventInterface[]> {
    return this.http.get<EventInterface[]>(this.urlUpcoming);
  }

  public getDataEventASC(): Observable<EventInterface[]> {
    return this.httpp.get<EventInterface[]>(this.urlASC);
  }
  public getCreatedEvents(): Observable<EventInterface[]> {
    return this.http.get<EventInterface[]>(`${this.url}/created`);
  }

  public createEvent(payload: any): Observable<any> {
    return this.http.post<any>(this.url, payload);
  }
  public updateEvent(userId: number, eventId: number, payload: any): Observable<any> {
    return this.http.patch<any>(`${this.url}/${userId}/${eventId}`, payload);
  }
  public getEventById(id: number): Observable<EventInterface> {
  return this.http.get<EventInterface>(`${this.url}/${id}`);
}
}
