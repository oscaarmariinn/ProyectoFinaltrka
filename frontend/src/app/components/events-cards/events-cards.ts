import { afterNextRender, ChangeDetectorRef, Component, EventEmitter, inject, Output } from '@angular/core';
import { EventService } from '../../services/event-service';
import { EventInterface } from '../../interfaces/event-interface';

@Component({
  selector: 'app-events-cards',
  imports: [],
  templateUrl: './events-cards.html',
  styleUrl: './events-cards.css',
})
export class EventsCards {

  constructor() {
    afterNextRender(() => {
      this.getResponse();
    });
  }

  public data = inject(EventService);
  private cdr = inject(ChangeDetectorRef);
  events: EventInterface[] = []

  public getResponse(): void {
    this.data.getDataEvent().subscribe({
      next: (response) => {
        response.forEach(event => {
          if (event.isPublic === true || event.isVerified === true) {
            this.events.push(event)
          }
        });
        console.log('Eventos cargados:', this.events);
        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error al cargar eventos:', error);
      }
    });
  }
  @Output() select = new EventEmitter<EventInterface>();

  selectEvent(event: EventInterface): void {
    this.select.emit(event);
  }

}
