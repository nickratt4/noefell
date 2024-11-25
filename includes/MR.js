$('#banModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var userId = button.data('user-id');
    var reportLocation = button.data('report-location');

    var modal = $(this);
    modal.find('#ban_user_id').val(userId);
    modal.find('#ban_report_location').val(reportLocation);
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var itemId = button.data('item-id');
    var itemType = button.data('item-type');

    var modal = $(this);
    modal.find('#delete_item_id').val(itemId);
    modal.find('#delete_item_type').val(itemType);
});