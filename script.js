const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})







const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})





if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})



const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}



})



// script.js
function alert_toast(message, type) {
    // Create toast div
    var toast = document.createElement('div');
    toast.classList.add('toast');
    
    // Set message and background color based on type
    toast.textContent = message;
    if (type === 'success') {
        toast.style.backgroundColor = 'green';  // Green background for success
    } else if (type === 'error') {
        toast.style.backgroundColor = 'red';  // Red background for error
    }
    
    // Append the toast to the container
    document.getElementById('toast-container').appendChild(toast);

    // Display the toast and remove it after a delay
    setTimeout(function() {
        toast.style.opacity = 1;
        setTimeout(function() {
            toast.style.opacity = 0;
            setTimeout(function() {
                toast.remove(); // Remove the toast element
            }, 300); // Delay before removal
        }, 3000); // Keep the toast visible for 3 seconds
    }, 100);
}
