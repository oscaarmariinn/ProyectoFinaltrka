import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventsCardsAsc } from './events-cards-asc';

describe('EventsCardsAsc', () => {
  let component: EventsCardsAsc;
  let fixture: ComponentFixture<EventsCardsAsc>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EventsCardsAsc]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EventsCardsAsc);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
