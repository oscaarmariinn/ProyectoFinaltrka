import { afterNextRender, ChangeDetectorRef, Component, EventEmitter, inject, Output } from '@angular/core';
import { EventService } from '../../services/event-service';
import { EventInterface } from '../../interfaces/event-interface';

@Component({
  selector: 'app-events-cards-asc',
  imports: [],
  templateUrl: './events-cards-asc.html',
  styleUrl: './events-cards-asc.css',
})
export class EventsCardsAsc {
  constructor() {
    afterNextRender(() => {
      this.getResponse();
    });
  }

  public data = inject(EventService);
  private cdr = inject(ChangeDetectorRef);
  events: EventInterface[] = []

  public getResponse(): void {
    this.data.getDataEventASC().subscribe({
      next: (response) => {
        this.events = response.slice(0, 3);
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
