document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.querySelector(".search-bar button");
    const resultsContainer = document.getElementById("searchResults");

    searchInput.addEventListener("input", function() {
        buscarEnTiempoReal();
    });

    searchInput.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            redirigirResultados();
        }
    });

    searchButton.addEventListener("click", function() {
        redirigirResultados();
    });

    function buscarEnTiempoReal() {
        let query = searchInput.value.trim();
        if (query.length < 2) {
            resultsContainer.innerHTML = "";
            resultsContainer.style.display = "none";
            return;
        }

        fetch(`/buscar/general?q=${query}`)
            .then(response => response.json())
            .then(data => {
                console.log("Resultados obtenidos:", data);
                resultsContainer.innerHTML = "";
                
                if (data.error) {
                    resultsContainer.innerHTML = `<p>${data.error}</p>`;
                    resultsContainer.style.display = "block";
                    return;
                }

                let html = "";

                if (data.canciones.length > 0) {
                    html += "<h3>🎵 Canciones</h3><ul>";
                    data.canciones.forEach(cancion => {
                        let titulo = cancion.titulo ?? 'Título desconocido';
                        let autor = cancion.autor ?? 'Autor desconocido';
                        let id = cancion.id ?? null;
                    
                        if (id) {
                            html += `<li><a href="/cancion/${id}">${titulo} - ${autor}</a></li>`;
                        } else {
                            console.warn("Canción sin ID detectada:", cancion);
                        }
                    });
                    
                    
                    html += "</ul>";
                }

                if (data.playlists.length > 0) {
                    html += "<h3>📂 Playlists</h3><ul>";
                    data.playlists.forEach(playlist => {
                        html += `<li><a href="/playlist/${playlist.id}">${playlist.nombre}</a></li>`;
                    });
                    html += "</ul>";
                }

                resultsContainer.innerHTML = html || "<p>No se encontraron resultados.</p>";
                resultsContainer.style.display = "block";
            })
            .catch(error => {
                console.error("Error en la búsqueda:", error);
                resultsContainer.innerHTML = "<p>Error en la búsqueda.</p>";
                resultsContainer.style.display = "block";
            });
    }

    function redirigirResultados() {
        let query = searchInput.value.trim();
        if (query.length > 1) {
            window.location.href = `/buscar/resultados?q=${encodeURIComponent(query)}`;
        }
    }
});
