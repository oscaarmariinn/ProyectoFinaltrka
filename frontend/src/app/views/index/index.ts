import { ChangeDetectorRef, Component, inject } from '@angular/core';
import { NgStyle } from '@angular/common';
import { Carrusel } from '../../components/carrusel/carrusel';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-index',
  imports: [NgStyle, Carrusel, RouterLink],
  templateUrl: './index.html',
  styleUrl: './index.css',
})
export class Index {
  images = [
    'https://ocioenvalencia.es/wp-content/uploads/2025/01/convento.jpg',
    'https://www.hellovalencia.es/wp-content/uploads/2019/07/Discoteca_Valencia_Marina-Beach-Club.jpg',
    'https://offloadmedia.feverup.com/valenciasecreta.com/wp-content/uploads/2022/10/13124225/sala-cine-mas-visitada-valencia.jpg',
    'https://bergamonte.es/wp-content/uploads/2022/08/clases-de-padel-Valencia-Deporte-scaled.jpg',
    'https://sp-ao.shortpixel.ai/client/to_webp,q_glossy,ret_img,w_1536,h_2048/https://unicovalencia.com/wp-content/uploads/2025/09/2-3b7fca34-1536x2048.jpg',
    'https://img.freepik.com/fotos-premium/vibrante-fiesta-playa-al-atardecer-dj-invitados-bailar_252600-24073.jpg?semt=ais_hybrid&w=740&q=80',
    'https://www.visita-valencia.com/wp-content/uploads/2021/05/imagenes-almuerzos-valencia_0003_almuerzo-bar-mistela.jpg',
  ];

  private cdr = inject(ChangeDetectorRef);
  currentImage = this.images[0];
  index = 0;
  
  public ngOnInit(): void {
    setInterval(() => {
      this.index = (this.index + 1) % this.images.length;
      this.currentImage = this.images[this.index];
      this.cdr.markForCheck();
    }, 3000);
  }
}
  


  




/*
  private cdr = inject(ChangeDetectorRef);
  private service = inject(Requests);

  public test: boolean = true;
  public char: boolean = true;
  public prev: string = '';
  public next: string = '';
  public name: string = '';
  public image: string = '';
  public characters: Character[] = [];
  public current: number = 0;
  public index: number = 0;

  public onCarousel(position: number): void {
    this.current += position;
    if (this.char === true) {
      if (this.current === this.characters.length) this.current = 0;
      if (this.current < 0) this.current = this.characters.length - 1;
      this.image = this.characters[this.current].image;
      this.name = this.characters[this.current].name;
    } 
  }
   public getCharacters(url: string): void {
    this.service.getCharacter(url).subscribe((response) => {
      this.characters = response.results;
      this.image = this.characters[this.current].image;
      this.name = this.characters[this.current].name;
      if (response.info.prev !== null) {
        this.prev = response.info.prev;
      } else {
        this.prev =
          'https://rickandmortyapi.com/api/character/?page=' +
          response.info.pages;
      }
      if (response.info.next !== null) {
        this.next = response.info.next;
      } else {
        this.next = 'https://rickandmortyapi.com/api/character/?page=1';
      }
      this.cdr.markForCheck();
    });
  }

   public showMore(position: number): void {
    this.index = 0;
    this.current = 0;
    if (position === -1) {
      if (this.char === true) {
        this.getCharacters(this.prev);
      } 
    } else if (position === 1) {
      if (this.char === true) {
        this.getCharacters(this.next);
      }
    }
  }*/

