import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { GroupCards } from '../../components/group-cards/group-cards';
import { ModalGroup } from '../../components/modal-group/modal-group';
import { GroupInterface } from '../../interfaces/group-interface';

@Component({
  selector: 'app-group',
  imports: [RouterLink, GroupCards, ModalGroup, RouterLinkActive],
  templateUrl: './group.html',
  styleUrl: './group.css',
})
export class Group {
  selectedGroup: GroupInterface | null = null;

  onSelect(event: GroupInterface) {
    this.selectedGroup = event;
  }
}
