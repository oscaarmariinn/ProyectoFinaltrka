import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalGroup } from './modal-group';

describe('ModalGroup', () => {
  let component: ModalGroup;
  let fixture: ComponentFixture<ModalGroup>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalGroup]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ModalGroup);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
