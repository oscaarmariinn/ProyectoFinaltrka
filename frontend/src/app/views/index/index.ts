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
}
