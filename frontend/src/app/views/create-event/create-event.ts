import { Component, inject } from '@angular/core';
import { EventService } from '../../services/event-service';
import { Router } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Category } from '../../interfaces/event-interface';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-create-event',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './create-event.html',
  styleUrl: './create-event.css',
})
export class CreateEvent {
private eventService = inject(EventService);
  private router = inject(Router);

  errorMessage = '';
  successMessage = '';
  isLoading = false;

  categories: Category[] = [];

  // TODO: reemplazar por el ID del usuario autenticado (ej. desde AuthService)
  private currentUserId = 1;

  reactiveForm = new FormGroup({
    title: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.minLength(3), Validators.maxLength(100)],
    }),
    description: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.maxLength(500)],
    }),
    event_date: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required],
    }),
    location: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.maxLength(150)],
    }),
    max_participants: new FormControl<number | null>(null, {
      validators: [Validators.required, Validators.min(1)],
    }),
    category_id: new FormControl<number | null>(null, {
      validators: [Validators.required],
    }),
    isPublic: new FormControl(true, { nonNullable: true }),
  });

  get title() { return this.reactiveForm.get('title')!; }
  get description() { return this.reactiveForm.get('description')!; }
  get event_date() { return this.reactiveForm.get('event_date')!; }
  get location() { return this.reactiveForm.get('location')!; }
  get max_participants() { return this.reactiveForm.get('max_participants')!; }
  get category_id() { return this.reactiveForm.get('category_id')!; }
  get isPublic() { return this.reactiveForm.get('isPublic')!; }

  ngOnInit(): void {
    // Cargar categorías disponibles para el selector
    this.eventService.getCategories().subscribe({
      next: (cats) => (this.categories = cats),
      error: () => (this.errorMessage = 'No se pudieron cargar las categorías.'),
    });
  }

  onSubmit(): void {
    if (this.reactiveForm.invalid) {
      this.reactiveForm.markAllAsTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';

    const raw = this.reactiveForm.getRawValue();

    const payload = {
      title: raw.title,
      description: raw.description,
      event_date: raw.event_date,
      location: raw.location,
      max_participants: raw.max_participants,
      category_id: raw.category_id,
      isPublic: raw.isPublic,
      creator_name: this.currentUserId,
    };

    this.eventService.createEvent(payload as any).subscribe({
      next: () => {
        this.isLoading = false;
        this.successMessage = '¡Evento creado correctamente!';
        setTimeout(() => this.router.navigate(['/events']), 1500);
      },
      error: () => {
        this.isLoading = false;
        this.errorMessage = 'Error al crear el evento. Inténtalo de nuevo.';
      },
    });
  }

  goBack(): void {
    this.router.navigate(['/events']);
  }
}
