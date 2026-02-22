import { Component, Input } from '@angular/core';
import { EventInterface } from '../../interfaces/event-interface';

@Component({
  selector: 'app-modal',
  imports: [],
  templateUrl: './modal.html',
  styleUrl: './modal.css',
})
export class Modal {
  @Input() selectedEvent: EventInterface | null = null;
}
