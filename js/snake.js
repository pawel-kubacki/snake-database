const snakeWrapper = document.querySelector('#snakeWrapper');
const area = document.querySelector('#area');
const snakeHead = document.querySelector('#snakeHead');
const snakeBody = [document.querySelectorAll('.snakeBody')];
const apple = document.querySelector('#apple')
const start = document.querySelector('#start');
const scoreBox = document.querySelector('#scoreBox');
const info = document.querySelector('#info');
const pauza = document.querySelector('#pauza');
const enterToStart = document.querySelector('#enterToStart');
const historyWrapper = document.querySelector('#historyWrapper');
const history = document.querySelector('#historyContent');
const setNameWrapper = document.querySelector('#setNameWrapper');
const setNameLabel = document.querySelector('.setNameLabel');
const logo = document.querySelector('.logo');
const gameOverDiv = document.querySelector('#gameOver');
const saveButton = document.querySelector('#saveButton');
const levelDiv = document.querySelector('#level');
const saveLevel = document.querySelector('#saveLevel');
let topWay = [];
let leftWay = [];
let step = 15;
let score = 0;
let number = 0;
let play = "off";
let interval = '';
let level = '';
let convertedLevel = '';
let vector = 38;
let virtualScore = 0;
const player = {
	playerName: "",
	playerScore: "",
	playerLevel: ""
};

const gameBeginning = () => {
	if (!JSON.parse(localStorage.getItem('playersScores'))) {
		localStorage.setItem("playersScores", JSON.stringify([player]));
	} else if (JSON.parse(localStorage.getItem('playersScores'))) {
		let getPlayersScoresArray = JSON.parse(localStorage.getItem('playersScores'))
		for (i = 0; i < getPlayersScoresArray.length; i++) {
			if (getPlayersScoresArray[i].playerName === "" && getPlayersScoresArray[i].playerScore === "" && getPlayersScoresArray[i].playerLevel === "") {
				// localStorage.removeItem("playersScores" == getPlayersScoresArray[i]);
			} else if (getPlayersScoresArray[i].playerScore > 0 || getPlayersScoresArray[i].playerScore < Infinity) {
				history.innerHTML += "<p>" + "<span>" + "Imię gracza: " + "</span>" + "<span>" + "<strong>" + `${getPlayersScoresArray[i].playerName}` + "</strong>" + "." + "</span>" + "<span>" + "Punkty: " + "<strong>" + ` ${getPlayersScoresArray[i].playerScore}` + "</strong>" + "." + "</span>" + "<span>" + "Poziom: " + "<strong>" + `${getPlayersScoresArray[i].playerLevel}` + "</strong>" + "." + "</span>" + "</p>"
			}
		}
	}
	for (i = 0; i < 3; i++) {
		const el = document.createElement("div");
		el.classList.add("snakeBody");
		snakeBody.push(el);
		area.appendChild(el, snakeHead);
	}
	createNewApple();
}

const createNewApple = () => {
	if (!apple) {
		const el = document.createElement("div");
		el.id = "apple";
		el.style.backgroundColor = `#${Math.floor(Math.random() * 16777215).toString(16)}`;
		area.appendChild(el);
		el.style.top = Math.floor(Math.random() * ((area.clientHeight - el.clientHeight - 10) - (area.clientHeight - area.clientHeight)) + (area.clientHeight - area.clientHeight)) + "px";
		el.style.left = Math.floor(Math.random() * ((area.clientWidth - el.clientHeight - 10) - (area.clientWidth - area.clientWidth)) + (area.clientWidth - area.clientWidth)) + "px";
	}
}

const increaseSnake = () => {
	const el = document.createElement("div");
	el.classList.add("snakeBody");
	snakeBody.push(el)
	area.appendChild(el, snakeHead);
	el.style.top = snakeHead.offsetTop + "px";
	el.style.left = snakeHead.offsetLeft + "px";
	createNewApple();
}

