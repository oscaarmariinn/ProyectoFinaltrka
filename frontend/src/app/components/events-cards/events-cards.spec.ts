import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventsCards } from './events-cards';

describe('EventsCards', () => {
  let component: EventsCards;
  let fixture: ComponentFixture<EventsCards>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EventsCards]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EventsCards);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
