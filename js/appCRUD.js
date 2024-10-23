let formCreate = document.getElementById("formCrud");
let titleInput = document.getElementById("title");
let artistInput = document.getElementById("artist");
let imageInput = document.getElementById("image");
let pathInput = document.getElementById("path");
let indexInput = document.getElementById("hidden");
let msg = document.getElementById("msg");
let formSave = document.getElementById("formsave");


formCreate.addEventListener("submit", (event) => {
    event.preventDefault();
    formValidation();
});

let formValidation = () => {
    if (titleInput.value === "" || artistInput.value === "" || imageInput.value === "" || pathInput.value === "") {
        console.log("Échec");
        msg.innerHTML = "Merci de bien compléter tous les champs !";
    } else {
        console.log("Validé");
        msg.innerHTML = "";
        acceptData();
        hideForm();
    }
};

let playlist = [];

let acceptData = () => {
    playlist.push({
        title: titleInput.value,
        artist: artistInput.value,
        image: imageInput.value,
        path: pathInput.value,
    });

    localStorage.setItem("playlist", JSON.stringify(playlist));
};

//
console.table(playlist);

function fetchAllMusics(playlist) {
    // Récupération de l'élement à remplir <tbody>
    const musicTab = document.getElementsByTagName("tbody")[0];
    musicTab.innerHTML = "";

    let tablo = "";
    // Récupération des données depuis Playlist 
        playlist.forEach((music, index) => {
            tablo += `<tr>
                <td>${music.title}</td>
                <td>${music.artist}</td>
                <td hidden>${music.image}</td>
                <td hidden>${music.path}</td>
                <td class="action">
                    <button type="button" class="edit btnBase vert" value="${index}" style="width:45px;"><span class="material-icons">edit</span></button>
                    <button type="button" class="delete btnBase rouge" value="${index}" style="width:45px;"><span class="material-icons">delete_outline</span></button>
                </td>
            </tr>`;
        });

    if (tablo.length > 0) {
        // Affichage des données dans le tableau
        musicTab.innerHTML += tablo;
        // Chaque bouton "Editer"
        document.querySelectorAll("button.edit").forEach(b => {
            b.addEventListener("click", function () {
                return editMusic(this.value);
            });
        });
        // Chaque bouton "Supprimer"
        document.querySelectorAll("button.delete").forEach(b => {
            b.addEventListener("click", function () {
                return deleteMusic(this.value);
            });
        });
    } else {
        // Aucune donnée
        musicTab.innerHTML += "<p> &nbsp; Aucune ligne trouvée</p>";
    }
}

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

document.getElementById("formsave").addEventListener("click", function () {
    // Récupération des champs
    const title = document.getElementById("title").value;
    const artist = document.getElementById("artist").value;
    const image = document.getElementById("image").value;
    const path = document.getElementById("path").value;

    if (title && artist && image && path) {
        // Nouvelle ligne
        const newTrack = { title: title, artist: artist, image: image, path: path };

        // Ajout de la nouvelle ligne
        if (document.getElementById("hidden").value.length > 0) {
            playlist.splice(document.getElementById("hidden").value, 1, newTrack);
        } else {
            playlist.push(newTrack);
        }

        localStorage.setItem("playlist", JSON.stringify(playlist));
        hideForm();
        console.table(playlist);
        // Affichage du nouveau tableau
        return fetchAllMusics(playlist);
    }
});

function hideForm() {
    formCreate.style.display = "none";
    contentForm.style.display = "block";

    document.getElementById("title").value = "";
    document.getElementById("artist").value = "";
    document.getElementById("image").value = "";
    document.getElementById("path").value = "";
    document.getElementById("hidden").value = "";
}

document.getElementById("formcancel").addEventListener("click", function () {
    console.log("annulée");
    msg.innerHTML = "";
    hideForm();
});

function editMusic(index) {
    // Récupération de la ligne via son index
    const music = playlist.find((m, i) => {
        return i == index;
    });

    // Alimentation des champs
    document.getElementById("title").value = music.title;
    document.getElementById("artist").value = music.artist;
    document.getElementById("image").value = music.image;
    document.getElementById("path").value = music.path;
    document.getElementById("hidden").value = index;

    fetchAllMusics(playlist);
    localStorage.setItem("playlist", JSON.stringify(playlist));

    displayForm();
}

function deleteMusic(index) {
    if (confirm("Confirmez-vous la suppression de ce titre ?")) {
        playlist.splice(index, 1);
        fetchAllMusics(playlist);
        localStorage.setItem("playlist", JSON.stringify(playlist));

        console.table(playlist);
    }
}

(() => { 
    //Expression de fonction invoquée immédiatement (IIFE (Immediately invoked function expression))
    playlist = JSON.parse(localStorage.getItem("playlist")) || [];
    fetchAllMusics(playlist);

    console.table(playlist);
})(); 
