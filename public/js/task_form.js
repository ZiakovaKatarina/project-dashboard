function edit_user_in_task(user_id) {
    document.getElementById('form_change_user_state_' + user_id).style.display = 'block';
    document.getElementById('saved_state_' + user_id).style.display = 'none';
}

function cancel_editing_user_in_task(user_id) {
    document.getElementById('form_change_user_state_' + user_id).style.display = 'none';
    document.getElementById('saved_state_' + user_id).style.display = 'inline';
}