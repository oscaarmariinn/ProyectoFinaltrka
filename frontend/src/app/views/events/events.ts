import { Component, inject } from '@angular/core';
import { EventsCards } from "../../components/events-cards/events-cards";
import { EventService } from '../../services/event-service';

@Component({
  selector: 'app-events',
  imports: [EventsCards],
  templateUrl: './events.html',
  styleUrl: './events.css',
})
export class Events {

}

