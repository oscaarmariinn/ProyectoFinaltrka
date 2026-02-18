import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalAsc } from './modal-asc';

describe('ModalAsc', () => {
  let component: ModalAsc;
  let fixture: ComponentFixture<ModalAsc>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalAsc]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModalAsc);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
