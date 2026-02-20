import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateGroup } from './update-group';

describe('UpdateGroup', () => {
  let component: UpdateGroup;
  let fixture: ComponentFixture<UpdateGroup>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateGroup]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UpdateGroup);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
