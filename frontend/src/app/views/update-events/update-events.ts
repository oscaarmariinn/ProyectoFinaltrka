import { Component, inject, OnInit, ChangeDetectorRef } from '@angular/core';
import { EventService } from '../../services/event-service';
import { AuthService } from '../../services/auth-service';
import { Router, ActivatedRoute } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-update-events',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './update-events.html',
  styleUrl: './update-events.css',
})
export class UpdateEvents implements OnInit {

  private eventService = inject(EventService);
  private authService = inject(AuthService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private cdr = inject(ChangeDetectorRef);

  errorMessage = '';
  successMessage = '';
  isLoading = false;
  isLoadingData = true;

  private eventId!: number;

  private get currentUserId(): number {
    return this.authService.currentUser()?.id ?? 0;
  }

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
    this.eventId = Number(this.route.snapshot.paramMap.get('id'));
    this.eventService.getEventById(this.eventId).subscribe({
      next: (event) => {
        this.reactiveForm.patchValue({
          title: event.title,
          description: event.description,
          event_date: event.event_date
            ? new Date(event.event_date).toISOString().slice(0, 16)
            : '',
          location: event.location,
          max_participants: event.max_participants,
          isPublic: event.isPublic,
        });
        this.isLoadingData = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error(err);
        this.errorMessage = 'No se pudo cargar el evento.';
        this.isLoadingData = false;
        this.cdr.detectChanges();
      },
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
    };

    this.eventService.updateEvent(this.currentUserId, this.eventId, payload).subscribe({
      next: () => {
        this.isLoading = false;
        this.successMessage = '¡Evento actualizado correctamente!';
        this.cdr.detectChanges();
        setTimeout(() => this.router.navigate(['/events']), 1500);
      },
      error: (err) => {
        this.isLoading = false;
        if (err.status === 403) {
          this.errorMessage = 'No tienes permisos para editar este evento.';
        } else if (err.status === 404) {
          this.errorMessage = 'El evento no existe.';
        } else {
          this.errorMessage = 'Error al actualizar el evento. Inténtalo de nuevo.';
        }
        this.cdr.detectChanges();
      },
    });
  }

  goBack(): void {
    this.router.navigate(['/events']);
  }
}