const catchedApples = () => {
	let apple = document.querySelector('#apple');
	let snakePosition1 = snakeHead.offsetLeft + (snakeHead.clientWidth / 2);
	let snakePosition2 = snakeHead.offsetTop + (snakeHead.clientHeight / 2);
	let applePosition1 = apple.offsetLeft;
	let applePosition2 = apple.offsetLeft + apple.clientWidth;
	let applePosition3 = apple.offsetTop;
	let applePosition4 = apple.offsetTop + apple.clientHeight;
	if (snakePosition1 > applePosition1
		&& snakePosition1 < applePosition2
		&& snakePosition2 > applePosition3
		&& snakePosition2 < applePosition4) {
		apple.remove();
		increaseSnake();
		score += 1;
		scoreBox.innerHTML = "Punkty: " + "<strong>" + score + "</strong>";
	} else {
		checkGameOver();
	}
}

const checkGameOver = () => {
	if ((snakeHead.offsetTop < (snakeHead.clientHeight / 2 - 0.5) - snakeHead.clientHeight)
		|| (snakeHead.offsetLeft < (snakeHead.clientHeight / 2 - 0.5) - snakeHead.clientHeight)
		|| snakeHead.offsetTop > area.clientHeight
		|| snakeHead.offsetLeft > area.clientWidth) {
		gameOver();
	}
	for (i = 1; i < snakeBody.length; i++) {
		let snakePosition1 = snakeHead.offsetLeft + (snakeHead.clientWidth / 2);
		let snakePosition2 = snakeHead.offsetTop + (snakeHead.clientHeight / 2);
		let snakeChildPosition1 = snakeBody[i].offsetLeft;
		let snakeChildPosition2 = snakeBody[i].offsetLeft + snakeBody[i].clientWidth;
		let snakeChildPosition3 = snakeBody[i].offsetTop;
		let snakeChildPosition4 = snakeBody[i].offsetTop + snakeBody[i].clientHeight;
		if (snakePosition1 > snakeChildPosition1
			&& snakePosition1 < snakeChildPosition2
			&& snakePosition2 > snakeChildPosition3
			&& snakePosition2 < snakeChildPosition4) {
			gameOver();
		}
	}
}

const gameOver = () => {
	clearInterval(interval);
	gameOverDiv.style.left = area.offsetLeft + 5 + "px";
	gameOverDiv.style.visibility = "visible";
	info.style.boxShadow = '10px 10px 8px 10px #f02929';
	info.style.color = '#f02929';
	scoreBox.style.boxShadow = '10px 10px 8px 10px #f02929';
	scoreBox.style.color = '#f02929';
	area.style.boxShadow = '0 0 0 0';
	area.style.background = "#f02929";
	document.querySelector('body').style.background = "#464545";
	setNameWrapper.style.opacity = "1";
	historyWrapper.style.opacity = "1";
	history.style.opacity = "1";
}

const save = () => {
	sessionStorage.setItem("getName", setNameValue);
	sessionStorage.setItem("getScores", score);
	sessionStorage.setItem("getlevel", convertedLevel);
	saveScores();
}

const saveScores = (e) => {
	document.querySelector('#inputScores').value = sessionStorage.getItem("getScores");
	document.querySelector('#inputLevel').value = sessionStorage.getItem("getlevel");
	player.playerName = sessionStorage.getItem("getName");
	player.playerScore = sessionStorage.getItem("getScores");
	player.playerLevel = sessionStorage.getItem("getlevel");
	let playersScoresArray = JSON.parse(localStorage.getItem('playersScores'))
	playersScoresArray.push(player);
	localStorage.setItem("playersScores", JSON.stringify(playersScoresArray));
	location.reload();
}

const converted = () => {
	switch (level) {
		case 500: convertedLevel = "B.Ł."; break;
		case 400: convertedLevel = "Ł"; break;
		case 300: convertedLevel = "Ś"; break;
		case 200: convertedLevel = "D.T."; break;
		case 100: convertedLevel = "T"; break;
		case 50: convertedLevel = "B.T."; break;
		case 20: convertedLevel = "P.N.W. :)"; break;
		case 10: convertedLevel = "N.W. :)"; break;
	}
}

