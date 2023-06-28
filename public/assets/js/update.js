window.onload = init;

function init(){
    document.getElementById('city').addEventListener('change',setVenue)
    document.getElementById('activity_venue').addEventListener('change',setCoord)
}

function setVenue(){
    let city = document.getElementById('city').value
    let postalHTML = document.getElementById('postal');
    let postal = document.getElementById(city + '_postal').innerHTML;
    postalHTML.innerHTML = postal;
    let first = true;

    let venueSelect = document.getElementById('activity_venue');
    venueSelect.childNodes.forEach(option => {
        console.log(city + '_' + option.value + '_street')
        if(document.getElementById(city + '_' + option.value + '_street') != null){
            option.hidden = false;
            if(first) {
                venueSelect.value = option.value
                setCoord();
                first = false;
            }
        }
        else option.hidden = true;
    })

}

function setCoord(){
    let venue = document.getElementById('activity_venue').value
    let city = document.getElementById('city').value;
    let streetHTML = document.getElementById('street');
    let street = document.getElementById(city + '_' + venue + '_street').innerHTML;
    streetHTML.innerHTML = street;

    let latitudeHTML = document.getElementById('latitude');
    let latitude = document.getElementById(city + '_' + venue + '_latitude').innerHTML;
    latitudeHTML.innerHTML = latitude;

    let longitudeHTML = document.getElementById('longitude');
    let longitude = document.getElementById(city + '_' + venue + '_longitude').innerHTML;
    longitudeHTML.innerHTML = longitude;

}