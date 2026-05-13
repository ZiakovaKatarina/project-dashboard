document.getElementById('show_form_for_new_comment').addEventListener('click', function() {
    document.getElementById('form_new_comment').style.display = 'block';
});

async function add_comment(task_id) {
    const content = document.getElementById('new_comment_content').value;
    
    if (content.trim().length === 0) {
        alert("Obsah komentáru nesmie byť prázdny.");
        return;
    }

    if (content.trim().length > 500) {
        alert("Obsah komentáru nemôže byť dlhší ako 500 znakov.");
        return;
    }

    var response = await fetch('?c=comment&a=add',
        {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                comment_content: content,
                task_id: task_id
            })
        }
    );
    var data = await response.json();
    if (data.error) {
        alert(data.error);
    } else if (data) {
        var new_content = `<tr id="comment-row-${data.comment_id}">`;
        new_content += `<td>${data.comment_id}</td>`;
        new_content += `<td>${data.user_id || ''}</td>`;
        new_content += `<td>${data.task_id}</td>`;
        new_content += `<td id="comment-content-${data.comment_id}">${data.content}</td>`;
        new_content += `<td>${data.creation}</td>`;
        new_content += `<td><button onclick="edit_comment(${data.comment_id})">Upraviť</button></td>`;
        new_content += `<td><button onclick="delete_comment(${data.comment_id})">Zmazať</button></td>`;
        new_content += `</tr>`;
        document.getElementById('comments-list').innerHTML += new_content;
        cancel_adding_comment();
    }
}

function edit_comment(commentId) {
    const cell = document.getElementById('comment-content-' + commentId);
    const old_value = cell.innerText;
    cell.innerHTML = `
        <textarea id="edit-content-${commentId}">${old_value}</textarea>
        <button onclick="save_edits(${commentId})">Uložiť</button>
        <button onclick="cancel_editing_comment(${commentId}, '${old_value}')">Zrušiť</button>
    `;
}

function cancel_editing_comment(commentId, content) {
    const cell = document.getElementById('comment-content-' + commentId);
    cell.innerText = content;
}

async function save_edits(commentId) {
    const cell = document.getElementById('edit-content-' + commentId);
    const new_value = cell.value;

    if (new_value.trim().length === 0) {
        alert("Obsah komentáru nesmie byť prázdny.");
        return;
    }

    if (new_value.trim().length > 500) {
        alert("Obsah komentáru nemôže byť dlhší ako 500 znakov.");
        return;
    }


    var response = await fetch('?c=comment&a=edit',
        {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                commentId: commentId,
                commentContent: new_value
            })
        }
    );
    var data = await response.json();
    if (data.error) {
        alert(data.error);
    } else if (data) {
        document.getElementById("comment-content-" + commentId).innerText = data.content;
    }
}

async function delete_comment(commentId) {
    var response = await fetch('?c=comment&a=delete',
        {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                comment_id: commentId
            })
        }
    );
    var data = await response.json();
    if (data.success) {
        document.getElementById("comment-row-" + commentId).remove();
    }
}

function cancel_adding_comment() {
    document.getElementById('new_comment_content').value = '';
    document.getElementById('form_new_comment').style.display = 'none';
}