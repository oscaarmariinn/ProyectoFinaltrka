import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { GroupCards } from '../../components/group-cards/group-cards';
import { ModalGroup } from '../../components/modal-group/modal-group';
import { MyGroupsComponent } from '../my-groups/my-groups';
import { GroupInterface } from '../../interfaces/group-interface';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-group',
  imports: [RouterLink, RouterLinkActive, GroupCards, ModalGroup, MyGroupsComponent, CommonModule],
  templateUrl: './group.html',
  styleUrl: './group.css',
})
export class Group {
  selectedGroup: GroupInterface | null = null;
  mostrarMisGrupos = false;

  onSelect(group: GroupInterface) {
    this.selectedGroup = group;
  }

  toggleMisGrupos() {
    this.mostrarMisGrupos = !this.mostrarMisGrupos;
  }
}
