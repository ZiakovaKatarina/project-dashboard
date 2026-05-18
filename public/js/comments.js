document.getElementById('show_form_for_new_comment').addEventListener('click', function() {
    document.getElementById('form_new_comment').style.display = 'flex';
});

async function add_comment(task_id, project_id) {
    const content = document.getElementById('new_comment_content').value;
    
    if (content.trim().length === 0) {
        alert("Obsah komentáru nesmie byť prázdny.");
        return;
    }

    if (content.trim().length > 500) {
        alert("Obsah komentáru nemôže byť dlhší ako 500 znakov.");
        return;
    }

    var response = await fetch('?c=comment&a=add&project=' + project_id + '&task=' + task_id,
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
        new_content += `<td>${data.username || ''}</td>`;
        new_content += `<td id="comment-content-${data.comment_id}"></td>`;
        new_content += `<td>${data.creation}</td>`;
        new_content += `<td><button onclick="edit_comment(${data.comment_id}, ${task_id}, ${project_id})">Upraviť</button>
                        <button onclick="delete_comment(${data.comment_id}, ${task_id}, ${project_id})">Zmazať</button></td>`;
        new_content += `</tr>`;
        document.getElementById('comments-list').innerHTML += new_content;
        document.getElementById('comment-content-' + data.comment_id).textContent = data.content;
        cancel_adding_comment();
    }
}

function edit_comment(commentId, taskId, projectId) {
    const cell = document.getElementById('comment-content-' + commentId);
    const old_value = cell.textContent;
    const save_value = encodeURIComponent(old_value);
    cell.innerHTML = `
        <div class="one-row-comment">
        <textarea id="edit-content-${commentId}"></textarea>
        <button onclick="save_edits(${commentId}, ${taskId}, ${projectId})">Uložiť</button>
        <button onclick="cancel_editing_comment(${commentId}, '${save_value}')">Zrušiť</button>
        </div>
    `;
    document.getElementById("edit-content-" + commentId).value = old_value;
}

function cancel_editing_comment(commentId, save_value) {
    const cell = document.getElementById('comment-content-' + commentId);
    cell.textContent = decodeURIComponent(save_value);
}

async function save_edits(commentId, taskId, projectId) {
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


    var response = await fetch('?c=comment&a=edit&project=' + projectId + '&task=' + taskId,
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
        document.getElementById("comment-content-" + commentId).textContent = data.content;
    }
}

async function delete_comment(commentId, taskId, projectId) {
    var response = await fetch('?c=comment&a=delete&project=' + projectId + '&task=' + taskId,
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