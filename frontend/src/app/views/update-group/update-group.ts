import { Component, inject, OnInit } from '@angular/core';
import { GroupService } from '../../services/group-service';
import { Router, ActivatedRoute } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-update-group',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './update-group.html',
  styleUrl: './update-group.css',
})
export class UpdateGroup implements OnInit{

  private groupService = inject(GroupService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  errorMessage = '';
  successMessage = '';
  isLoading = false;
  isLoadingData = true;

  // ID del grupo obtenido desde la URL (ej: /groups/edit/3)
  private groupId!: number;

  // TODO: reemplazar por el ID del usuario autenticado (ej. desde AuthService)
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
  });

  get name() { return this.reactiveForm.get('name')!; }
  get description() { return this.reactiveForm.get('description')!; }
  get is_private() { return this.reactiveForm.get('is_private')!; }

  ngOnInit(): void {
    this.groupId = Number(this.route.snapshot.paramMap.get('id'));
    console.log('groupId:', this.groupId);
    this.groupService.getGroupById(this.groupId).subscribe({
      next: (group) => {
        this.reactiveForm.patchValue({
          name: group.name,
          description: group.description,
          is_private: group.is_private,
        });
        this.isLoadingData = false;
      },
      error: () => {
        this.errorMessage = 'No se pudo cargar el grupo.';
        this.isLoadingData = false;
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
      name: raw.name,
      description: raw.description,
      is_private: raw.is_private,
      creator_name: this.currentUserId,
    };

    this.groupService.updateGroup(this.currentUserId, this.groupId, payload as any).subscribe({
      next: () => {
        this.isLoading = false;
        this.successMessage = '¡Grupo actualizado correctamente!';
        setTimeout(() => this.router.navigate(['/groups']), 1500);
      },
      error: (err) => {
        this.isLoading = false;
        if (err.status === 403) {
          this.errorMessage = 'No tienes permisos para editar este grupo.';
        } else if (err.status === 404) {
          this.errorMessage = 'El grupo no existe.';
        } else {
          this.errorMessage = 'Error al actualizar el grupo. Inténtalo de nuevo.';
        }
      },
    });
  }

  goBack(): void {
    this.router.navigate(['/groups']);
  }
}
