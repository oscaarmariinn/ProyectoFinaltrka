import { NgStyle } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-carrusel',
  imports: [NgStyle],
  templateUrl: './carrusel.html',
  styleUrl: './carrusel.css',
})
export class Carrusel {
  @Input() name: string = '';
  @Input() photo: string = 'https://m.media-amazon.com/images/S/pv-target-images/87bd8d9edc4fafda8377cdc6da823ca55cc8afe36dc855d4a4b311716c69b521.jpg';
  @Output() position = new EventEmitter<number>();

  public showMore(position: number): void {
    this.position.emit(position);
  }
}
