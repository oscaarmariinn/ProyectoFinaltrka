import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GroupCards } from './group-cards';

describe('GroupCards', () => {
  let component: GroupCards;
  let fixture: ComponentFixture<GroupCards>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GroupCards]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GroupCards);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
