import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateEvents } from './update-events';

describe('UpdateEvents', () => {
  let component: UpdateEvents;
  let fixture: ComponentFixture<UpdateEvents>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateEvents]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UpdateEvents);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
