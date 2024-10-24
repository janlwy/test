let formCreate = document.getElementById("formCrud");
let titleInput = document.getElementById("title");
let artistInput = document.getElementById("artist");
let imageInput = document.getElementById("image");
let pathInput = document.getElementById("path");
let indexInput = document.getElementById("hidden");
let msg = document.getElementById("msg");
let formSave = document.getElementById("formsave");



fetchAllMusics(playlist);
// Fonction Recherche
document
    .querySelectorAll("input[type=search]")[0]
    .addEventListener("input", search);

function search() {
    const filteredData = playlist.filter(playlist =>
        playlist.title.toLowerCase().includes(this.value.toLowerCase())
    );
    fetchAllMusics(filteredData);
}

// formulaire CRUD
formCreate.style.display = "none";
const contentForm = document.getElementById("contentMusic");

document.getElementById("form-add").addEventListener("click", function () {
    displayForm();
});

function displayForm() {
    formCreate.style.display = "block";
    contentForm.style.display = "none";
}


function hideForm() {
    formCreate.style.display = "none";
    contentForm.style.display = "block";

    document.getElementById("title").value = "";
    document.getElementById("artist").value = "";
    document.getElementById("image").value = "";
    document.getElementById("path").value = "";
    document.getElementById("hidden").value = "";
}

