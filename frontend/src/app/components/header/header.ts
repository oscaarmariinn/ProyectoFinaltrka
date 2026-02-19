import { afterNextRender, ChangeDetectorRef, Component, inject } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';


@Component({
  selector: 'app-header',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header {
  public user: boolean = false;
  constructor() {
    afterNextRender(() => {
      this.reload();
    });
  }

  private cdr = inject(ChangeDetectorRef);

  public reload(): void {
    let local = localStorage.getItem('jwt_token');
    if (local !== null) {
      this.user = true;
    }
    this.cdr.markForCheck();
  }

}
