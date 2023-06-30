window.onload = init;
const venues = []
const searchInput = document.getElementById("search");

function init() {
    document.querySelectorAll("#venue").forEach(function (index) {
        venues.push(index.innerHTML.toLowerCase())
    })
    // console.log(cities)
    searchInput.addEventListener('input', function () {
        const inputValue = searchInput.value;
        venues.forEach(function (element) {
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