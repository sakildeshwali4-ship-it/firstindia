<script>
    var assignmentOptions = {
        live_tv: @json($liveTvs->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->values()),
        video: @json($videos->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        })->values())
    };
    var selectedAssignableId = '{{ $result->assignable_id ?? '' }}';

    function loadAssignableOptions() {
        var type = $('#assignable_type').val();
        var options = assignmentOptions[type] || [];
        var html = '<option value="">Select Item</option>';

        options.forEach(function(item) {
            var selected = selectedAssignableId == item.id ? 'selected' : '';
            html += '<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>';
        });

        $('#assignable_id').html(html);
    }

    $(document).ready(function() {
        loadAssignableOptions();

        $('#assignable_type').change(function() {
            selectedAssignableId = '';
            loadAssignableOptions();
        });
    });
</script>
