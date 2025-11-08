<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Models\Contact;
use App\Services\CustomFieldService;

class ContactController extends Controller
{

    protected $service;
    protected $customFieldService;

    public function __construct(ContactService $service, CustomFieldService $customFieldService)
    {
        $this->service = $service;
        $this->customFieldService = $customFieldService;
    }


    public function index(){
        return view('contacts.index');
    }


    public function list(Request $request){
        $request->merge(['only_active' => true]);
        $contacts = $this->service->getAllContacts($request->all());        
        return view('contacts._list', compact('contacts'));
    }

     public function create()
    {
        
        return view('contacts._form', ['contact' => new Contact()]);
    }

    public function store(Request $request)
    {
        try{
            $validated = $this->service->validateRequestData($request);
            $this->service->createContact($validated);

            return response()->json(['success' => true, 'message' => 'Contact added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error adding contact: '.$e->getMessage()]);
        }

    }


    public function edit(Contact $contact)
    {
        $contact = $this->service->findContactById($contact->id);
        return view('contacts._form', compact('contact'));
    }


    public function update(Request $request)
    {
        try {
        
            $validated = $this->service->validateRequestData($request);
            $this->service->updateContact($request->id, $validated);

            if($request->has('custom_fields')){
                foreach($request->custom_fields as $fieldId => $fieldValue){
                    $this->customFieldService->assignValuesToContacts($fieldId, $request->id, [
                        'field_value' => $fieldValue
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Contact updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating contact: '.$e->getMessage()]);
        }

    }

    
    public function mergePreview(Request $request)
    {
        try{
            $contactIds = $request->input('ids', []);
            $contacts = $this->service->getAllContacts(['ids' => $contactIds]);

            return view('contacts._merge_preview', compact('contacts'));
        } catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'Error fetching merge preview: '.$e->getMessage()]);
        }
    }


    public function mergeContacts(Request $request)
    {
        try{

            $masterId = $request->input('master_id');
            $contactIds = explode(',', $request->input('contact_ids', ''));
            $secondaryId = current(array_diff($contactIds, [$masterId]));
    
            $master = $this->service->findContactById($masterId);
            $secondary = $this->service->findContactById($secondaryId);
    
            $this->service->mergeContacts($master, $secondary);
            $this->customFieldService->mergeCustomFieldValues($master, $secondary);
    
            return response()->json(['success' => true, 'message' => 'Contacts merged successfully.']);
        } catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'Error merging contacts: '.$e->getMessage()]);
        }
        
    }

    public function deactivate($id)
    {
        try{
            $this->service->deactivateContact($id);
            return response()->json(['success' => true, 'message' => 'Contact deleted successfully.']);
        } catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'Error deleting contact: '.$e->getMessage()]);
        }
    }

    public function showMergeLog(Request $request)
    {
        try{
            $masterId = $request->input('master_id');
            $mergedContactId = $request->input('merged_contact_id');

            $contacts = $this->service->getAllContacts(['ids' => [$masterId, $mergedContactId]]);

            return view('contacts._merge_history', compact('contacts'));
        } catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'Error fetching merge log: '.$e->getMessage()]);
        }
    }
      


}
