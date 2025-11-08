<!-- Modal -->
<div class="modal fade" id="addCustomFieldModal" tabindex="-1" aria-labelledby="addCustomFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomFieldModalLabel">Add Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customFieldForm">
                    @csrf
                    <input type="hidden" name="contact_id" id="contact_id">

                    <!-- Field Type -->
                    <div class="mb-3">
                        <label for="field_type" class="form-label">Field Type</label>
                        <select class="form-select" name="field_type" id="field_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="file">File</option>
                        </select>
                    </div>

                    <!-- Label Name -->
                    <div class="mb-3">
                        <label for="field_name" class="form-label">Label Name</label>
                        <input type="text" class="form-control" id="field_name" name="field_name" placeholder="Enter label (e.g. Company Name)" required>
                    </div>

                    <!-- Field Name (readonly) -->
                    <div class="mb-3">
                        <label for="field_key" class="form-label">Field Name</label>
                        <input type="text" class="form-control" id="field_key" name="field_key" readonly>
                    </div>

                    <!-- Field Value -->
                    <div class="mb-3" id="field_value_container">
                        <label for="field_value" class="form-label">Field Value</label>
                        <input type="text" class="form-control" id="field_value" name="field_value" placeholder="Enter value">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS Section -->
@push('scripts')
<script>
$(document).ready(function() {

    // Generate field_name automatically from label_name
    $('#field_name').on('input', function() {
        let label = $(this).val();
        let fieldName = label.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
        $('#field_key').val(fieldName);
    });

    // Change input type dynamically based on field_type
    $('#field_type').on('change', function() {
        let type = $(this).val();
        let container = $('#field_value_container');
        container.empty();

        if(type === 'file') {
            container.append(`
                <label for="field_value" class="form-label">Field Value</label>
                <input type="file" class="form-control" id="field_value" name="field_value">
            `);
        } else if(type === 'date') {
            container.append(`
                <label for="field_value" class="form-label">Field Value</label>
                <input type="date" class="form-control" id="field_value" name="field_value">
            `);
        } else if(type === 'number') {
            container.append(`
                <label for="field_value" class="form-label">Field Value</label>
                <input type="number" class="form-control" id="field_value" name="field_value" placeholder="Enter number">
            `);
        } else {
            container.append(`
                <label for="field_value" class="form-label">Field Value</label>
                <input type="text" class="form-control" id="field_value" name="field_value" placeholder="Enter value">
            `);
        }
    });

    // Handle form submission via AJAX
    $('#customFieldForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('custom_field.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showToast('success', response.message || 'Custom Field saved successfully!');
                $('#addCustomFieldModal').modal('hide');
                $('#customFieldForm')[0].reset(); // Reset form fields
                loadContacts(); // refresh list dynamically
            },
            error: function (xhr) {
                console.error(xhr);
                showToast('error', xhr.responseJSON?.message || 'Something went wrong.');
            }
        });
    });
});
</script>
@endpush
