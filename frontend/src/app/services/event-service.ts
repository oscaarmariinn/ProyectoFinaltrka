import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { EventInterface } from '../interfaces/event-interface';

@Injectable({
  providedIn: 'root',
})
export class EventService {

  private url = "http://127.0.0.1:8000/api/events";
  private http = inject(HttpClient);

  public getDataEvent(): Observable<EventInterface[]> {
    return this.http.get<EventInterface[]>(this.url);
  }

  private urlASC = "http://127.0.0.1:8000/api/events?order[eventDate]=ASC";
  private httpp = inject(HttpClient);

  public getDataEventASC(): Observable<EventInterface[]> {
    return this.httpp.get<EventInterface[]>(this.urlASC);
  }

}
