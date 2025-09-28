$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault(); // This means Prevent form submission

        let email = $('#loginEmail').val().trim();
        let password = $('#loginPassword').val().trim();
        let remember = $('#rememberMe').is(':checked') ? 1 : 0;

        // AJAX request
        $.ajax({
            url: 'php/login.php',
            method: 'POST',
            dataType: 'json',
            data: { email, password, remember },
            success: function(response) {
                if(response.status === 'success') {
                    // Save session info to localStorage
                    localStorage.setItem('userToken', response.token);
                    localStorage.setItem('userEmail', email);

                    alert('Login successful!');
                    window.location.href = 'profile.html'; // redirect
                } else {
                    alert(response.message);
                }
            },
            error: function(error) {
                console.error(error);
                alert('Something went wrong!');
            }
        });
    });
});
