<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use App\Helpers\CommonHelper;

class ContactService
{
    protected $contactRepo;

    public function __construct(ContactRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    public function getAllContacts($filters = [])
    {
        return $this->contactRepo->getAll($filters);
    }

    public function createContact(array $data)
    {
        $contact = $this->contactRepo->create($data);

        if(isset($data['profile_image_path']) && $data['profile_image_path']){
            $contact->profile_image_path = CommonHelper::uploadFile($data['profile_image_path'], 'contacts/profile_image_path', 'public');
            $contact->save();
        }

         if(isset($data['other_doc']) && $data['other_doc']){
            $contact->other_doc= CommonHelper::uploadFile($data['other_doc'], 'contacts/other_docs', 'public');
            $contact->save();
        }
        

        // if (!empty($data['custom_fields'])) {
        //     $this->contactRepo->saveCustomFields($contact->id, $data['custom_fields']);
        // }

        return $contact;
    }

    public function findContactById($id)
    {
        return $this->contactRepo->findById($id);
    }

    public function updateContact($id, array $data)
    {
        $contact = $this->contactRepo->update($id, $data);

         if(isset($data['profile_image_path']) && $data['profile_image_path']){
            $contact->profile_image_path = CommonHelper::uploadFile($data['profile_image_path'], 'contacts/profile_image_path', 'public');
            $contact->save();
        }

        if(isset($data['other_doc']) && $data['other_doc']){
            $contact->other_doc= CommonHelper::uploadFile($data['other_doc'], 'contacts/other_docs', 'public');
            $contact->save();
        }

        return $contact;
    }


    public function validateRequestData($request)
    {
        
        return  $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|max:150',
            'phone'  => 'required|string|max:30',
            'gender' => 'required|in:male,female,other',
            'profile_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_doc' => 'nullable|mimes:pdf,doc,docx,txt|max:5120',
        ]);
    }
    


    public function mergeContacts($master, $secondary)
    {
        $fillableFields = $this->contactRepo->getFillableFields();
        

        foreach ($fillableFields as $field) {
            
            switch($field){
                case('email'):
                case('phone'):
                    if($master->{$field} != $secondary->{$field} && !empty($secondary->{$field})) {
                        $master->{$field} = implode(', ',[$master->{$field}, $secondary->{$field}]);
                    }
                    break;
                default:
                    if(empty($master->{$field}) && !empty($secondary->{$field})) {
                        $master->{$field} = $secondary->{$field};
                    }
            }

        }

        $secondary->merged_into = $master->id;
        $secondary->status = 'merged';
        $secondary->save();
        $master->save();

        return true;
    }


    public function deactivateContact($id)
    {
        return $this->contactRepo->markInactive($id);
    }
        


}