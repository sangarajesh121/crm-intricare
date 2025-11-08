<?php

namespace App\Services;

use App\Repositories\CustomFieldRepository;
use App\Helpers\CommonHelper;

class CustomFieldService
{
    protected $customFieldRepo;

    public function __construct(CustomFieldRepository $customFieldRepo)
    {
        $this->customFieldRepo = $customFieldRepo;
    }

    public function getCustomFieldById($id)
    {
        return $this->customFieldRepo->findById($id);
    }


    public function createCustomField(array $data)
    {
        //Check if field with same key and type exists
        $customField = $this->customFieldRepo->findByKeyAndType($data['field_key'], $data['field_type']);
        
        if(empty($customField)) {
            $customField = $this->customFieldRepo->createDefinition($data);
        }

        return $customField;
    }

    public function assignValuesToContacts($customField, $contactId, $data)
    {

        // If $customField is an ID, fetch the object
        if (!is_object($customField)) {
            $customField = $this->getCustomFieldById($customField);
            if (!$customField) {
                return null; // Or throw an exception
            }
        }
        
        $value = $data['field_value'];

        // If field type is file, handle file upload
        if($customField->field_type === 'file' && $value) {
            $filePath = CommonHelper::uploadFile($value, 'contacts/custom_fields', 'public');
           return $this->customFieldRepo->upsertValue($contactId, $customField->id, ['field_value' => $filePath]);
        } else {
           return  $this->customFieldRepo->upsertValue($contactId, $customField->id, ['field_value' => $value]);
        }
    }


    public function mergeCustomFieldValues($master, $secondary)
    {
        // Get all custom field values for both contacts
        $masterValues = $master->customFieldValues()->get();
        $secondaryValues = $secondary->customFieldValues()->get();
        
        // Create maps for quick lookups
        $masterValueMap = $masterValues->keyBy('custom_field_id');
        $secondaryValueMap = $secondaryValues->keyBy('custom_field_id');
        
        // Get all unique custom field IDs from both contacts
        $allFieldIds = collect($masterValueMap->keys())
            ->merge($secondaryValueMap->keys())
            ->unique();
            
        foreach ($allFieldIds as $fieldId) {
            $masterValue = $masterValueMap->get($fieldId);
            $secondaryValue = $secondaryValueMap->get($fieldId);
            
            // Case 1: Master doesn't have the field - transfer from secondary
            if (!$masterValue && $secondaryValue) {
                $this->customFieldRepo->upsertValue(
                    $master->id,
                    $fieldId,
                    [
                        'field_value' => $secondaryValue->field_value,
                        'field_origin' => 'merged'
                    ]
                );
            }
            // Case 2: Both have the field, but master's value is empty
            elseif ($masterValue && $secondaryValue && empty($masterValue->field_value) && !empty($secondaryValue->field_value)) {
                $this->customFieldRepo->upsertValue(
                    $master->id,
                    $fieldId,
                    [
                        'field_value' => $secondaryValue->field_value,
                        'field_origin' => 'overrided'
                    ]
                );
            }
            // Case 3: Master has non-empty value - keep it with 'self' origin (already set by default)
        }
        
        return $master;
    }


    public function validateCustomFieldData($request)
    {
        return  $request->validate([
            'field_name'   => 'required|string|max:100',
            'field_key'  => 'required|string|max:150',
            'field_type'  => 'required|in:text,number,date,file',
            'contact_id' => 'sometimes|integer|exists:contacts,id',
        ]);
    }
}