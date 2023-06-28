window.onload = init;
const imageInput = document.getElementById("participant_image");
function init(){
    imageInput.addEventListener('change', function() {
        let input = this;
        let url = input.value;
        let ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

        if (input.files && input.files[0] && (ext === "gif" || ext === "png" || ext === "jpeg" || ext === "jpg")) {
            let reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById('profile-picture').src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            // document.getElementById('img').src = '/assets/no_preview.png';
        }
    });
}