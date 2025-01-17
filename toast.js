function alert_toast(message, type) {
    if (type === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000, // Auto-close after 3 seconds
            showConfirmButton: false
        });
    } else if (type === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            timer: 3000, // Auto-close after 3 seconds
            showConfirmButton: false
        });
    }
}
