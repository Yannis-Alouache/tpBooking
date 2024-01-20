let minInput = document.getElementById('min-input')
let minSlide = document.getElementById('min-slider')
minInput.addEventListener("change", (event)=> {
	minSlide.value = minInput.value;
});

minSlide.addEventListener("input", (event)=> {
	minInput.value = minSlide.value;
});

let maxInput = document.getElementById('max-input')
let maxSlide = document.getElementById('max-slider')
maxInput.addEventListener("change", (event)=> {
	maxSlide.value = maxInput.value;
});

maxSlide.addEventListener("input", (event)=> {
	maxInput.value = maxSlide.value;
});

const datepickerEl = document.getElementById('datepickerId');
new DateRangePicker(datepickerEl, {
	// options
});