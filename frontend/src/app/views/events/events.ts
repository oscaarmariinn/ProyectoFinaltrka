import { Component, inject } from '@angular/core';
import { EventsCards } from "../../components/events-cards/events-cards";
import { EventService } from '../../services/event-service';
import { Modal } from '../../components/modal/modal';
import { EventInterface } from '../../interfaces/event-interface';

@Component({
  selector: 'app-events',
  imports: [EventsCards, Modal],
  templateUrl: './events.html',
  styleUrl: './events.css',
})
export class Events {
  selectedEvent: EventInterface | null = null;

  onSelect(event: EventInterface) {
    this.selectedEvent = event;
  }
}

