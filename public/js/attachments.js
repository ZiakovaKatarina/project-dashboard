document.getElementById('show_form_for_new_attachment').addEventListener('click', function() {
    document.getElementById('form_new_attachment').style.display = 'block';
});

function cancel_adding_attachments() {
    document.getElementById('form_new_attachment').style.display = 'none';
    document.getElementById('input_new_attachment').value = "";
}