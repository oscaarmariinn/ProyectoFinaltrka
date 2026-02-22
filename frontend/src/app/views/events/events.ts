import { Component, inject } from '@angular/core';
import { EventsCards } from "../../components/events-cards/events-cards";
import { Modal } from '../../components/modal/modal';
import { EventInterface } from '../../interfaces/event-interface';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { MyEventsComponent } from '../my-events/my-events';


@Component({
  selector: 'app-events',
  imports: [EventsCards, Modal, RouterLink, RouterLinkActive, MyEventsComponent],
  templateUrl: './events.html',
  styleUrl: './events.css',
})
export class Events {
  selectedEvent: EventInterface | null = null;

  mostrarMisEventos = false;

  onSelect(event: EventInterface): void {
    this.selectedEvent = event;
  }
  
  toggleMisEventos() {
    this.mostrarMisEventos = !this.mostrarMisEventos;
  }
}

