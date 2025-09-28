$(document).ready(function() {
  // Move to 2nd page
  $("#nextBtn").click(function() {
    let name = $("#name").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let confirmPassword = $("#confirmPassword").val().trim();
    let terms = $("#terms").is(":checked");

    //validation for 1st page
    if (!name || !email || !password || !confirmPassword) {
      alert("Please fill all fields.");
      return;
    }
    if (password !== confirmPassword) {
      alert("Passwords do not match.");
      return;
    }
    if (!terms) {
      alert("Please accept terms & conditions.");
      return;
    }

    $("#step1").hide();
    $("#step2").show();
  });

  // Back to 1st page
  $("#backBtn").click(function() {
    $("#step2").hide();
    $("#step1").show();
  });

  $("#registerForm").submit(function(e) {
    e.preventDefault(); 
    let formData = new FormData(this);

    $.ajax({
      url: "php/register.php", 
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        alert(response);
      },
      error: function() {
        alert("Error during registration!");
      }
    });
  });

});
