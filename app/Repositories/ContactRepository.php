<?php

namespace App\Repositories;

use App\Models\Contact;
// use App\Models\ContactCustomFieldValue;

class ContactRepository
{
    public function getAll($filters = [])
    {
        $query = Contact::query();

        if (!empty($filters['search'])) {
            $query->where(function($query) use ($filters) { 
                $query->where('name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('email', 'like', '%'.$filters['search'].'%')
                  ->orWhereHas('customFieldValues', function($q) use ($filters) {
                      $q->where('field_value', 'like', '%'.$filters['search'].'%'); //custom fields search
                  });
            });
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['ids'])) {
            $query->whereIn('id', $filters['ids']);
        }
        
        if($filters['only_active'] ?? false) {
            $query->where('status', 'active');
        }

        return $query->latest()->paginate(5);
    }

    
    public function findById($id)
    {
        return Contact::findOrFail($id);
    }
    
    public function create(array $data)
    {
        return Contact::create($data);
    }

    public function update($id, array $data)
    {
        $contact = $this->findById($id);
        $contact->update($data);
        return $contact;
    }

    public function getFillableFields() : array
    {
        return Contact::fillableFields() ?? [];
    }

    public function markInactive($id)
    {
        $contact = $this->findById($id);
        $contact->status = 'inactive';
        $contact->save();
        return $contact;
    }



}