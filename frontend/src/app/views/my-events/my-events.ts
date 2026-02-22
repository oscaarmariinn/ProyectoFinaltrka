import { Component, inject, OnInit, Output, EventEmitter, ChangeDetectorRef } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { EventInterface } from '../../interfaces/event-interface';
import { EventService } from '../../services/event-service';

@Component({
  selector: 'app-my-events',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './my-events.html',
})
export class MyEventsComponent implements OnInit {
  private eventService = inject(EventService);
  private cdr = inject(ChangeDetectorRef);
  events: EventInterface[] = [];
  loading = true;

  @Output() select = new EventEmitter<EventInterface>();

  ngOnInit(): void {
    this.eventService.getCreatedEvents().subscribe({
      next: (data) => {
        console.log(data);
        this.events = data;
        this.loading = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error(err);
        this.loading = false;
        this.cdr.detectChanges();
      }
    });
  }

  selectEvent(event: EventInterface): void {
    this.select.emit(event);
  }
}
