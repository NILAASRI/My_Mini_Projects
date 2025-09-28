$(document).ready(function() {
  // Move to 2nd page
  $("#nextBtn").click(function() {
    let name = $("#name").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let confirmPassword = $("#confirmPassword").val().trim();
    let terms = $("#terms").is(":checked");

    // Simple validation for 1st page
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

  // Register button click
  $("#registerBtn").click(function() {
    let formData = new FormData();

    // Append all form inputs
    formData.append("name", $("#name").val().trim());
    formData.append("email", $("#email").val().trim());
    formData.append("password", $("#password").val().trim());
    formData.append("confirmPassword", $("#confirmPassword").val().trim());
    formData.append("dob", $("#dob").val());
    formData.append("age", $("#age").val());
    formData.append("phone", $("#phone").val().trim());
    formData.append("address", $("#address").val().trim());
    formData.append("gender", $("#gender").val());

    // Profile picture file
    let profilePic = $("#profilePic")[0].files[0];
    if (profilePic) {
      formData.append("profilePic", profilePic);
    }

    // AJAX call
    $.ajax({
      url: "register.php", 
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        try {
          let res = typeof response === "string" ? JSON.parse(response) : response;
          if (res.status === "success") {
            alert(res.msg);
            window.location.href = "index.html";
          } else {
            alert(res.msg);
          }
        } catch (e) {
          alert("Unexpected response from server!");
        }
      },
      error: function() {
        alert("Error during registration!");
      }
    });
  });
});
