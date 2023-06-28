window.onload = init;

function init(){
    document.getElementById('isOrganized').addEventListener('click',setList);
    document.getElementById('isRegistred').addEventListener('click',setList);
    document.getElementById('isNotRegistred').addEventListener('click',setList);
    document.getElementById('past').addEventListener('click',setList);
}

function setList(){

    document.getElementsByName('organizer').forEach(name=>{
        name.closest("tr").hidden = true;
    })

    let isOrganized = document.getElementById('isOrganized').checked;
    let isRegistred = document.getElementById('isRegistred').checked;
    let isNotRegistred = document.getElementById('isNotRegistred').checked;
    let isPast = document.getElementById('past').checked;

    if(isOrganized) organized();
    if(isRegistred) registered();
    if(isNotRegistred) notRegistered();
    if(isPast) past();

    if(!isOrganized && !isRegistred && !isNotRegistred && !isPast){
        setAll();
    }

}


function organized(){
    let username = document.getElementById('username').innerHTML;
    document.getElementsByName('organizer').forEach(name=>{
        if(name.innerHTML === username) name.closest("tr").hidden = false;
    })
}

function registered(){
    document.getElementsByName('register').forEach(register =>{
        if(register.innerHTML.includes('x')) register.closest('tr').hidden = false;
    })
}

function notRegistered(){
    document.getElementsByName('register').forEach(register =>{
        if(!register.innerHTML.includes('x')) register.closest('tr').hidden = false;
    })
}

function past(){
    document.getElementsByName('state').forEach(state =>{
        if(state.innerHTML.includes('passé')) state.closest('tr').hidden = false;
    })
}

function setAll(){
    document.querySelectorAll('tr').forEach(tr=>{
        tr.hidden = false;
    })
}