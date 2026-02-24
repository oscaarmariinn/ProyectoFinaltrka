import { ChangeDetectorRef, Component, inject, OnInit, OnDestroy } from '@angular/core';
import { NgStyle } from '@angular/common';
import { RouterLink } from '@angular/router';
import { EventInterface } from '../../interfaces/event-interface';
import { EventsCardsAsc } from '../../components/events-cards-asc/events-cards-asc';
import { Modal } from '../../components/modal/modal';
import { AuthService } from '../../services/auth-service';

@Component({
  selector: 'app-index',
  standalone: true,
  imports: [RouterLink, Modal, EventsCardsAsc],
  templateUrl: './index.html',
  styleUrl: './index.css',
})
export class Index implements OnInit, OnDestroy {

  private authService = inject(AuthService);
  isLoggedIn = this.authService.isAuthenticated;
  images: string[] = [
    'imgs/eventosimgs/tardeo.jpg',
    'imgs/eventosimgs/almuerzo.jpg',
    'imgs/eventosimgs/cenar.jpg',
    'imgs/eventosimgs/centro_comercial.jpg',
    'imgs/eventosimgs/cine.jpg',
    'imgs/eventosimgs/tomar_algo.jpg',
    'imgs/eventosimgs/comida.jpg',
    'imgs/eventosimgs/cumpleanos.jpg',
    'imgs/eventosimgs/deporte.jpg',
  ];
  categories: string[] = [
  'tardeo.jpg',
  'almuerzo.jpg',
  'cenar.jpg',
  'centro_comercial.jpg',
  'cine.jpg',
  'tomar_algo.jpg',
  'comida.jpg',
  'cumpleanos.jpg',
  'deporte.jpg',
];
  currentImage: string = "imgs/eventosimgs/cine.jpg"
  nextImage: string = "imgs/eventosimgs/cine.jpg";
  index = 0;
  transitioning = false;
  currentCategory: string = 'cine';

  private intervalId!: number;
  private cdr = inject(ChangeDetectorRef);

  async ngOnInit(): Promise<void> {
    await this.preloadImages();
    this.currentImage = this.images[0];
    this.currentCategory = this.categories[0];
    this.intervalId = window.setInterval(() => {
      this.changeImage();
    }, 4000);
  }

  ngOnDestroy(): void {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  }
  private changeImage(): void {
    const nextIndex = (this.index + 1) % this.images.length;
    this.nextImage = this.images[nextIndex];
    this.transitioning = true;
    setTimeout(() => {
      this.currentImage = this.nextImage;
      this.currentCategory = this.categories[nextIndex];
      this.index = nextIndex;
      this.transitioning = false;
      this.cdr.markForCheck();
    }, 800);
  }
  private preloadImages(): Promise<void> {
    return new Promise((resolve) => {
      let loaded = 0;
      const total = this.images.length;
      this.images.forEach(src => {
        const img = new Image();
        img.src = src;
        img.decode?.().catch(() => { });
        img.onload = () => {
          loaded++;
          if (loaded === total) resolve();
        };
        img.onerror = () => {
          loaded++;
          if (loaded === total) resolve();
        };
      });
    });
  }
  selectedEvent: EventInterface | null = null;

  onSelect(event: EventInterface) {
    this.selectedEvent = event;
  }
}
