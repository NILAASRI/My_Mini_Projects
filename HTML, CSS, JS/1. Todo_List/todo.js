const todoInput = document.querySelector("#todo_input")
const todoAddBtn = document.querySelector(".todo_add_btn")
const body = document.body;

todoAddBtn.addEventListener("click", ()=>{
    const inputValue = todoInput.value
    const todoListUl = document.querySelector(".todo_list")
    const li = document.createElement("li")
    const liInnerHtml = 
    `<li class="todo_list_lis">
    <div>${inputValue}</div>
    <div>
        <button class="done">Done</button> 
        <button class="remove">Remove</button>
    </div>
    </li>`;
li.innerHTML = liInnerHtml;
if(inputValue.trim() === ""){
    alert("Please Add Your Task");
}
else{
    todoListUl.append(li);
}
    
});

body.addEventListener("click", (eventObject)=>{
    if(eventObject.target.classList.contains("done")){
        const todoTask = eventObject.target.parentElement.previousElementSibling;
        todoTask.style.textDecoration = "line-through";
        todoTask.style.color = "hsla(57, 88%, 86%, 0.600)";
    }
    if(eventObject.target.classList.contains("remove")){
        const removeTodoTask = eventObject.target.parentElement.parentElement
        removeTodoTask.remove();
    }
});