import { Component, Input } from '@angular/core';
import { EventInterface } from '../../interfaces/event-interface';

@Component({
  selector: 'app-modal-asc',
  imports: [],
  templateUrl: './modal-asc.html',
  styleUrl: './modal-asc.css',
})
export class ModalAsc {
@Input() selectedEvent: EventInterface | null = null;
}
