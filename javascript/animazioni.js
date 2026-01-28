const animazione = document.querySelector(".animazione");
const stopBtn = document.querySelector("#stop");
const startBtn = document.querySelector("#start");

stopBtn.addEventListener("click", () => {
  animazione.style.animationPlayState = "paused";
});

startBtn.addEventListener("click", () => {
  animazione.style.animationPlayState = "running";
});