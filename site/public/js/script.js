// clear browser cache as sometimes web browsers tends to cache even if we empty cache.
const reload = document.getElementById('reload');

reload.addEventListener('click', () => {
    window.location.reload(true);
});

window.onload = (event) => {
    console.log("page is fully loaded");
};
