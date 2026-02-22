import { inject, Injectable } from '@angular/core';
import { GroupInterface } from '../interfaces/group-interface';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {environment} from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class GroupService {
  private url = `${environment.apiUrl}/api/groups`;
  private http = inject(HttpClient);

  public getDataGroup(): Observable<GroupInterface[]> {
    return this.http.get<GroupInterface[]>(this.url);
  }

  public createGroup(group: GroupInterface): Observable<GroupInterface> {
    return this.http.post<GroupInterface>(this.url, group);
  }

  public getGroupById(id: number): Observable<any> {
    return this.http.get<any>(`${this.url}/${id}`);
  }

  public updateGroup(userId: number, groupId: number, payload: any): Observable<any> {
    return this.http.patch<any>(`${this.url}/${userId}/${groupId}`, payload);
  }

  public getCreatedGroups(): Observable<GroupInterface[]> {
    return this.http.get<GroupInterface[]>(`${this.url}/created`);
  }
}
