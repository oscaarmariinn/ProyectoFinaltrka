import { Component, inject } from '@angular/core';
import { GroupService } from '../../services/group-service';
import { Router } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-create-group',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './create-group.html',
  styleUrl: './create-group.css',
})
export class CreateGroup {
private groupService = inject(GroupService);
  private router = inject(Router);

  errorMessage = '';
  successMessage = '';
  isLoading = false;

  
  private currentUserId = 1; 

  reactiveForm = new FormGroup({
    name: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.minLength(3), Validators.maxLength(60)],
    }),
    description: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.maxLength(300)],
    }),
    is_private: new FormControl(false, { nonNullable: true }),
    userEmails: new FormControl('', { nonNullable: true }),
  });

  get name() { return this.reactiveForm.get('name')!; }
  get description() { return this.reactiveForm.get('description')!; }
  get is_private() { return this.reactiveForm.get('is_private')!; }

  onSubmit(): void {
    if (this.reactiveForm.invalid) {
      this.reactiveForm.markAllAsTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    const raw = this.reactiveForm.getRawValue();

    const payload = {
      name: raw.name,
      description: raw.description,
      is_private: raw.is_private,
      creator_name: this.currentUserId, 
    };

    this.groupService.createGroup(payload as any).subscribe({
      next: () => {
        this.isLoading = false;
        this.successMessage = '¡Grupo creado correctamente!';
        setTimeout(() => this.router.navigate(['/group']), 1500);
      },
      error: () => {
        this.isLoading = false;
        this.errorMessage = 'Error al crear el grupo. Inténtalo de nuevo.';
      },
    });
  }

  goBack(): void {
    this.router.navigate(['/groups']);
  }
}
