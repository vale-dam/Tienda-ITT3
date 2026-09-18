const buscador = document.getElementById("busqueda");
const filtroCarrera = document.getElementById("filtro-carrera");
const filtroCategoria = document.getElementById("filtro-categoria");

const productos = document.querySelectorAll("section article");


function filtrarProductos() {

    const texto = buscador.value.toLowerCase().trim();
    const carrera = filtroCarrera.value.toLowerCase();
    const categoria = filtroCategoria.value.toLowerCase();


    productos.forEach(function(producto) {

        const contenido = producto.textContent.toLowerCase();


        const coincideBusqueda = contenido.includes(texto);

        const coincideCarrera =
            carrera === "" || contenido.includes(carrera);

        const coincideCategoria =
            categoria === "" || contenido.includes(categoria);


        if (
            coincideBusqueda &&
            coincideCarrera &&
            coincideCategoria
        ) {

            producto.style.display = "";

        } else {

            producto.style.display = "none";

        }

    });

}


buscador.addEventListener("input", filtrarProductos);

filtroCarrera.addEventListener("change", filtrarProductos);

filtroCategoria.addEventListener("change", filtrarProductos);