
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
