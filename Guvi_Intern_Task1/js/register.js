$(document).ready(function() {

  // Step 1 → Step 2
  $("#nextBtn").click(function() {
    let name = $("#name").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let confirmPassword = $("#confirmPassword").val().trim();
    let terms = $("#terms").is(":checked");

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

  // Step 2 → Step 1
  $("#backBtn").click(function() {
    $("#step2").hide();
    $("#step1").show();
  });

  // AJAX registration (no form submission)
  $("#registerBtn").click(function() {
    let formData = new FormData();
    formData.append("name", $("#name").val());
    formData.append("email", $("#email").val());
    formData.append("password", $("#password").val());
    formData.append("confirmPassword", $("#confirmPassword").val());
    formData.append("dob", $("#dob").val());
    formData.append("age", $("#age").val());
    formData.append("phone", $("#phone").val());
    formData.append("address", $("#address").val());
    formData.append("gender", $("#gender").val());
    //formData.append("profilePic", $("#profilePic")[0].files[0]);

    $.ajax({
      url: "php/register.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        try {
          let res = JSON.parse(response);
          alert(res.msg);
          if (res.status === "success") {
            $("#registerForm")[0].reset();
            $("#step2").hide();
            $("#step1").show();
          }
        } catch (e) {
          alert("Unexpected response from server!");
        }
      },
      // error: function() {
      //   alert("Error during registration!");
      // }
    });
  });

});
