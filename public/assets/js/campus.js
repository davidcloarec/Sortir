window.onload = init;
const cities = []
const searchInput = document.getElementById("search");

function init() {
    document.querySelectorAll("#campus").forEach(function (index) {
        cities.push(index.innerHTML.toLowerCase())
    })
    // console.log(cities)
    searchInput.addEventListener('input', function () {
        const inputValue = searchInput.value;
        cities.forEach(function (element) {
            if (element.toLowerCase().includes(inputValue)) {
                // console.log("Table row :  " + element + " - Input value : " + inputValue);
                document.getElementById(element).hidden = false;
            } else {
                if (!document.getElementById(element).hidden) {
                    document.getElementById(element).hidden = true;
                }
            }
        })
    });

}