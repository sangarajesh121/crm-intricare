<?php

namespace App\Http\Controllers;
use App\Services\CustomFieldService;

use Illuminate\Http\Request;

class CustomfieldController extends Controller
{

    protected $service;

    public function __construct(CustomFieldService $service)
    {
        $this->service = $service;
    }
    
    
    public function store(Request $request)
    {
       $validated = $this->service->validateCustomFieldData($request);

       $customField = $this->service->createCustomField($validated);
       $this->service->assignValuesToContacts($customField, $validated['contact_id'], $request);

       return response()->json(['success' => true, 'message' => 'Custom field added successfully.']);
    }


    public function update(Request $request)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
