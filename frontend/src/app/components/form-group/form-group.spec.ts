import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormGroup } from './form-group';

describe('FormGroup', () => {
  let component: FormGroup;
  let fixture: ComponentFixture<FormGroup>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormGroup]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormGroup);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
