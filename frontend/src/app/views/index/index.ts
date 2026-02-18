import { ChangeDetectorRef, Component, inject, OnInit, OnDestroy } from '@angular/core';
import { NgStyle } from '@angular/common';
import { RouterLink } from '@angular/router';
import { Carrusel } from '../../components/carrusel/carrusel';
import { EventInterface } from '../../interfaces/event-interface';
import { EventsCardsAsc } from '../../components/events-cards-asc/events-cards-asc';
import { ModalAsc } from '../../components/modal-asc/modal-asc';
import { Modal } from '../../components/modal/modal';

@Component({
  selector: 'app-index',
  standalone: true,
  imports: [NgStyle, Carrusel, RouterLink, Modal, EventsCardsAsc],
  templateUrl: './index.html',
  styleUrl: './index.css',
})
export class Index implements OnInit, OnDestroy {

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
  currentImage!: string;
  nextImage!: string;
  index = 0;
  transitioning = false;

  private intervalId!: number;
  private cdr = inject(ChangeDetectorRef);

  async ngOnInit(): Promise<void> {
    await this.preloadImages();
    this.currentImage = this.images[0];
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
        img.decode?.().catch(() => {});
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
