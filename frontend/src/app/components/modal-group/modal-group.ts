import { Component, Input } from '@angular/core';
import { GroupInterface } from '../../interfaces/group-interface';

@Component({
  selector: 'app-modal-group',
  imports: [],
  templateUrl: './modal-group.html',
  styleUrl: './modal-group.css',
})
export class ModalGroup {
@Input() selectedGroup: GroupInterface | null = null;
}
