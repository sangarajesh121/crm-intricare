<?php

namespace App\Repositories;

use App\Models\CustomField;
use GuzzleHttp\Psr7\Request;

use App\Models\ContactCustomFieldValue;

class CustomFieldRepository
{

   public function findById(int $id): ?CustomField
    {
        return CustomField::findOrFail($id);
    }
    
    public function findByKey(string $fieldKey): ?CustomField
    {
        return CustomField::where('field_key', $fieldKey)->first();
    } 
    
    
    public function createDefinition(array $data): CustomField
    {
        return CustomField::create([
            'field_name' => $data['field_name'],
            'field_key' => $data['field_key'],
            'field_type' => $data['field_type'],
        ]);
    }

    public function upsertValue(int $contactId, int $customFieldId, $data)
    {
        
        return ContactCustomFieldValue::updateOrCreate(
            ['contact_id' => $contactId, 'custom_field_id' => $customFieldId],
            $data
        );
    }
    
    
}