import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserPasssord } from './user-passsord';

describe('UserPasssord', () => {
  let component: UserPasssord;
  let fixture: ComponentFixture<UserPasssord>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UserPasssord]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UserPasssord);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
