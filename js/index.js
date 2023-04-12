const now = new Date();
const welcome = document.querySelector("#welcome");
const logo = document.querySelector(".logo");

const myTimer = setInterval(function () {
    const h1 = document.querySelector("h1");
    const zerofill = value => (value < 10 && value > -1 ? '0' : "") + value;
    let dzisiaj = new Date();
    let dzien = dzisiaj.getDate();
    let miesiac = dzisiaj.getMonth() + 1;
    let rok = dzisiaj.getFullYear();
    let godzina = dzisiaj.getHours();
    let minuty = dzisiaj.getMinutes();
    let sekunda = dzisiaj.getSeconds();
    h1.innerHTML = zerofill(dzien) + "/" + zerofill(miesiac) + "/" + rok + " | " + zerofill(godzina) + ":" + zerofill(minuty) + ":" + zerofill(sekunda);

}, 1000);

const hoverYes = () => {
    logo.classList.remove("logo");
    logo.classList.add("logoHover");
}

const hoverNo = () => {
    logo.classList.remove("logoHover");
    logo.classList.add("logo");
}

welcome.addEventListener('mouseenter', hoverYes);
welcome.addEventListener('mouseleave', hoverNo);