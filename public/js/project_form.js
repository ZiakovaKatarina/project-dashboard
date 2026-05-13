function edit_user_in_project(user_id) {
    document.getElementById('form_change_user_rights_' + user_id).style.display = 'block';
    document.getElementById('saved_right_' + user_id).style.display = 'none';
}

function cancel_editing_user_in_project(user_id) {
    document.getElementById('form_change_user_rights_' + user_id).style.display = 'none';
    document.getElementById('saved_right_' + user_id).style.display = 'inline';
}