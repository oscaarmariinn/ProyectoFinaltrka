import { Component, inject, OnInit, Output, EventEmitter, ChangeDetectorRef } from '@angular/core';
import { GroupService} from '../../services/group-service';
import { GroupInterface } from '../../interfaces/group-interface';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-my-groups',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './my-groups.html',
})
export class MyGroupsComponent implements OnInit {
  private groupService = inject(GroupService);
  private cdr = inject(ChangeDetectorRef);
  groups: GroupInterface[] = [];
  loading = true;

  @Output() select = new EventEmitter<GroupInterface>();

  ngOnInit(): void {
    this.groupService.getCreatedGroups().subscribe({
      next: (data) => {
        console.log(data);
        this.groups = data;
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

  selectGroup(group: GroupInterface): void {
    this.select.emit(group);
  }
}
