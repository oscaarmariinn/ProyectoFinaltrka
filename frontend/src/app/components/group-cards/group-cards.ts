import { afterNextRender, ChangeDetectorRef, Component, EventEmitter, inject, Output } from '@angular/core';

import { GroupInterface } from '../../interfaces/group-interface';
import { GroupService } from '../../services/group-service';
import { RouterLink, RouterLinkActive } from '@angular/router';


@Component({
  selector: 'app-group-cards',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './group-cards.html',
  styleUrl: './group-cards.css',
})
export class GroupCards {
  constructor() {
    afterNextRender(() => {
      this.getResponse();
    });
  }

  public data = inject(GroupService);
  private cdr = inject(ChangeDetectorRef);
  groups: GroupInterface[] = []

  public getResponse(): void {
    this.data.getDataGroup().subscribe({
      next: (response) => {
        response.forEach(group => {
          if (group.is_private === false) {
            this.groups.push(group)
          }
        });

        console.log('Grupos cargados:', this.groups);
        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error al cargar grupos:', error);
      }
    });
  }

  @Output() select = new EventEmitter<GroupInterface>();

  selectEvent(group: GroupInterface): void {
    this.select.emit(group);
  }

}
