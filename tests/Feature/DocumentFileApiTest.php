<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createDocumentManager(): array
{
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::query()->create([
        'permission_name' => 'documents.manage',
        'display_name' => 'Manage documents',
        'group' => 'documents',
    ]);
    $user->roles()->attach($role);
    $role->permissions()->attach($permission);

    return [$user, Employee::factory()->create()];
}

test('document upload is stored privately and downloaded only through the protected endpoint', function () {
    Storage::fake('local');
    [$user, $employee] = createDocumentManager();

    $response = $this->actingAs($user, 'sanctum')->post('/api/documents', [
        'document_name' => 'Employment contract',
        'document_type' => 'contract',
        'file' => UploadedFile::fake()->create('contract.pdf', 50, 'application/pdf'),
        'employee_id' => $employee->id,
    ], ['Accept' => 'application/json']);

    $response->assertCreated()
        ->assertJsonMissingPath('data.file_path');

    $documentId = $response->json('data.id');
    $document = \App\Models\Document::query()->findOrFail($documentId);

    Storage::disk('local')->assertExists($document->file_path);

    $this->getJson("/api/documents/{$documentId}/download")
        ->assertOk();

    $this->deleteJson("/api/documents/{$documentId}")
        ->assertNoContent();

    $this->assertSoftDeleted($document);
    Storage::disk('local')->assertExists($document->file_path);
});

test('document upload reports invalid file types in Arabic', function () {
    Storage::fake('local');
    [$user, $employee] = createDocumentManager();

    $this->withHeader('Accept-Language', 'ar')
        ->actingAs($user, 'sanctum')
        ->postJson('/api/documents', [
            'document_name' => 'Invalid document',
            'document_type' => 'contract',
            'file' => UploadedFile::fake()->create('contract.txt', 10, 'text/plain'),
            'employee_id' => $employee->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('file')
        ->assertJsonPath('errors.file.0', 'صيغة الملف غير مدعومة. الصيغ المقبولة: PDF أو JPG أو JPEG أو PNG.');

    $this->postJson('/api/documents', [
        'document_name' => 'Oversized document',
        'document_type' => 'contract',
        'file' => UploadedFile::fake()->create('contract.pdf', 10241, 'application/pdf'),
        'employee_id' => $employee->id,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('file')
        ->assertJsonPath('errors.file.0', 'يجب ألا يتجاوز حجم الملف 10 ميغابايت.');
});
