let taskForms = document.getElementsByClassName('taskForm');
let loading = document.getElementById('loading');
let tasks = document.getElementById("tasks");

const taskItemDom = (id, title, content, createdAt, updatedAt) => `
    <div class="content-item" id="item`+id+`">
        <span>
            Title:
            <strong> `+title+` </strong>
        </span>
        <span>
            Body:
            <strong> `+content+` </strong>
        </span>
        <span>
            Created at:
            <strong> `+createdAt+` </strong>
        </span>
        <span>
            Updated at:
            <strong> `+updatedAt+` </strong>
        </span>
        <span>
            <button class="btn mr-5 btn-modal" data-target="editTask`+id+`">Edit</button>
            <form class="taskForm">
                <input type="hidden" name="id" value="`+id+`">
                    <input type="hidden" name="type" value="deleteTask">
                    <button type="submit" class="btn btn-danger mr-5">
                        Delete
                    </button>
            </form>
        </span>
    </div>

    <div id="editTask`+id+`" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <h4>Update task</h4>
            <form class="taskForm" method="POST">
                <input type="hidden" name="id" value="`+id+`">
                <input type="hidden" name="type" value="updateTask">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter title" required value="`+title+`">
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea rows="5" class="form-control" name="content" id="content" placeholder="Enter content" required>`+content+`</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Update</button>
                </div>
                <div class="form-group">
                    <button class="btn cancel" data-target="editTask`+id+`"> Cancel </button>
                </div>
            </form>
        </div>
    </div>
`;


document.body.onsubmit = function(event) {
    event.preventDefault();
    const target = event.target;
    closeALlModals();
    if (target.className && target.className.indexOf('taskForm') !== -1) {
        loading.classList.remove('d-none');
        let formData = new FormData();
        for( let j=0; j < target.elements.length; j++ ){
            const attribute = target.elements[j];
            formData.append(
                encodeURIComponent(attribute.name),
                encodeURIComponent(attribute.value)
            );
        }
        const type = formData.get('type');
        const id = formData.get('id');
        if (type === 'deleteTask'){
            if (!confirm("Do you want to delete this task?")) {
                loading.classList.add('d-none');
                return 0;
            }
        }

        fetch(
            'tasks.php',
            {
                method: 'POST',
                body: formData
            }
        )
            .then(response => {
                response.json().then(function(data) {
                    document.getElementById('message').classList.remove("d-none");
                    const messageText = document.getElementById('messageText');
                    messageText.classList.add( data.status ? "text-success" : "text-danger");
                    messageText.innerHTML = data.message;

                    if (type === 'deleteTask'){
                        const task = document.getElementById("item"+id);
                        const editTask = document.getElementById("editTask"+id);
                        task.parentNode.removeChild(editTask);
                        task.parentNode.removeChild(task);
                    }
                    if (type === 'updateTask'){
                        const task = document.getElementById("item"+id);
                        const editTask = document.getElementById("editTask"+id);
                        task.parentNode.removeChild(editTask);
                        task.parentNode.removeChild(task);
                        tasks.insertAdjacentHTML('afterbegin', taskItemDom(data.task.id, data.task.title, data.task.body, data.task.createdAt, data.task.updatedAt));
                    }
                    if (type === 'addTask'){
                        tasks.insertAdjacentHTML('afterbegin', taskItemDom(data.task.id, data.task.title, data.task.body, data.task.createdAt, data.task.updatedAt));
                        const noTask = document.getElementById("noTask");
                        if (noTask){
                            noTask.classList.add('d-none');
                        }
                    }
                });
                loading.classList.add('d-none');
                taskForms = document.getElementsByClassName('taskForm');
                tasks = document.getElementById("tasks");
            })
            .catch((e) => {
                console.log(e);
                loading.classList.add('d-none');
            });
    }
}