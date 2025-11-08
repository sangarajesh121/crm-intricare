<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomfieldController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/list', [ContactController::class, 'list'])->name('contacts.list');
    Route::get('/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/store', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::post('/contacts/{id}/deactivate', [ContactController::class, 'deactivate'])->name('contacts.deactivate');

    Route::post('/merge-preview', [ContactController::class, 'mergePreview'])->name('contacts.merge_preview');
    Route::post('/merge-contacts', [ContactController::class, 'mergeContacts'])->name('contacts.merge_contacts');
    Route::post('/merge-log', [ContactController::class, 'showMergeLog'])->name('contacts.merge_log');
});

Route::prefix('custom_fields')->group(function () {
    Route::post('/store', [CustomfieldController::class, 'store'])->name('custom_field.store');
});
