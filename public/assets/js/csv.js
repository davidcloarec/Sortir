window.onload = init;
const fileInput = document.getElementById('user_participant_csv_submitFile');
const reader = new FileReader()
const output = document.getElementById('output');
let data = []
function init() {
    fileInput.addEventListener('change', function(){
        let reader = new FileReader();

        reader.onload = function (e) {
            data = scsvToArray(e.target.result);
            removeAllChildNodes(output);
            displayTable(output,data);
        };

        reader.readAsText(fileInput.files[0]);
    });
}




/**
 *
 * @param scsv semi-colon separated values | We use the complete file as a string
 * @returns {*[]} returns an array of arrays, each array inside should contain the result of one line of the csv
 */
function scsvToArray(scsv) {
    // Convert the string to an array, each array entry represents one line
    let lines = scsv.split('\n');
    let result = [];

    for (let i = 0; i < lines.length; i++) {
        // Split data
        let row = lines[i].split(';');
        // Push the resulting array into the main array
        result.push(row);
    }
    // Return the result
    return result;
}

function removeAllChildNodes(parent) {
    while (parent.firstChild!=null) {
        parent.removeChild(parent.firstChild);
    }
}

/**
 *
 * @param node parent node to add the table
 * @param data an array of data to display
 */
function displayTable(node,data) {
    let div = document.createElement("div");
    div.className = "text-center";
    let h1 = document.createElement("h3")
    h1.textContent = "Prévisualisation des utilisateurs"
    div.appendChild(h1)
    let table = document.createElement('table');
    table.id = 'display';
    table.className = 'table table-bordered table-striped table-hover m-0';
    let thead = document.createElement("thead");
    let titles = [
        "Email Utilisateur",
        "Mot de passe",
        "Pseudo",
        "Nom",
        "Prénom",
        "Téléphone",
        "Email Participant",
        "Administrateur",
        "Actif",
        "Campus",
    ]
    titles.forEach(title=>{
        let th = document.createElement("th");
        th.textContent = title;
        th.scope = "col";
        thead.appendChild(th)
    });
    table.appendChild(thead)
    let tbody = document.createElement("tbody")
    // création des <td>
    data.forEach(function(array) {
        let row = document.createElement('tr');
        array.forEach(function(item){
            if(item!==""){
                let cell = document.createElement('td');
                cell.textContent = item;
                row.appendChild(cell);
            }
        });
        if(row.firstChild){
            tbody.appendChild(row);
        }
    });
    table.appendChild(tbody);
    div.appendChild(table);
    node.appendChild(div);
}