<div class="card shadow-sm">

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="3%">#</th>
                        <th width="3%">
                            {{-- Checkbox --}}
                        </th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th class="text-center" width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dummy Data -->
                    @forelse($contacts as $contact)
                    <tr>
                        <td>{{($contacts->currentPage() - 1) * $contacts->perPage() + $loop->iteration }}</td>
                        @if(!empty($contact->mergedContact))
                        <td><i class="bi bi-info-circle-fill text-warning" id="showMergeDetail" data-master_id = "{{$contact->id}}" data-merged_contact_id = "{{$contact->mergedContact->id}}" title="This contact has been involved in a merge operation"></i></td>
                        @else
                        <td><input type="checkbox" class="merge-checkbox" value="{{ $contact->id }}"></td>
                        @endif
                        {{-- <td>{{$contact->name}}</td> --}}
                        <td><div class="d-flex align-items-center">
                            <img src="{{$contact->profile_image_url}}"  width="60" class="rounded-circle me-2" alt="Profile">
                            <span>{{$contact->name}}</span>
                        </div></td>
                        <td>{{$contact->email}}</td>
                        <td>{{$contact->phone}}</td>
                        <td>{{$contact->gender}}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1 editBtn" data-id="{{$contact->id}}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary me-1" 
                                data-contact-id="{{ $contact->id }}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addCustomFieldModal" 
                                title="Add Custom Field"
                                id="addCustomFieldBtn"
                            >
                                <i class="bi bi-plus-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-contact"  data-contact_id="{{ $contact->id }}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No contacts found</td></tr>
                    @endforelse
                    
                </tbody>
            </table>

            <button id="mergeButton" class="btn btn-primary m-3 text-md-end" style="display:none;">
                Merge Contacts
            </button>

            <div class=" m-2">
                {!! $contacts->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="mergeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Merge Contacts</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="mergeModalBody">
        <!-- Dynamic content from backend will appear here -->
      </div>
    </div>
  </div>
</div>



<script>
    $(document).ready(function(){
        $('.editBtn').click(function() {
            const id = $(this).data('id');
            $.get(`/contacts/${id}/edit`, function(data) {
                $('#contactModalContent').html(data);
                $('#contactModal').modal('show');
            });
        });

        $(document).on('click', '#addCustomFieldBtn', function () {
            let contactId = $(this).data('contact-id');
            $('#contact_id').val(contactId);
        });
    });


   
    // Merging Contacts Script

    $(function () {
        const $checkboxes = $('.merge-checkbox');
        const $mergeButton = $('#mergeButton');
        const $modal = $('#mergeModal');
        const $modalBody = $('#mergeModalBody');

        // Handle checkbox selection
        $checkboxes.on('change', function () {
            const checked = $('.merge-checkbox:checked');

            if (checked.length === 2) {
                $checkboxes.not(':checked').prop('disabled', true);
                $mergeButton.show();
            } else {
                $checkboxes.prop('disabled', false);
                $mergeButton.hide();
            }
        });

        // Fetch preview content
        $mergeButton.on('click', function () {
            const selectedIds = $('.merge-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            $.ajax({
                url: "{{ route('contacts.merge_preview') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ids: selectedIds
                },
                beforeSend: function () {
                    $modalBody.html('<div class="text-center p-5">Loading...</div>');
                    $modal.modal('show');
                },
                success: function (response) {
                    $modalBody.html(response);
                },
                error: function (xhr) {
                    $modalBody.html('<div class="text-danger p-4">Failed to load merge data.</div>');
                }
            });
        });



        // Handle final merge submission (delegated since content is dynamic)
        $(document).off('submit', '#mergeConfirmForm').on('submit', '#mergeConfirmForm', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('contacts.merge_contacts') }}",
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('#mergeConfirmForm button[type="submit"]').prop('disabled', true).text('Merging...');
                },
                success: function (response) {
                    showToast('success', response.message || 'Contact merged successfully!');
                    $modal.modal('hide');
                    $('.merge-checkbox').prop('checked', false).prop('disabled', false);
                    $mergeButton.hide();
                    // Refresh the contact list
                    loadContacts();
                },
                error: function (xhr) {
                    showToast('error', xhr.responseJSON?.message || 'Something went wrong.');
                    $('#mergeConfirmForm button[type="submit"]').prop('disabled', false).text('Confirm Merge');
                }
            });
        });


        // Show the Merge Log

        $(document).on('click', '#showMergeDetail', function() {
            let masterId = $(this).data('master_id');
            let mergedContactId = $(this).data('merged_contact_id');

            $.ajax({
                url: "{{ route('contacts.merge_log') }}",
                type: 'POST',
                data: {
                    master_id: masterId,
                    merged_contact_id: mergedContactId
                },
                beforeSend: function () {
                    $('#mergeModalBody').html('<div class="text-center p-5">Loading...</div>');
                    $('#mergeModal').modal('show');
                },
                success: function (response) {
                    $('#mergeModalBody').html(response);
                },
                error: function (xhr) {
                    $('#mergeModalBody').html('<div class="text-danger p-4">Failed to load merge log.</div>');
                }
            });
        });



        // Delete The Contact
        $(document).on('click', '.delete-contact', function(e) {
            e.preventDefault();

            let contactId = $(this).data('contact_id');

            if (confirm("Are you sure you want to delete this contact?")) {
                $.ajax({
                    url: "{{ route('contacts.deactivate', ':id') }}".replace(':id', contactId),
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message || 'Contact merged successfully!');
                            loadContacts();
                        } else {
                            showToast('error', response.message || 'Something went wrong.');
                            loadContacts();
                        }
                    },
                    error: function(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Something went wrong.');
                    }
                });
            }
        });


    });




    



    

</script>