const changeClass = () => {
	info.style.boxShadow = '0px 0px 0px 0px';
	scoreBox.style.boxShadow = '0px 0px 0px 0px';
	area.style.boxShadow = '0 0 15px 30px #C0C0C0';
	setNameWrapper.style.opacity = "0";
	historyWrapper.style.opacity = "0";
}

function mainFunction(play) {
	play === "on"
		? ((interval = setInterval(
			() => {
				topWay.push(snakeHead.style.top);
				leftWay.push(snakeHead.style.left);
				if (vector === 37) {
					snakeHead.style.left = snakeHead.offsetLeft - step + "px";
				} else if (vector === 38) {
					snakeHead.style.top = snakeHead.offsetTop - step + "px";
				} else if (vector === 39) {
					snakeHead.style.left = snakeHead.offsetLeft + step + "px";
				} else if (vector === 40) {
					snakeHead.style.top = snakeHead.offsetTop + step + "px";
				}
				snakeBody[1 + number].style.top = `${topWay[0]}`;
				snakeBody[1 + number].style.left = `${leftWay[0]}`;
				topWay.shift();
				leftWay.shift();
				number += 1;
				if (number === snakeBody.length - 1) { number = 0 };
				catchedApples();
			}, level)))
		: (clearInterval(interval));
};

const hoverYes = () => {
	logo.classList.remove("logo");
	logo.classList.add("logoHover");
}

const hoverNo = () => {
	logo.classList.remove("logoHover");
	logo.classList.add("logo");
}

const setLevel = () => {
	let selectValue = document.querySelector('#kindOfLevel');
	switch (selectValue.value) {
		case "Bardzo łatwy": level = 500; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Łatwy": level = 400; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Średni": level = 300; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Dość trudny": level = 200; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Trudny": level = 100; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Bardzo trudny": level = 50; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Prawie nie wykonalny :)": level = 20; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
		case "Nie wykonalny :)": level = 10;; changeClass(); start.style.visibility = "hidden"; levelDiv.style.left = "-300px"; converted(); break;
	}
}

const addVirtualScore = () => {
	if (virtualScore === 9999) { virtualScore = 0 };
	scoreBox.innerHTML = "<strong>" + virtualScore + " :)" + "</strong>";
	virtualScore += 1;
}

const removeVirtualScore = () => {
	scoreBox.innerHTML = "Punkty: " + "<strong>" + score + "</strong>";
	virtualScore = 0;
}



window.addEventListener("keydown", (event) => {
	let x = event.keyCode || event.keyCode;
	switch (event.keyCode) {
		case 38: if (vector !== 38 && vector !== 40) { vector = x }; break;
		case 40: if (vector !== 40 && vector !== 38) { vector = x }; break;
		case 37: if (vector !== 37 && vector !== 39) { vector = x }; break;
		case 39: if (vector !== 39 && vector !== 37) { vector = x }; break;
		case 13: if (gameOverDiv.style.visibility === "visible") { save() } else if (play === "off" && start.style.visibility === "hidden") { play = "on"; pauza.style.display = 'none'; mainFunction(play) }; break;
		case 32: if (play === "on" && start.style.visibility === "hidden") { play = "off"; pauza.style.display = 'inline'; mainFunction(play) }; break;
	}
})
window.addEventListener("load", gameBeginning);
start.addEventListener("click", () => { levelDiv.style.left = area.offsetLeft - 5 + "px" });
setNameWrapper.addEventListener('mouseenter', hoverYes);
setNameWrapper.addEventListener('mouseleave', hoverNo);
saveButton.addEventListener('submit', save);
saveLevel.addEventListener('click', setLevel);
scoreBox.addEventListener('mousemove', addVirtualScore);
scoreBox.addEventListener('mouseleave', removeVirtualScore);