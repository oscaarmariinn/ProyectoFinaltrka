import {
  afterNextRender,
  ChangeDetectorRef,
  Component,
  EventEmitter,
  inject,
  Output,
} from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
import { EventService, EventFilters } from '../../services/event-service';
import { EventInterface, Category } from '../../interfaces/event-interface';

@Component({
  selector: 'app-events-cards',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './events-cards.html',
  styleUrl: './events-cards.css',
})
export class EventsCards {

  private data = inject(EventService);
  private cdr  = inject(ChangeDetectorRef);
  private fb   = inject(FormBuilder);

  events:     EventInterface[] = [];
  categories: Category[]       = [];
  loading = false;
  filtersOpen = false;

  filterForm: FormGroup = this.fb.group({
    title:            [''],
    description:      [''],
    date_from:        [''],
    date_to:          [''],
    category:         [''],
    max_participants: [''],
  });

  @Output() select = new EventEmitter<EventInterface>();

  constructor() {
    afterNextRender(() => {
      this.loadCategories();
      this.loadEvents({});
    });
  }

  toggleFilters(): void {
    this.filtersOpen = !this.filtersOpen;
  }

  loadCategories(): void {
    this.data.getCategories().subscribe({
      next: (cats) => {
        this.categories = cats;
        this.cdr.markForCheck();
      },
      error: (err) => console.error('Error cargando categorías:', err),
    });
  }

  loadEvents(filters: EventFilters): void {
    this.loading = true;
    this.data.getFilteredEvents(filters).subscribe({
      next: (response) => {
        this.events = response.filter(e => e.isPublic === true || e.isVerified === true);
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: (err) => {
        console.error('Error al cargar eventos:', err);
        this.loading = false;
        this.cdr.markForCheck();
      },
    });
  }

  applyFilters(): void {
    const raw = this.filterForm.value;
    const filters: EventFilters = Object.fromEntries(
      Object.entries(raw).filter(([_, v]) => v !== null && v !== '')
    ) as EventFilters;
    this.loadEvents(filters);
  }

  resetFilters(): void {
    this.filterForm.reset();
    this.loadEvents({});
  }

  selectEvent(event: EventInterface): void {
    this.select.emit(event);
  }

  getActiveFilters(): EventFilters {
    const raw = this.filterForm.value;
    return Object.fromEntries(
      Object.entries(raw).filter(([_, v]) => v !== null && v !== '')
    ) as EventFilters;
  }

  exportEvents(format: 'csv' | 'json'): void {
    this.data.exportAllEvents(this.getActiveFilters(), format);
  }
}
