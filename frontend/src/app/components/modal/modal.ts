import { Component, Input, inject, signal, computed, OnChanges, SimpleChanges } from '@angular/core';
import { EventInterface } from '../../interfaces/event-interface';
import { EventService } from '../../services/event-service';
import { AuthService } from '../../services/auth-service';

@Component({
  selector: 'app-modal',
  imports: [],
  templateUrl: './modal.html',
  styleUrl: './modal.css',
})
export class Modal implements OnChanges {

  @Input() selectedEvent: EventInterface | null = null;

  private eventService = inject(EventService);
  private authService  = inject(AuthService);

  loading = signal(false);
  message = signal<string | null>(null);
  messageType = signal<'success' | 'error'>('success');

  isJoined = signal(false);
  currentParticipants = signal(0);

  isFull = computed(() => {
    const max = this.selectedEvent?.max_participants ?? null;
    if (max === null) return false;
    return this.currentParticipants() >= max;
  });

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['selectedEvent'] && this.selectedEvent) {
      this.message.set(null);
      this.currentParticipants.set(this.selectedEvent.participants?.length ?? 0);
      const userId = this.authService.currentUser()?.id;
      this.isJoined.set(
        !!userId && (this.selectedEvent.participants ?? []).some(p => p.id === userId)
      );
    }
  }

  join(): void {
    if (!this.selectedEvent) return;
    this.loading.set(true);
    this.message.set(null);
    this.eventService.joinEvent(this.selectedEvent.id).subscribe({
      next: (res) => {
        this.isJoined.set(true);
        this.currentParticipants.set(res.participants);
        this.message.set(res.message);
        this.messageType.set('success');
        this.loading.set(false);
      },
      error: (err) => {
        this.message.set(err.error?.message ?? 'Error al inscribirse');
        this.messageType.set('error');
        this.loading.set(false);
      },
    });
  }

  leave(): void {
    if (!this.selectedEvent) return;
    this.loading.set(true);
    this.message.set(null);
    this.eventService.leaveEvent(this.selectedEvent.id).subscribe({
      next: (res) => {
        this.isJoined.set(false);
        this.currentParticipants.set(res.participants);
        this.message.set(res.message);
        this.messageType.set('success');
        this.loading.set(false);
      },
      error: (err) => {
        this.message.set(err.error?.message ?? 'Error al cancelar inscripción');
        this.messageType.set('error');
        this.loading.set(false);
      },
    });
  }
}
